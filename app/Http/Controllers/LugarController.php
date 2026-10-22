<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLugarRequest;
use App\Http\Requests\UpdateLugarRequest;
use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Resena;
use App\Models\Favorito;
use App\Concerns\ManagesImages;
use App\Services\CloudinaryImageService;
use App\Services\MapaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    use ManagesImages;

    public function __construct(
        private MapaService $mapaService
    ) {}

    public function index(Request $request)
    {
        $lugares = Lugar::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->ubicacion($request->query('departamento'), $request->query('municipio'))
            ->orderBy('nombre')
            ->get();

        $favoritosIds = $this->obtenerFavoritosIds(auth()->id(), 'lugar');

        return view('lugares.index', compact('lugares', 'favoritosIds'));
    }

    public function create()
    {
        return view('lugares.create');
    }

    public function store(StoreLugarRequest $request)
    {
        $validado = $request->validated();

        if ($request->hasFile('imagen')) {
            $validado['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'lugares');
        }

        $lugar = (new Lugar());
        $lugar->fill($validado);
        $lugar->user_id = auth()->id();
        $lugar->save();

        // Guardar imágenes adicionales
        if ($request->hasFile('imagenes')) {
            $this->guardarImagenesGaleria($lugar, $request, 'galeria/lugares');
        }

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar creado exitosamente.');
    }

    public function show(Lugar $lugar)
    {
        $lugar->load('imagenes', 'resenas.user');
        $esFav = auth()->check()
            && !auth()->user()->esAdmin()
            && Favorito::where('user_id', auth()->id())->where('tipo', 'lugar')->where('referencia_id', $lugar->id)->exists();

        return view('lugares.show', compact('lugar', 'esFav'));
    }

    public function edit(Lugar $lugar)
    {
        return view('lugares.edit', compact('lugar'));
    }

    public function update(UpdateLugarRequest $request, Lugar $lugar)
    {
        $validado = $request->validated();
        $imagenPrincipalAnterior = $lugar->imagen;
        $nuevaImagenPrincipal = null;

        // IDs de imágenes de galería a eliminar (solo las de este lugar)
        $idsEliminar = $validado['eliminar_imagenes'] ?? [];

        // 1. Subir nueva imagen principal primero (fuera de la transacción)
        if ($request->hasFile('imagen')) {
            $nuevaImagenPrincipal = $this->subirImagenPrincipal($request->file('imagen'), 'lugares');
        }

        // 2. Subir nuevas imágenes de galería primero (fuera de la transacción)
        $nuevasImagenesGaleria = [];
        if ($request->hasFile('imagenes')) {
            $servicioCloudinary = app(CloudinaryImageService::class);
            foreach ($request->file('imagenes') as $archivo) {
                $secureUrl = $servicioCloudinary->subir($archivo->getRealPath(), 'galeria/lugares');
                $nuevasImagenesGaleria[] = $secureUrl;
            }
        }

        try {
            // 3. Transacción: actualizar lugar y borrar imágenes de galería solicitadas
            $rutasViejasGaleria = [];
            DB::transaction(function () use ($lugar, $validado, $nuevaImagenPrincipal, $nuevasImagenesGaleria, $idsEliminar, &$rutasViejasGaleria, &$imagenPrincipalAnterior) {
                // Capturar rutas viejas solo de las que se eliminarán
                $rutasViejasGaleria = $lugar->imagenes()->whereIn('id', $idsEliminar)->pluck('ruta')->toArray();
                $imagenPrincipalAnterior = $lugar->imagen;

                $datos = $validado;
                unset($datos['imagen'], $datos['imagenes'], $datos['eliminar_imagenes']);

                if ($nuevaImagenPrincipal !== null) {
                    $datos['imagen'] = $nuevaImagenPrincipal;
                }

                $lugar->update($datos);

                // Borrar solo las filas de galería solicitadas, y solo si pertenecen a este lugar
                if (! empty($idsEliminar)) {
                    $lugar->imagenes()->whereIn('id', $idsEliminar)->delete();
                }

                // Insertar nuevas imágenes de galería (solo si se subieron nuevas)
                if (! empty($nuevasImagenesGaleria)) {
                    $ordenMax = $lugar->imagenes()->max('orden') ?? 0;
                    $orden = $ordenMax;
                    foreach ($nuevasImagenesGaleria as $ruta) {
                        $orden++;
                        $lugar->imagenes()->create([
                            'ruta' => $ruta,
                            'orden' => $orden,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            // Si la transacción falló, borrar las imágenes recién subidas para no dejar huérfanos
            if ($nuevaImagenPrincipal !== null) {
                try { $this->eliminarImagen($nuevaImagenPrincipal); } catch (\Throwable $e2) {}
            }
            foreach ($nuevasImagenesGaleria as $ruta) {
                try { $this->eliminarImagen($ruta); } catch (\Throwable $e2) {}
            }
            throw $e;
        }

        // 4. Después del commit: borrar archivos de Cloudinary que ya no se usan
        try {
            if ($imagenPrincipalAnterior && $nuevaImagenPrincipal !== null) {
                $this->eliminarImagen($imagenPrincipalAnterior);
            }
            foreach ($rutasViejasGaleria as $ruta) {
                $this->eliminarImagen($ruta);
            }
        } catch (\Throwable $e) {
            Log::warning('lugar_update_cloudinary_borrado_fallo', [
                'lugar_id' => $lugar->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar actualizado exitosamente.');
    }
    public function destroy(Lugar $lugar)
    {
        // Eliminar imagen principal
        $this->eliminarImagen($lugar->imagen);

        // Eliminar imágenes de galería
        $this->eliminarTodasImagenes($lugar->imagenes);

        // Limpiar registros huérfanos
        Resena::where('tipo', 'lugar')->where('referencia_id', $lugar->id)->delete();
        Favorito::where('tipo', 'lugar')->where('referencia_id', $lugar->id)->delete();

        $lugar->delete();

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar eliminado exitosamente.');
    }

    public function mapa()
    {
        $lugaresMapa = $this->mapaService->obtenerTodosLosPuntos();

        return view('mapa', compact('lugaresMapa'));
    }
}





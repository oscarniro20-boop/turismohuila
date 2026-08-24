<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Agencia;
use App\Models\Favorito;
use App\Models\Resena;
use App\Concerns\ManagesImages;
use Illuminate\Http\Request;

class TourController extends Controller
{
    use ManagesImages;

    public function index(Request $request)
    {
        $tours = Tour::with('paquetes')
            ->withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->when($request->filled('municipio'), function ($query) use ($request) {
                $query->where('municipio', $request->query('municipio'));
            })
            ->orderBy('nombre')
            ->get();

        $municipios = config('municipios.huila');

        $favoritosIds = $this->obtenerFavoritosIds(auth()->id(), 'tour');

        return view('tours.index', compact('tours', 'municipios', 'favoritosIds'));
    }

    public function create()
    {
        $agencias = Agencia::orderBy('nombre')->get();

        return view('tours.create', compact('agencias'));
    }

    public function store(Request $request)
    {
        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'municipio'    => 'required|string|max:255',
            'precio'       => 'required|numeric|min:0',
            'duracion_dias'=> 'required|integer|min:1',
            'incluye'      => 'required|string',
            'cupo_maximo'  => 'required|integer|min:1',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'precio', 'duracion_dias', 'incluye',
            'cupo_maximo',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'tours');
        }

        $tour = Tour::create($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($tour, $request, 'galeria/tours');

        // Fuente oficial de verdad: tour_paquetes (una fila por agencia seleccionada)
        foreach ($request->input('agencias', []) as $agenciaId) {
            \App\Models\TourPaquete::updateOrCreate(
                ['agencia_id' => $agenciaId, 'tour_id' => $tour->id],
                ['precio' => $request->input("precios.$agenciaId")]
            );
        }

        return redirect()->route('tours.index')
                         ->with('success', 'Tour creado correctamente.');
    }

    public function show(Tour $tour)
    {
        $tour->load('paquetes.agencia', 'imagenes', 'resenas');
        $esFav = auth()->check()
            && !auth()->user()->esAdmin()
            && Favorito::where('user_id', auth()->id())->where('tipo', 'tour')->where('referencia_id', $tour->id)->exists();

        return view('tours.show', compact('tour', 'esFav'));
    }

    public function edit(Tour $tour)
    {
        $tour->load('paquetes.agencia');
        $agencias = Agencia::orderBy('nombre')->get();

        return view('tours.edit', compact('tour', 'agencias'));
    }

    public function update(Request $request, Tour $tour)
    {
        if ($request->has('eliminar_imagenes')) {
            $eliminadas = $this->eliminarImagenesGaleriaSeleccionadas($tour, $request);

            return back()->with($eliminadas ? 'success' : 'error', $eliminadas ? 'Imágenes eliminadas correctamente.' : 'No se seleccionaron imágenes válidas.');
        }

        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'required|string',
            'municipio'     => 'required|string|max:255',
            'precio'        => 'required|numeric|min:0',
            'duracion_dias' => 'required|integer|min:1',
            'incluye'       => 'required|string',
            'cupo_maximo'   => 'required|integer|min:1',
            'imagen'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'agencias'                 => 'nullable|array',
            'agencias.*'               => 'integer|exists:agencias,id',
            'paquetes.*.precio'        => 'nullable|numeric|min:0',
            'paquetes.*.duracion_dias' => 'nullable|integer|min:1',
            'paquetes.*.cupo_maximo'   => 'nullable|integer|min:1',
            'paquetes.*.descripcion'   => 'nullable|string',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'precio', 'duracion_dias', 'incluye',
            'cupo_maximo',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            $this->eliminarImagen($tour->imagen);
            
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'tours');
        }

        $tour->update($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($tour, $request, 'galeria/tours');

        $agenciaIds = $request->input('agencias', []);

        foreach ($agenciaIds as $agenciaId) {
            $p = $request->input("paquetes.$agenciaId", []);
            \App\Models\TourPaquete::updateOrCreate(
                ['agencia_id' => $agenciaId, 'tour_id' => $tour->id],
                [
                    'precio'        => $p['precio'] ?? null,
                    'duracion_dias' => $p['duracion_dias'] ?? null,
                    'cupo_maximo'   => $p['cupo_maximo'] ?? null,
                    'descripcion'   => $p['descripcion'] ?? null,
                ]
            );
        }

        \App\Models\TourPaquete::where('tour_id', $tour->id)
            ->whereNotIn('agencia_id', $agenciaIds)
            ->delete();

        return redirect()->route('tours.index')
                         ->with('success', 'Tour actualizado correctamente.');
    }

    public function destroy(Tour $tour)
    {
        // Eliminar imagen principal
        $this->eliminarImagen($tour->imagen);
        
        // Eliminar imágenes de galería
        $this->eliminarTodasImagenes($tour->imagenes);

        // Limpiar registros huérfanos
        Resena::where('tipo', 'tour')->where('referencia_id', $tour->id)->delete();
        Favorito::where('tipo', 'tour')->where('referencia_id', $tour->id)->delete();
        $tour->delete();

        return redirect()->route('tours.index')
                         ->with('success', 'Tour eliminado correctamente.');
    }
}

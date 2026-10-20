<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\LugarImagen;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;
use App\Models\Resena;
use App\Models\Favorito;
use App\Concerns\ManagesImages;
use App\Rules\MunicipioDeDepartamento;
use App\Services\MapaService;
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

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'       => 'required|string|max:255',
            'departamento' => 'required|string|in:'.implode(',', \App\Support\Colombia::departamentos()),
            'municipio'    => ['required', 'string', 'max:255', new MunicipioDeDepartamento],
            'categoria'    => 'required|string|max:100',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'descripcion'  => 'required|string',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $datos['user_id'] = auth()->id();
        $datos['departamento'] = $datos['departamento'] ?? 'Huila';

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'lugares');
        }

        $lugar = Lugar::create($datos);

        // Guardar imágenes adicionales
        $this->guardarImagenesGaleria($lugar, $request, 'galeria/lugares');

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

    public function update(Request $request, Lugar $lugar)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'departamento' => 'required|string|in:'.implode(',', \App\Support\Colombia::departamentos()),
            'municipio'    => ['required', 'string', 'max:255', new MunicipioDeDepartamento],
            'categoria'    => 'required|string|max:100',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'descripcion'  => 'required|string',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

                        $datos = $request->except('imagen', 'user_id');

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            $this->eliminarImagen($lugar->imagen);
            
            $datos['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'lugares');
        }

        $lugar->update($datos);

        // Actualizar imágenes adicionales si se enviaron
        if ($request->hasFile('imagenes')) {
            // Eliminar imágenes anteriores
            $lugar->imagenes()->delete();
            
            // Guardar nuevas imágenes
            $this->guardarImagenesGaleria($lugar, $request, 'galeria/lugares');
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

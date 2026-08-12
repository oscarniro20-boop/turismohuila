<?php

namespace App\Http\Controllers;

use App\Models\Restaurante;
use App\Concerns\ManagesImages;
use Illuminate\Http\Request;

class RestauranteController extends Controller
{
    use ManagesImages;

    public function index(Request $request)
    {
        $restaurantes = Restaurante::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->when($request->filled('municipio'), function ($query) use ($request) {
                $query->where('municipio', $request->query('municipio'));
            })
            ->orderBy('nombre')
            ->get();

        $municipios = config('municipios.huila');

        $favoritosIds = $this->obtenerFavoritosIds(auth()->id(), 'restaurante');

        return view('restaurantes.index', compact('restaurantes', 'municipios', 'favoritosIds'));
    }

    public function create()
    {
        return view('restaurantes.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'required|string',
            'municipio'       => 'required|string|max:255',
            'tipo_cocina'     => 'required|string|max:255',
            'precio_promedio' => 'required|numeric|min:0',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono'        => 'nullable|string|max:20',
            'direccion'       => 'nullable|string|max:255',
            'latitud'          => 'nullable|numeric|between:-90,90',
            'longitud'         => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'tipo_cocina', 'precio_promedio',
            'telefono', 'direccion',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'restaurantes');
        }

        $restaurante = Restaurante::create($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($restaurante, $request, 'galeria/restaurantes');

        return redirect()->route('restaurantes.index')
                         ->with('success', 'Restaurante creado correctamente.');
    }

    public function show(Restaurante $restaurante)
    {
        $restaurante->load('imagenes', 'resenas');
        return view('restaurantes.show', compact('restaurante'));
    }

    public function edit(Restaurante $restaurante)
    {
        return view('restaurantes.edit', compact('restaurante'));
    }

    public function update(Request $request, Restaurante $restaurante)
    {
        if ($request->has('eliminar_imagenes')) {
            $eliminadas = $this->eliminarImagenesGaleriaSeleccionadas($restaurante, $request);

            return back()->with($eliminadas ? 'success' : 'error', $eliminadas ? 'Imágenes eliminadas correctamente.' : 'No se seleccionaron imágenes válidas.');
        }

        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'required|string',
            'municipio'       => 'required|string|max:255',
            'tipo_cocina'     => 'required|string|max:255',
            'precio_promedio' => 'required|numeric|min:0',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono'        => 'nullable|string|max:20',
            'direccion'       => 'nullable|string|max:255',
            'latitud'          => 'nullable|numeric|between:-90,90',
            'longitud'         => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'tipo_cocina', 'precio_promedio',
            'telefono', 'direccion',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            $this->eliminarImagen($restaurante->imagen);
            
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'restaurantes');
        }

        $restaurante->update($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($restaurante, $request, 'galeria/restaurantes');

        return redirect()->route('restaurantes.index')
                         ->with('success', 'Restaurante actualizado correctamente.');
    }

    public function destroy(Restaurante $restaurante)
    {
        // Eliminar imagen principal
        $this->eliminarImagen($restaurante->imagen);
        
        // Eliminar imágenes de galería
        $this->eliminarTodasImagenes($restaurante->imagenes);

        $restaurante->delete();

        return redirect()->route('restaurantes.index')
                         ->with('success', 'Restaurante eliminado correctamente.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Resena;
use App\Models\Favorito;
use App\Concerns\ManagesImages;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    use ManagesImages;

    public function index(Request $request)
    {
        $hoteles = Hotel::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->when($request->filled('municipio'), function ($query) use ($request) {
                $query->where('municipio', $request->query('municipio'));
            })
            ->orderBy('nombre')
            ->get();

        $municipios = config('municipios.huila');

        $favoritosIds = $this->obtenerFavoritosIds(auth()->id(), 'hotel');

        return view('hoteles.index', compact('hoteles', 'municipios', 'favoritosIds'));
    }

    public function create()
    {
        return view('hoteles.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'municipio'    => 'required|string|max:255',
            'precio_noche' => 'required|numeric|min:0',
            'estrellas'    => 'required|integer|min:1|max:5',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono'     => 'nullable|string|max:20',
            'direccion'    => 'nullable|string|max:255',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'precio_noche', 'estrellas',
            'telefono', 'direccion',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'hoteles');
        }

        $hotel = Hotel::create($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($hotel, $request, 'galeria/hoteles');

        return redirect()->route('hoteles.index')
                         ->with('success', 'Hotel creado correctamente.');
    }

    public function show(Hotel $hotel)
    {
        $hotel->load('imagenes', 'resenas');
        $esFav = auth()->check()
            && !auth()->user()->esAdmin()
            && Favorito::where('user_id', auth()->id())->where('tipo', 'hotel')->where('referencia_id', $hotel->id)->exists();

        return view('hoteles.show', compact('hotel', 'esFav'));
    }

    public function edit(Hotel $hotel)
    {
        return view('hoteles.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        if ($request->has('eliminar_imagenes')) {
            $eliminadas = $this->eliminarImagenesGaleriaSeleccionadas($hotel, $request);

            return back()->with($eliminadas ? 'success' : 'error', $eliminadas ? 'Imágenes eliminadas correctamente.' : 'No se seleccionaron imágenes válidas.');
        }

        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'municipio'    => 'required|string|max:255',
            'precio_noche' => 'required|numeric|min:0',
            'estrellas'    => 'required|integer|min:1|max:5',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono'     => 'nullable|string|max:20',
            'direccion'    => 'nullable|string|max:255',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'precio_noche', 'estrellas',
            'telefono', 'direccion',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            $this->eliminarImagen($hotel->imagen);
            
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'hoteles');
        }

        $hotel->update($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($hotel, $request, 'galeria/hoteles');

        return redirect()->route('hoteles.index')
                         ->with('success', 'Hotel actualizado correctamente.');
    }

    public function destroy(Hotel $hotel)
    {
        // Eliminar imagen principal
        $this->eliminarImagen($hotel->imagen);
        
        // Eliminar imágenes de galería
        $this->eliminarTodasImagenes($hotel->imagenes);

        // Limpiar registros huérfanos
        Resena::where('tipo', 'hotel')->where('referencia_id', $hotel->id)->delete();
        Favorito::where('tipo', 'hotel')->where('referencia_id', $hotel->id)->delete();
        $hotel->delete();

        return redirect()->route('hoteles.index')
                         ->with('success', 'Hotel eliminado correctamente.');
    }
}

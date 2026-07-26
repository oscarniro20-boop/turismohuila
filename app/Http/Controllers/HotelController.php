<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
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

        return view('hoteles.index', compact('hoteles', 'municipios'));
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
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'telefono'     => 'nullable|string|max:20',
            'direccion'    => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'precio_noche', 'estrellas',
            'telefono', 'direccion'
        ]);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $nombre = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('imagenes/hoteles'), $nombre);
            $data['imagen'] = 'imagenes/hoteles/' . $nombre;
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
        return view('hoteles.show', compact('hotel'));
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
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'telefono'     => 'nullable|string|max:20',
            'direccion'    => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio',
            'precio_noche', 'estrellas',
            'telefono', 'direccion'
        ]);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $nombre = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('imagenes/hoteles'), $nombre);
            $data['imagen'] = 'imagenes/hoteles/' . $nombre;
        }

        $hotel->update($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($hotel, $request, 'galeria/hoteles');

        return redirect()->route('hoteles.index')
                         ->with('success', 'Hotel actualizado correctamente.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return redirect()->route('hoteles.index')
                         ->with('success', 'Hotel eliminado correctamente.');
    }
}

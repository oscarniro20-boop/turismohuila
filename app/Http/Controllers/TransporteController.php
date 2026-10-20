<?php

namespace App\Http\Controllers;

use App\Models\Transporte;
use App\Models\Resena;
use App\Models\Favorito;
use App\Concerns\ManagesImages;
use Illuminate\Http\Request;

class TransporteController extends Controller
{
    use ManagesImages;

    public function index(Request $request)
    {
        $transportes = Transporte::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->ubicacion($request->query('departamento'), $request->query('municipio'))
            ->orderBy('nombre')
            ->get();

        $favoritosIds = $this->obtenerFavoritosIds(auth()->id(), 'transporte');

        return view('transportes.index', compact('transportes', 'favoritosIds'));
    }

    public function create()
    {
        return view('transportes.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'municipio'   => 'required|string|max:255',
            'tipo'        => 'required|string|max:100',
            'precio'      => 'nullable|numeric|min:0',
            'capacidad'   => 'nullable|integer|min:1',
            'imagen'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono'    => 'nullable|string|max:20',
            'latitud'     => 'nullable|numeric|between:-90,90',
            'longitud'    => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio', 'tipo',
            'precio', 'capacidad', 'telefono',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'transportes');
        }

        $transporte = Transporte::create($data);

        $this->guardarImagenesGaleria($transporte, $request, 'galeria/transportes');

        return redirect()->route('transportes.index')
                         ->with('success', 'Transporte creado correctamente.');
    }

    public function show(Transporte $transporte)
    {
        $transporte->load(['imagenes', 'resenas.user']);
        $esFav = auth()->check()
            && !auth()->user()->esAdmin()
            && Favorito::where('user_id', auth()->id())->where('tipo', 'transporte')->where('referencia_id', $transporte->id)->exists();

        return view('transportes.show', compact('transporte', 'esFav'));
    }

    public function edit(Transporte $transporte)
    {
        return view('transportes.edit', compact('transporte'));
    }

    public function update(Request $request, Transporte $transporte)
    {
        if ($request->has('eliminar_imagenes')) {
            $eliminadas = $this->eliminarImagenesGaleriaSeleccionadas($transporte, $request);

            return back()->with($eliminadas ? 'success' : 'error', $eliminadas ? 'Imágenes eliminadas correctamente.' : 'No se seleccionaron imágenes válidas.');
        }

        $request->validate($this->reglasGaleria());
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'municipio'   => 'required|string|max:255',
            'tipo'        => 'required|string|max:100',
            'precio'      => 'nullable|numeric|min:0',
            'capacidad'   => 'nullable|integer|min:1',
            'imagen'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono'    => 'nullable|string|max:20',
            'latitud'     => 'nullable|numeric|between:-90,90',
            'longitud'    => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre', 'descripcion', 'municipio', 'tipo',
            'precio', 'capacidad', 'telefono',
            'latitud', 'longitud',
        ]);

        if ($request->hasFile('imagen')) {
            $this->eliminarImagen($transporte->imagen);
            $data['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'transportes');
        }

        $transporte->update($data);

        $this->guardarImagenesGaleria($transporte, $request, 'galeria/transportes');

        return redirect()->route('transportes.index')
                         ->with('success', 'Transporte actualizado correctamente.');
    }

    public function destroy(Transporte $transporte)
    {
        $this->eliminarImagen($transporte->imagen);
        $this->eliminarTodasImagenes($transporte->imagenes);

        Resena::where('tipo', 'transporte')->where('referencia_id', $transporte->id)->delete();
        Favorito::where('tipo', 'transporte')->where('referencia_id', $transporte->id)->delete();
        $transporte->delete();

        return redirect()->route('transportes.index')
                         ->with('success', 'Transporte eliminado correctamente.');
    }
}

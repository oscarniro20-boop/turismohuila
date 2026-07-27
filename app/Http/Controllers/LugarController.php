<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\LugarImagen;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    public function index(Request $request)
    {
        $lugares = Lugar::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->when($request->filled('municipio'), function ($query) use ($request) {
                $query->where('municipio', $request->query('municipio'));
            })
            ->orderBy('nombre')
            ->get();

        $municipios = config('municipios.huila');

        return view('lugares.index', compact('lugares', 'municipios'));
    }

    public function create()
    {
        return view('lugares.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'municipio'    => 'required|string|max:255',
            'categoria'    => 'required|string|max:100',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'descripcion'  => 'required|string',
            'calificacion' => 'required|numeric|min:1|max:5',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $datos = $request->all();
        $datos['user_id'] = auth()->id();

        if ($request->hasFile('imagen')) {
            $nombre = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('imagenes/lugares'), $nombre);
            $datos['imagen'] = 'imagenes/lugares/' . $nombre;
        }

        $lugar = Lugar::create($datos);

        // Guardar imágenes adicionales
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $nombre = time() . '_' . $index . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('imagenes/lugares'), $nombre);
                
                LugarImagen::create([
                    'lugar_id' => $lugar->id,
                    'ruta' => 'imagenes/lugares/' . $nombre,
                    'orden' => $index,
                ]);
            }
        }

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar creado exitosamente.');
    }

    public function show(Lugar $lugar)
    {
        $lugar->load('imagenes', 'resenas');
        return view('lugares.show', compact('lugar'));
    }

    public function edit(Lugar $lugar)
    {
        if (!auth()->user()->esAdmin() && $lugar->user_id !== auth()->id()) {
            abort(403, 'No puedes editar un lugar que no creaste.');
        }
        return view('lugares.edit', compact('lugar'));
    }

    public function update(Request $request, Lugar $lugar)
    {
        if (!auth()->user()->esAdmin() && $lugar->user_id !== auth()->id()) {
            abort(403, 'No puedes editar un lugar que no creaste.');
        }

        $request->validate([
            'nombre'       => 'required|string|max:255',
            'municipio'    => 'required|string|max:255',
            'categoria'    => 'required|string|max:100',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'descripcion'  => 'required|string',
            'calificacion' => 'required|numeric|min:1|max:5',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $datos = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $nombre = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('imagenes/lugares'), $nombre);
            $datos['imagen'] = 'imagenes/lugares/' . $nombre;
        }

        $lugar->update($datos);

        // Actualizar imágenes adicionales si se enviaron
        if ($request->hasFile('imagenes')) {
            // Eliminar imágenes anteriores
            $lugar->imagenes()->delete();
            
            // Guardar nuevas imágenes
            foreach ($request->file('imagenes') as $index => $imagen) {
                $nombre = time() . '_' . $index . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('imagenes/lugares'), $nombre);
                
                LugarImagen::create([
                    'lugar_id' => $lugar->id,
                    'ruta' => 'imagenes/lugares/' . $nombre,
                    'orden' => $index,
                ]);
            }
        }

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar actualizado exitosamente.');
    }

    public function destroy(Lugar $lugar)
    {
        if (!auth()->user()->esAdmin() && $lugar->user_id !== auth()->id()) {
            abort(403, 'No puedes eliminar un lugar que no creaste.');
        }

        $lugar->delete();

        return redirect()->route('lugares.index')
            ->with('success', 'Lugar eliminado exitosamente.');
    }

    public function mapa()
    {
        $lugares = Lugar::whereNotNull('latitud')->whereNotNull('longitud')->get();
        return view('mapa', compact('lugares'));
    }
}

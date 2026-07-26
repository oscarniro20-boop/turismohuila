<?php

namespace App\Http\Controllers;

use App\Models\Guia;
use App\Models\Lugar;
use Illuminate\Http\Request;

class GuiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guias = Guia::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->get()
            ->each(function ($guia) {
                $guia->total_resenas = $guia->resenas_count;
            });
        return view('guias.index', compact('guias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lugares = Lugar::orderBy('nombre')->get();
        return view('guias.create', compact('lugares'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bio' => 'nullable|string|max:1000',
            'idiomas' => 'nullable|string|max:255',
            'experiencia_anios' => 'nullable|integer|min:0',
            'zonas' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'lugares' => 'nullable|array',
            'lugares.*' => 'exists:lugares,id',
        ]);

        $datos = $request->all();

        if ($request->hasFile('foto')) {
            $nombre = time() . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('imagenes/guias'), $nombre);
            $datos['foto'] = 'imagenes/guias/' . $nombre;
        }

        $guia = Guia::create($datos);

        // Asociar lugares seleccionados
        if ($request->has('lugares')) {
            $guia->lugares()->sync($request->lugares);
        }

        // Procesar múltiples imágenes de galería
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $ruta = $imagen->store('galeria/guias', 'public');
                $guia->imagenes()->create([
                    'ruta' => $ruta,
                    'orden' => $index,
                ]);
            }
        }

        return redirect()->route('guias.index')
            ->with('success', 'Guía turístico creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guia $guia)
    {
        $guia->load('lugares', 'imagenes', 'resenas');
        return view('guias.show', compact('guia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guia $guia)
    {
        $guia->load('lugares');
        $lugares = Lugar::orderBy('nombre')->get();
        return view('guias.edit', compact('guia', 'lugares'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guia $guia)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bio' => 'nullable|string|max:1000',
            'idiomas' => 'nullable|string|max:255',
            'experiencia_anios' => 'nullable|integer|min:0',
            'zonas' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'lugares' => 'nullable|array',
            'lugares.*' => 'exists:lugares,id',
        ]);

        $datos = $request->except('foto', 'lugares');

        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($guia->foto && file_exists(public_path($guia->foto))) {
                unlink(public_path($guia->foto));
            }
            
            $nombre = time() . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->move(public_path('imagenes/guias'), $nombre);
            $datos['foto'] = 'imagenes/guias/' . $nombre;
        }

        $guia->update($datos);

        // Actualizar asociación de lugares
        if ($request->has('lugares')) {
            $guia->lugares()->sync($request->lugares);
        } else {
            $guia->lugares()->detach();
        }

        // Procesar múltiples imágenes de galería
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $nombreImg = time() . '_' . uniqid() . '_' . $index . '.' . $imagen->getClientOriginalExtension();
                $imagen->move(public_path('imagenes/galeria/guias'), $nombreImg);
                $guia->imagenes()->create([
                    'ruta' => 'imagenes/galeria/guias/' . $nombreImg,
                    'orden' => $index,
                ]);
            }
        }

        return redirect()->route('guias.index')
            ->with('success', 'Guía turístico actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guia $guia)
    {
        // Eliminar foto si existe
        if ($guia->foto && file_exists(public_path($guia->foto))) {
            unlink(public_path($guia->foto));
        }

        $guia->delete();

        return redirect()->route('guias.index')
            ->with('success', 'Guía turístico eliminado exitosamente.');
    }
}
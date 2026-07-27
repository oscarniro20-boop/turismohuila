<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Models\TourPaquete;
use Illuminate\Http\Request;

class AgenciaController extends Controller
{
    public function index()
    {
        $agencias = Agencia::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->withCount('tours')
            ->get()
            ->each(function ($agencia) {
                $agencia->total_resenas = $agencia->resenas_count;
            });
        return view('agencias.index', compact('agencias'));
    }

    public function create()
    {
        return view('agencias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'             => 'required|string|max:255|unique:agencias,nombre',
            'municipio'          => 'nullable|string|max:255',
            'telefono_contacto'  => 'nullable|string|max:50',
            'whatsapp'           => 'nullable|string|max:50',
            'email'              => 'nullable|email|max:255',
            'imagen'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tours'              => 'nullable|array',
            'tours.*'            => 'integer|exists:tours,id',
            'paquetes.*.precio'        => 'nullable|numeric|min:0',
            'paquetes.*.duracion_dias' => 'nullable|integer|min:1',
            'paquetes.*.cupo_maximo'   => 'nullable|integer|min:1',
            'paquetes.*.descripcion'   => 'nullable|string',
        ]);

        $datos = $request->only(['nombre', 'municipio', 'telefono_contacto', 'whatsapp', 'email']);

        if ($request->hasFile('imagen')) {
            $nombre = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('imagenes/agencias'), $nombre);
            $datos['imagen'] = 'imagenes/agencias/' . $nombre;
        }

        $agencia = Agencia::create($datos);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($agencia, $request, 'galeria/agencias');

        $tourIds = $request->input('tours', []);
        $agencia->tours()->sync($tourIds);

        foreach ($tourIds as $tourId) {
            $p = $request->input("paquetes.$tourId", []);
            TourPaquete::updateOrCreate(
                ['agencia_id' => $agencia->id, 'tour_id' => $tourId],
                [
                    'precio'        => $p['precio'] ?? null,
                    'duracion_dias' => $p['duracion_dias'] ?? null,
                    'cupo_maximo'   => $p['cupo_maximo'] ?? null,
                    'descripcion'   => $p['descripcion'] ?? null,
                ]
            );
        }

        return redirect()->route('agencias.index')
                         ->with('success', 'Agencia creada correctamente.');
    }

    public function show(Agencia $agencia)
    {
        $agencia->load('paquetes.tour', 'imagenes', 'resenas');
        return view('agencias.show', compact('agencia'));
    }

    public function edit(Agencia $agencia)
    {
        $agencia->load(['tours', 'paquetes.tour']);
        return view('agencias.edit', compact('agencia'));
    }

    public function update(Request $request, Agencia $agencia)
    {
        $request->validate([
            'nombre'             => 'required|string|max:255|unique:agencias,nombre,' . $agencia->id,
            'municipio'          => 'nullable|string|max:255',
            'telefono_contacto'  => 'nullable|string|max:50',
            'whatsapp'           => 'nullable|string|max:50',
            'email'              => 'nullable|email|max:255',
            'imagen'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tours'              => 'nullable|array',
            'tours.*'            => 'integer|exists:tours,id',
            'paquetes.*.precio'        => 'nullable|numeric|min:0',
            'paquetes.*.duracion_dias' => 'nullable|integer|min:1',
            'paquetes.*.cupo_maximo'   => 'nullable|integer|min:1',
            'paquetes.*.descripcion'   => 'nullable|string',
        ]);

        $datos = $request->only(['nombre', 'municipio', 'telefono_contacto', 'whatsapp', 'email']);

        if ($request->hasFile('imagen')) {
            $nombre = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('imagenes/agencias'), $nombre);
            $datos['imagen'] = 'imagenes/agencias/' . $nombre;
        }

        $agencia->update($datos);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($agencia, $request, 'galeria/agencias');

        $tourIds = $request->input('tours', []);
        $agencia->tours()->sync($tourIds);

        foreach ($tourIds as $tourId) {
            $p = $request->input("paquetes.$tourId", []);
            TourPaquete::updateOrCreate(
                ['agencia_id' => $agencia->id, 'tour_id' => $tourId],
                [
                    'precio'        => $p['precio'] ?? null,
                    'duracion_dias' => $p['duracion_dias'] ?? null,
                    'cupo_maximo'   => $p['cupo_maximo'] ?? null,
                    'descripcion'   => $p['descripcion'] ?? null,
                ]
            );
        }

        TourPaquete::where('agencia_id', $agencia->id)
            ->whereNotIn('tour_id', $tourIds)
            ->delete();

        return redirect()->route('agencias.index')
                         ->with('success', 'Agencia actualizada correctamente.');
    }

    public function destroy(Agencia $agencia)
    {
        $agencia->delete();
        return redirect()->route('agencias.index')
                         ->with('success', 'Agencia eliminada correctamente.');
    }
}
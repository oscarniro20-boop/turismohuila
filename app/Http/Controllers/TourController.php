<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
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

        return view('tours.index', compact('tours', 'municipios'));
    }

    public function create()
    {
        return view('tours.create');
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
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $file = $request->file('imagen');
            $nombre = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('imagenes/tours'), $nombre);
            $data['imagen'] = 'imagenes/tours/' . $nombre;
        }

        $tour = Tour::create($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($tour, $request, 'galeria/tours');

        $tour->agencias()->sync(
            collect($request->input('agencias', []))->mapWithKeys(fn($id) => [
                $id => ['precio' => $request->input("precios.$id")]
            ])
        );

        return redirect()->route('tours.index')
                         ->with('success', 'Tour creado correctamente.');
    }

    public function show(Tour $tour)
    {
        $tour->load('paquetes.agencia', 'imagenes', 'resenas');
        return view('tours.show', compact('tour'));
    }

    public function edit(Tour $tour)
    {
        $tour->load('paquetes.agencia');
        return view('tours.edit', compact('tour'));
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
            'imagen'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $file = $request->file('imagen');
            $nombre = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('imagenes/tours'), $nombre);
            $data['imagen'] = 'imagenes/tours/' . $nombre;
        }

        $tour->update($data);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($tour, $request, 'galeria/tours');

        $agenciaIds = $request->input('agencias', []);
        $tour->agencias()->sync($agenciaIds);

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
        $tour->delete();

        return redirect()->route('tours.index')
                         ->with('success', 'Tour eliminado correctamente.');
    }
}

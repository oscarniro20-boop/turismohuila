<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Models\Tour;
use App\Models\TourPaquete;
use App\Models\Resena;
use App\Models\Favorito;
use App\Concerns\ManagesImages;
use App\Http\Requests\StoreAgenciaRequest;
use App\Http\Requests\UpdateAgenciaRequest;

class AgenciaController extends Controller
{
    use ManagesImages;

    public function index()
    {
        $agencias = Agencia::withAvg('resenas', 'calificacion')
            ->withCount('resenas')
            ->withCount('tours')
            ->get()
            ->each(function ($agencia) {
                $agencia->total_resenas = $agencia->resenas_count;
            });

        $favoritosIds = $this->obtenerFavoritosIds(auth()->id(), 'agencia');

        return view('agencias.index', compact('agencias', 'favoritosIds'));
    }

    public function create()
    {
        $tours = Tour::orderBy('nombre')->get();

        return view('agencias.create', compact('tours'));
    }

    public function store(StoreAgenciaRequest $request)
    {
        $datos = $request->only(['nombre', 'municipio', 'telefono_contacto', 'whatsapp', 'email', 'latitud', 'longitud']);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'agencias');
        }

        $agencia = Agencia::create($datos);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($agencia, $request, 'galeria/agencias');

        $tourIds = $request->input('tours', []);

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
        $esFav = auth()->check()
            && !auth()->user()->esAdmin()
            && Favorito::where('user_id', auth()->id())->where('tipo', 'agencia')->where('referencia_id', $agencia->id)->exists();

        return view('agencias.show', compact('agencia', 'esFav'));
    }

    public function edit(Agencia $agencia)
    {
        $agencia->load('paquetes.tour', 'imagenes', 'resenas');
        $tours = Tour::orderBy('nombre')->get();

        return view('agencias.edit', compact('agencia', 'tours'));
    }

    public function update(UpdateAgenciaRequest $request, Agencia $agencia)
    {
        $datos = $request->only(['nombre', 'municipio', 'telefono_contacto', 'whatsapp', 'email', 'latitud', 'longitud']);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            $this->eliminarImagen($agencia->imagen);
            
            $datos['imagen'] = $this->subirImagenPrincipal($request->file('imagen'), 'agencias');
        }

        $agencia->update($datos);

        // Procesar múltiples imágenes de galería
        $this->guardarImagenesGaleria($agencia, $request, 'galeria/agencias');

        $tourIds = $request->input('tours', []);

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
        // Eliminar imagen principal
        $this->eliminarImagen($agencia->imagen);
        
        // Eliminar imágenes de galería
        $this->eliminarTodasImagenes($agencia->imagenes);

        // Limpiar registros huérfanos
        Resena::where('tipo', 'agencia')->where('referencia_id', $agencia->id)->delete();
        Favorito::where('tipo', 'agencia')->where('referencia_id', $agencia->id)->delete();
        TourPaquete::where('agencia_id', $agencia->id)->delete();

        $agencia->delete();

        return redirect()->route('agencias.index')
                         ->with('success', 'Agencia eliminada correctamente.');
    }
}
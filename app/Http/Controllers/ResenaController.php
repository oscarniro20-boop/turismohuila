<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResenaController extends Controller
{
    public function index()
    {
        $resenas = Resena::with(['user', 'servicio'])->latest()->get();
        return view('resenas.index', compact('resenas'));
    }

    public function nombreServicio(Request $request)
    {
        $modelo = match ($request->query('tipo')) {
            'lugar'       => \App\Models\Lugar::class,
            'hotel'       => \App\Models\Hotel::class,
            'restaurante' => \App\Models\Restaurante::class,
            'tour'        => \App\Models\Tour::class,
            'guia'        => \App\Models\Guia::class,
            'agencia'     => \App\Models\Agencia::class,
            default       => null,
        };

        if (!$modelo) {
            return response()->json(['nombre' => null]);
        }

        $item = $modelo::find($request->query('id'));
        
        if (!$item) {
            return response()->json(['nombre' => null, 'relacionados' => []]);
        }
        
        $relacionados = [];
        if ($request->query('tipo') === 'tour') {
            $relacionados = $item->agencias->pluck('nombre')->toArray();
        }
        if ($request->query('tipo') === 'agencia') {
            $relacionados = $item->tours->pluck('nombre')->toArray();
        }
        
        return response()->json(['nombre' => $item->nombre, 'relacionados' => $relacionados]);
    }

    public function misResenas()
    {
        $resenas = Resena::with('servicio')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        return view('resenas.mis-resenas', compact('resenas'));
    }

    public function create()
    {
        $tipo = request('tipo');
        $referenciaId = request('referencia_id');
        return view('resenas.create', compact('tipo', 'referenciaId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo'         => 'required|in:lugar,hotel,restaurante,tour,guia,agencia',
            'referencia_id'=> 'required|integer',
            'comentario'   => 'required|string|max:1000',
            'calificacion' => 'required|integer|min:1|max:5',
        ]);

        Resena::create([
            'user_id'      => Auth::id(),
            'tipo'         => $request->tipo,
            'referencia_id'=> $request->referencia_id,
            'comentario'   => $request->comentario,
            'calificacion' => $request->calificacion,
        ]);

        return redirect()->route(auth()->user()->esAdmin() ? 'resenas.index' : 'resenas.mis')
                         ->with('success', 'Reseña creada correctamente.');
    }

    public function show(Resena $resena)
    {
        return view('resenas.show', compact('resena'));
    }

    public function edit(Resena $resena)
    {
        return view('resenas.edit', compact('resena'));
    }

    public function update(Request $request, Resena $resena)
    {
        $this->authorize('update', $resena);

        $request->validate([
            'comentario'   => 'required|string|max:1000',
            'calificacion' => 'required|integer|min:1|max:5',
        ]);

        $resena->update($request->only(['comentario', 'calificacion']));

        return redirect()->route(auth()->user()->esAdmin() ? 'resenas.index' : 'resenas.mis')
                         ->with('success', 'Reseña actualizada correctamente.');
    }

    public function destroy(Resena $resena)
    {
        $this->authorize('delete', $resena);

        $resena->delete();

        return redirect()->route('resenas.mis')
                         ->with('success', 'Reseña eliminada correctamente.');
    }
}
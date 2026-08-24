<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;

class FavoritoController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:lugar,hotel,restaurante,tour,guia,agencia',
            'referencia_id' => 'required|integer',
        ]);

        // Evita favoritos huerfanos: el recurso referenciado debe existir.
        $clase = match ($request->tipo) {
            'lugar' => \App\Models\Lugar::class,
            'hotel' => \App\Models\Hotel::class,
            'restaurante' => \App\Models\Restaurante::class,
            'tour' => \App\Models\Tour::class,
            'guia' => \App\Models\Guia::class,
            'agencia' => \App\Models\Agencia::class,
            default => null,
        };

        if ($clase === null || ! $clase::whereKey($request->referencia_id)->exists()) {
            return response()->json(['message' => 'El recurso indicado no existe.'], 404);
        }

        $favorito = Favorito::where('user_id', auth()->id())
            ->where('tipo', $request->tipo)
            ->where('referencia_id', $request->referencia_id)
            ->first();

        if ($favorito) {
            $favorito->delete();
            return response()->json(['activo' => false]);
        }

        Favorito::create([
            'user_id' => auth()->id(),
            'tipo' => $request->tipo,
            'referencia_id' => $request->referencia_id,
        ]);

        return response()->json(['activo' => true]);
    }

    public function index()
    {
        $favoritos = Favorito::with('servicio')
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->filter(fn($f) => $f->servicio !== null)
            ->groupBy('tipo')
            ->map(fn($grupo) => $grupo->map(fn($f) => $f->servicio));

        return view('mis-favoritos.index', compact('favoritos'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BusquedaService;

class BusquedaController extends Controller
{
    public function buscar(Request $request, BusquedaService $busquedaService)
    {
        $q = $request->query('q', '');

        if (mb_strlen(trim($q)) < 2) {
            return response()->json(['resultados' => []]);
        }

        $resultados = $busquedaService->buscarEnTodos($q);

        return response()->json(['resultados' => $resultados]);
    }
}

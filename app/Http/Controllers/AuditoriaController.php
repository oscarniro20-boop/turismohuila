<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Visor de la auditoría central (Fase 6), solo para administradores.
 */
class AuditoriaController extends Controller
{
    /** Listado paginado con filtros por acción y entidad. */
    public function index(Request $request): View
    {
        $acciones = Auditoria::query()->select('accion')->distinct()->orderBy('accion')->pluck('accion');
        $entidades = Auditoria::query()->select('entidad')->distinct()->orderBy('entidad')->pluck('entidad');

        $filtros = [
            'accion' => $request->query('accion', ''),
            'entidad' => $request->query('entidad', ''),
        ];

        $auditorias = Auditoria::with('user')
            ->when($filtros['accion'] !== '', fn ($q) => $q->where('accion', $filtros['accion']))
            ->when($filtros['entidad'] !== '', fn ($q) => $q->where('entidad', $filtros['entidad']))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.auditorias', [
            'auditorias' => $auditorias,
            'acciones' => $acciones,
            'entidades' => $entidades,
            'filtros' => $filtros,
        ]);
    }
}
@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="d-block mb-1 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">ADMINISTRACIÓN</span>
            <h1 class="mb-0" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.9rem;">Auditoría central</h1>
            <p class="small mb-0 mt-1" style="color: var(--color-muted-fg);">Registro de operaciones administrativas: quién hizo qué y cuándo.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">👥 Usuarios</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">⬅️ Volver al panel</a>
        </div>
    </div>

    <div class="rounded-4 p-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
        <form method="GET" action="{{ route('admin.auditorias.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-sm-5">
                <label for="accion" class="form-label small fw-semibold" style="color: var(--color-fg);">Acción</label>
                <select class="form-select form-select-sm" id="accion" name="accion">
                    <option value="">Todas</option>
                    @foreach ($acciones as $accion)
                        <option value="{{ $accion }}" {{ $filtros['accion'] === $accion ? 'selected' : '' }}>{{ $accion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-5">
                <label for="entidad" class="form-label small fw-semibold" style="color: var(--color-fg);">Entidad</label>
                <select class="form-select form-select-sm" id="entidad" name="entidad">
                    <option value="">Todas</option>
                    @foreach ($entidades as $entidad)
                        <option value="{{ $entidad }}" {{ $filtros['entidad'] === $entidad ? 'selected' : '' }}>{{ $entidad }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-sm rounded-pill px-3 w-100" style="border: 1px solid var(--color-border); color: var(--color-fg);">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle mb-0 small" style="--bs-table-hover-bg: var(--color-border);">
                <thead>
                    <tr>
                        <th scope="col">Fecha</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Acción</th>
                        <th scope="col">Entidad</th>
                        <th scope="col">Detalle</th>
                        <th scope="col">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditorias as $registro)
                        <tr>
                            <td class="text-nowrap" style="color: var(--color-muted-fg);">{{ $registro->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $registro->user?->name ?? '—' }}</td>
                            <td><span class="fw-semibold" style="color: var(--color-fg);">{{ $registro->accion }}</span></td>
                            <td style="color: var(--color-muted-fg);">
                                {{ $registro->entidad ?? '—' }}{{ $registro->entidad_id ? ' #'.$registro->entidad_id : '' }}
                            </td>
                            <td class="small" style="color: var(--color-muted-fg); max-width: 22rem;">
                                @if ($registro->datos)
                                    <code class="small">{{ \Illuminate\Support\Str::limit(json_encode($registro->datos, JSON_UNESCAPED_UNICODE), 160) }}</code>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="color: var(--color-muted-fg);">{{ $registro->ip ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="color: var(--color-muted-fg);">Todavía no hay registros de auditoría.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $auditorias->links() }}
        </div>
    </div>
</div>
@endsection
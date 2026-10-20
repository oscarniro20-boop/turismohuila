@extends('layouts.app')

@section('title', 'Panel de administración')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="d-block mb-1 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">ADMINISTRACIÓN</span>
            <h1 class="mb-0" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.9rem;">Panel de administración</h1>
            <a href="{{ route('admin.importacion.index') }}" class="btn btn-sm rounded-pill mt-2" style="border: 1px solid var(--color-border); color: var(--color-fg);">📥 Importación masiva CSV</a>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm rounded-pill mt-2" style="border: 1px solid var(--color-border); color: var(--color-fg);">👥 Usuarios</a>
            <a href="{{ route('admin.auditorias.index') }}" class="btn btn-sm rounded-pill mt-2" style="border: 1px solid var(--color-border); color: var(--color-fg);">📜 Auditoría</a>
        </div>
        <div class="text-end">
            <div class="small" style="color: var(--color-muted-fg);">Servicios activos</div>
            <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.9rem; color: var(--color-primary);">{{ $serviciosActivos }}</div>
        </div>
    </div>

    {{-- ── KPIs ─────────────────────────────────────────────────────────── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 mb-4">
        <div class="col">
            <div class="rounded-4 p-3 h-100" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <div class="small" style="color: var(--color-muted-fg);">Lugares publicados</div>
                <div class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 2.2rem; color: var(--color-fg);">{{ $lugaresCount }}</div>
                <div style="height: 6px; border-radius: 3px; background: transparent;">
                    <div style="width: {{ min(100, $lugaresCount * 12) }}%; height: 6px; border-radius: 3px; background: var(--color-primary);"></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="rounded-4 p-3 h-100" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <div class="small" style="color: var(--color-muted-fg);">Servicios activos</div>
                <div class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 2.2rem; color: var(--color-fg);">{{ $serviciosActivos }}</div>
                <div style="height: 6px; border-radius: 3px; background: transparent;">
                    <div style="width: {{ min(100, $serviciosActivos * 5) }}%; height: 6px; border-radius: 3px; background: var(--color-primary);"></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="rounded-4 p-3 h-100" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <div class="small" style="color: var(--color-muted-fg);">Reseñas este mes</div>
                <div class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 2.2rem; color: var(--color-fg);">{{ $resenasEsteMes }}</div>
                <div style="height: 6px; border-radius: 3px; background: transparent;">
                    <div style="width: {{ min(100, $resenasEsteMes * 15) }}%; height: 6px; border-radius: 3px; background: var(--color-primary);"></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="rounded-4 p-3 h-100" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <div class="small" style="color: var(--color-muted-fg);">Usuarios registrados</div>
                <div class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 2.2rem; color: var(--color-fg);">{{ $usuariosCount }}</div>
                <div style="height: 6px; border-radius: 3px; background: transparent;">
                    <div style="width: {{ min(100, $usuariosCount * 15) }}%; height: 6px; border-radius: 3px; background: var(--color-primary);"></div>
                </div>
            </div>
        </div>
    </div>
{{-- ── Distribución por categoría ───────────────────────────────────── --}}
    <div class="rounded-4 p-4 mb-4" style="background: #fff; border: 1px solid var(--color-border); box-shadow: var(--shadow-ring);">
        <h2 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem;">Distribución por categoría</h2>
        @if($serviciosActivos > 0)
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                @foreach($porCategoria as $categoria => $datos)
                    <div class="col">
                        <div class="rounded-4 p-3 h-100" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-medium" style="color: var(--color-fg);">{{ $categoria }}</span>
                                <span class="fw-semibold" style="color: var(--color-primary);">{{ $datos['count'] }}</span>
                            </div>
                            <div class="mt-1" style="height: 6px; border-radius: 3px; background: transparent;">
                                <div style="width: {{ $datos['pct'] }}%; height: 6px; border-radius: 3px; background: var(--color-primary);"></div>
                            </div>
                            <div class="small mt-1" style="color: var(--color-muted-fg);">{{ number_format($datos['pct'], 1) }}% del total</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="small mb-0" style="color: var(--color-muted-fg);">Aún no hay servicios registrados.</p>
        @endif
    </div>
{{-- ── Gestión de servicios (tabs) ──────────────────────────────────── --}}
    <div class="rounded-4 p-4 mb-4" style="background: #fff; border: 1px solid var(--color-border); box-shadow: var(--shadow-ring);">
        <h2 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem;">Gestión de servicios</h2>

        <ul class="nav nav-tabs gap-1 mb-3" role="tablist" style="border-bottom-color: var(--color-border);">
            @foreach($serviciosPorModulo as $label => $items)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $loop->index }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $loop->index }}" type="button" role="tab"
                            style="color: {{ $loop->first ? 'var(--color-primary)' : 'var(--color-muted-fg)' }};"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $label }} ({{ count($items) }})</button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($serviciosPorModulo as $label => $items)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $loop->index }}" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 small" style="--bs-table-hover-bg: var(--color-muted);">
                            <thead>
                                <tr>
                                    <th scope="col">Servicio</th>
                                    <th scope="col">Municipio</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Valoración</th>
                                    <th scope="col">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td class="fw-semibold" style="color: var(--color-fg);">{{ $item['nombre'] }}</td>
                                        <td style="color: var(--color-muted-fg);">📍 {{ $item['ubicacion'] }}</td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,167,69,.16); color: var(--color-fg);">Publicado</span>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            @if($item['calificacion'] > 0)
                                                <span style="color: var(--color-gold);">★</span> {{ number_format($item['calificacion'], 1) }}
                                            @else
                                                <span style="color: var(--color-muted-fg);">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ $item['edit_url'] }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">✏️ Editar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3" style="color: var(--color-muted-fg);">Sin registros en {{ $label }}.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
{{-- ── Calidad media global ─────────────────────────────────────────── --}}
    <div class="rounded-4 p-3 mb-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
        <div class="small" style="color: var(--color-muted-fg);">Calidad media global</div>
        <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.9rem; color: var(--color-primary);">
            {{ $calidadMedia > 0 ? number_format($calidadMedia, 1) . ' ★' : '—' }}
        </div>
    </div>

    {{-- ── Actividad reciente ───────────────────────────────────────────── --}}
    <div class="rounded-4 p-4" style="background: #fff; border: 1px solid var(--color-border); box-shadow: var(--shadow-ring);">
        <h2 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem;">Actividad reciente</h2>
        @if($actividadReciente->isNotEmpty())
            <div class="d-flex flex-column gap-3">
                @foreach($actividadReciente as $evento)
                    <div class="d-flex align-items-start gap-3 rounded-4 p-3" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:40px; height:40px; background: var(--color-primary); color:#fff; font-size:1.1rem;">{{ $evento['icono'] }}</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small" style="color: var(--color-fg);">{{ $evento['titulo'] }}</div>
                            <p class="small mb-0" style="color: var(--color-muted-fg);">{{ $evento['detalle'] }}</p>
                        </div>
                        <div class="small text-nowrap" style="color: var(--color-muted-fg);">{{ $evento['fecha']?->format('d/m/Y H:i') }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="small mb-0" style="color: var(--color-muted-fg);">Todavía no hay actividad registrada.</p>
        @endif
    </div>
</div>
@endsection
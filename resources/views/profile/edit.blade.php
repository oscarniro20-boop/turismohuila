@extends('layouts.app')

@section('title', 'Mi Cuenta - TurismoHuila')

@section('content')
<div class="container py-4">

    {{-- Header MI CUENTA --}}
    <div class="d-flex align-items-start justify-content-between mb-4 p-4 rounded-4" style="background: var(--color-muted);">
        <div>
            <h1 class="h3 mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">MI CUENTA</h1>
            <p class="mb-1 small" style="color: var(--color-muted-fg);">{{ $user->email }}</p>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill" style="background-color: {{ $user->esAdmin() ? 'var(--color-gold)' : 'var(--color-primary)' }}; color: #fff;">
                    {{ $user->esAdmin() ? 'Admin' : 'Turista' }}
                </span>
                <small style="color: var(--color-muted-fg);">Miembro desde {{ $user->created_at->translatedFormat('F \d\e Y') }}</small>
            </div>
        </div>
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:64px; height:64px; background-color: var(--color-primary); color:#fff; font-family:'Fraunces', Georgia, serif; font-size:1.5rem; font-weight:600; box-shadow: var(--shadow-ring);">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="rounded-4 h-100 hover-lift" style="background: #fff; box-shadow: var(--shadow-ring);">
                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="fs-1 fw-bold" style="color: var(--color-primary); font-family: 'Fraunces', Georgia, serif;">{{ $favoritosCount }}</div>
                    <div class="small fw-medium text-uppercase mt-1" style="color: var(--color-muted-fg); letter-spacing: .05em;">Favoritos</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="rounded-4 h-100 hover-lift" style="background: #fff; box-shadow: var(--shadow-ring);">
                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="fs-1 fw-bold" style="color: var(--color-gold); font-family: 'Fraunces', Georgia, serif;">{{ $resenasCount }}</div>
                    <div class="small fw-medium text-uppercase mt-1" style="color: var(--color-muted-fg); letter-spacing: .05em;">Reseñas publicadas</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="rounded-4 h-100 hover-lift" style="background: #fff; box-shadow: var(--shadow-ring);">
                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="fs-1 fw-bold" style="color: var(--color-secondary); font-family: 'Fraunces', Georgia, serif;">{{ number_format($calificacionPromedio, 1) }}</div>
                    <div class="small fw-medium text-uppercase mt-1" style="color: var(--color-muted-fg); letter-spacing: .05em;">Calificación promedio</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pestañas estilo píldora --}}
    <ul class="nav gap-2 mb-4" id="perfilTab" role="tablist" style="border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 border-0" id="favoritos-tab" data-bs-toggle="tab" data-bs-target="#favoritos" type="button" role="tab" aria-controls="favoritos" aria-selected="true">Favoritos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 border-0" id="resenas-tab" data-bs-toggle="tab" data-bs-target="#mis-resenas" type="button" role="tab" aria-controls="mis-resenas" aria-selected="false">Mis reseñas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 border-0" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab" aria-controls="datos" aria-selected="false">Datos</button>
        </li>
    </ul>

    <style>
        #perfilTab .nav-link { color: var(--color-muted-fg); background: transparent; }
        #perfilTab .nav-link.active { color: #fff; background-color: var(--color-primary); }
    </style>

    <div class="tab-content" id="perfilTabContent">
        {{-- TAB: Favoritos --}}
        <div class="tab-pane fade show active" id="favoritos" role="tabpanel" aria-labelledby="favoritos-tab">
            @if($favoritos->isEmpty())
                <div class="rounded-4 p-4 text-center" style="background: var(--color-muted); color: var(--color-muted-fg);">No tienes favoritos guardados.</div>
            @else
                @php
                    $rutasPorTipo = [
                        'lugar' => 'lugares.show',
                        'hotel' => 'hoteles.show',
                        'restaurante' => 'restaurantes.show',
                        'tour' => 'tours.show',
                        'guia' => 'guias.show',
                        'agencia' => 'agencias.show',
                    ];
                @endphp
                @foreach($favoritos->groupBy('tipo') as $tipo => $items)
                    <h6 class="fw-bold mt-3 mb-2 text-uppercase small" style="color: var(--color-muted-fg); letter-spacing: .08em;">
                        {{ $tipo === 'lugar' ? 'Lugares' : ($tipo === 'guia' ? 'Guías' : ucfirst($tipo) . 's') }}
                        <span class="badge rounded-pill" style="background: var(--color-border); color: var(--color-fg);">{{ $items->count() }}</span>
                    </h6>
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($items as $fav)
                            <div class="d-flex justify-content-between align-items-center rounded-3 px-3 py-2" style="background: #fff; border: 1px solid var(--color-border);">
                                <span class="small fw-medium">
                                    @if($fav->servicio && isset($rutasPorTipo[$fav->tipo]))
                                        <a href="{{ route($rutasPorTipo[$fav->tipo], $fav->referencia_id) }}" class="text-decoration-none" style="color: var(--color-fg);">{{ $fav->servicio->nombre }}</a>
                                    @elseif($fav->servicio)
                                        {{ $fav->servicio->nombre ?? ucfirst($fav->tipo) . ' #' . $fav->referencia_id }}
                                    @else
                                        <span style="color: var(--color-muted-fg);">{{ ucfirst($fav->tipo) . ' (eliminado)' }}</span>
                                    @endif
                                </span>
                                <form method="POST" action="{{ route('favoritos.toggle') }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="tipo" value="{{ $fav->tipo }}">
                                    <input type="hidden" name="referencia_id" value="{{ $fav->referencia_id }}">
                                    <button type="submit" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center border-0" style="width: 32px; height: 32px; background: transparent;" title="Quitar de favoritos">🗑️</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

        {{-- TAB: Mis reseñas --}}
        <div class="tab-pane fade" id="mis-resenas" role="tabpanel" aria-labelledby="resenas-tab">
            @if($resenas->isEmpty())
                <div class="rounded-4 p-4 text-center" style="background: var(--color-muted); color: var(--color-muted-fg);">No has publicado reseñas todavía.</div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($resenas as $resena)
                        <div class="rounded-4 p-3" style="background: #fff; box-shadow: var(--shadow-ring);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="small">{{ $resena->nombre_servicio }}</strong>
                                    <div class="mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="small" style="color: {{ $i <= $resena->calificacion ? 'var(--color-gold)' : '#ddd' }};">★</span>
                                        @endfor
                                        <span class="small ms-1" style="color: var(--color-muted-fg);">{{ $resena->calificacion }}/5</span>
                                    </div>
                                </div>
                                <small style="color: var(--color-muted-fg);">{{ $resena->created_at->format('d/m/Y') }}</small>
                            </div>
                            @if($resena->comentario)
                                <p class="mt-2 mb-0 small" style="color: var(--color-muted-fg);">{{ $resena->comentario }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TAB: Datos --}}
        <div class="tab-pane fade" id="datos" role="tabpanel" aria-labelledby="datos-tab">
            <div class="rounded-4 mb-4" style="background: #fff; box-shadow: var(--shadow-ring);">
                <div class="px-4 pt-4 fw-medium" style="font-family: 'Fraunces', Georgia, serif; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">Información del Perfil</div>
                <div class="p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-4 mb-4" style="background: #fff; box-shadow: var(--shadow-ring);">
                <div class="px-4 pt-4 fw-medium" style="font-family: 'Fraunces', Georgia, serif; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">Cambiar Contraseña</div>
                <div class="p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-4 mb-4" style="background: #fff; box-shadow: var(--shadow-ring);">
                <div class="px-4 pt-4 fw-medium" style="font-family: 'Fraunces', Georgia, serif; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">Eliminar Cuenta</div>
                <div class="p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

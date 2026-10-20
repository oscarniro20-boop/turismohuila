@extends('layouts.app')

@section('title', 'Mi Cuenta - Turismo por Colombia')

@push('styles')
<style>
    .profile-page { background: var(--color-bg); }
    .profile-hero { background: var(--color-primary); color: #fff; }
    .profile-hero__inner, .profile-content { max-width: 940px; margin-inline: auto; }
    .profile-hero__inner { min-height: 164px; padding: 2.25rem 1.25rem 2.1rem; }
    .profile-hero__eyebrow, .profile-section-label { color: var(--color-gold); font-size: .68rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
    .profile-avatar { width: 64px; height: 64px; flex: 0 0 64px; border: 1px solid rgba(255,255,255,.22); background: color-mix(in oklab, var(--color-primary) 74%, #fff); color: #fff; font-family: 'Fraunces', Georgia, serif; font-size: 1.5rem; }
    .profile-hero__name { font-family: 'Fraunces', Georgia, serif; font-size: clamp(1.8rem, 4vw, 2.45rem); line-height: 1; }
    .profile-meta { color: rgba(255,255,255,.7); font-size: .72rem; }
    .profile-badge { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.1); color: #fff; font-size: .68rem; font-weight: 600; }
    .profile-content { padding: 1.55rem 1.25rem 3rem; }
    .profile-stat { min-height: 61px; border: 1px solid var(--color-border); border-radius: .75rem; background: color-mix(in oklab, #fff 72%, var(--color-bg)); box-shadow: var(--shadow-ring); padding: .75rem 1rem; }
    .profile-stat__icon { width: 28px; height: 28px; color: var(--color-primary); background: color-mix(in oklab, var(--color-primary) 10%, #fff); font-size: .9rem; }
    .profile-stat__value { color: var(--color-fg); font-family: 'Fraunces', Georgia, serif; font-size: 1.1rem; line-height: 1; }
    .profile-stat__label { color: var(--color-muted-fg); font-size: .64rem; }
    #perfilTab { border-bottom: 0 !important; padding-bottom: 0 !important; }
    #perfilTab .nav-link { color: var(--color-muted-fg); background: color-mix(in oklab, var(--color-border) 42%, transparent); font-size: .72rem; }
    #perfilTab .nav-link.active { color: var(--color-fg); background: #fff; box-shadow: var(--shadow-ring); }
    .profile-panel { border: 1px solid var(--color-border); border-radius: .75rem; background: color-mix(in oklab, #fff 72%, var(--color-bg)); box-shadow: var(--shadow-ring); }
    @media (max-width: 575.98px) {
        .profile-hero__inner, .profile-content { padding-inline: 1rem; }
        .profile-hero__inner { min-height: 145px; padding-block: 1.75rem; }
        .profile-avatar { width: 54px; height: 54px; flex-basis: 54px; }
        .profile-stat { padding-inline: .7rem; }
        .profile-stat__icon { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="profile-page">

    {{-- Header MI CUENTA --}}
    <div class="profile-hero">
        <div class="profile-hero__inner d-flex align-items-center gap-3">
        <div class="profile-avatar rounded-circle d-flex align-items-center justify-content-center">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <div class="profile-hero__eyebrow mb-2">{{ __('ui.profile.account') }}</div>
            <h1 class="profile-hero__name mb-2">{{ $user->name }}</h1>
            <div class="profile-meta mb-2">{{ $user->email }}</div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge rounded-pill profile-badge">{{ $user->esAdmin() ? __('ui.profile.admin') : __('ui.profile.tourist') }}</span>
                <span class="profile-meta">{{ __('ui.profile.member_since', ['date' => $user->created_at->translatedFormat('F \d\e Y')]) }}</span>
            </div>
        </div>
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="profile-content">
    <div class="row g-2 mb-4">
        <div class="col-md-4">
            <div class="profile-stat d-flex align-items-center gap-2 h-100">
                <span class="profile-stat__icon rounded-3 d-flex align-items-center justify-content-center">♡</span>
                <span><strong class="profile-stat__value d-block">{{ $favoritosCount }}</strong><small class="profile-stat__label">{{ __('ui.profile.favorites') }}</small></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="profile-stat d-flex align-items-center gap-2 h-100">
                <span class="profile-stat__icon rounded-3 d-flex align-items-center justify-content-center">◌</span>
                <span><strong class="profile-stat__value d-block">{{ $resenasCount }}</strong><small class="profile-stat__label">{{ __('ui.profile.reviews_published') }}</small></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="profile-stat d-flex align-items-center gap-2 h-100">
                <span class="profile-stat__icon rounded-3 d-flex align-items-center justify-content-center">★</span>
                <span><strong class="profile-stat__value d-block">{{ number_format($calificacionPromedio, 1) }}</strong><small class="profile-stat__label">{{ __('ui.profile.average_rating') }}</small></span>
            </div>
        </div>
    </div>

    {{-- Pestañas estilo píldora --}}
    <ul class="nav gap-2 mb-4" id="perfilTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2 border-0" id="favoritos-tab" data-bs-toggle="tab" data-bs-target="#favoritos" type="button" role="tab" aria-controls="favoritos" aria-selected="true">{{ __('ui.profile.favorites') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 border-0" id="resenas-tab" data-bs-toggle="tab" data-bs-target="#mis-resenas" type="button" role="tab" aria-controls="mis-resenas" aria-selected="false">{{ __('ui.profile.my_reviews') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 border-0" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab" aria-controls="datos" aria-selected="false">{{ __('ui.profile.data') }}</button>
        </li>
    </ul>

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
                        'transporte' => 'transportes.show',
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
            <div class="profile-panel mb-4">
                <div class="px-4 pt-4 fw-medium" style="font-family: 'Fraunces', Georgia, serif; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">{{ __('ui.profile.profile_information') }}</div>
                <div class="p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="profile-panel mb-4">
                <div class="px-4 pt-4 fw-medium" style="font-family: 'Fraunces', Georgia, serif; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">{{ __('ui.profile.change_password') }}</div>
                <div class="p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="profile-panel mb-4">
                <div class="px-4 pt-4 fw-medium" style="font-family: 'Fraunces', Georgia, serif; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">{{ __('ui.profile.delete_account') }}</div>
                <div class="p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

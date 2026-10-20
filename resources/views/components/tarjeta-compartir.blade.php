@props(['titulo', 'descripcion' => null, 'tipo'])

@php
    $articulos = [
        'lugar' => __('ui.sharing.place'),
        'hotel' => __('ui.sharing.hotel'),
        'restaurante' => __('ui.sharing.restaurant'),
        'tour' => __('ui.sharing.tour'),
        'agencia' => __('ui.sharing.agency'),
        'guia' => __('ui.sharing.guide'),
        'transporte' => __('ui.sharing.transport'),
    ];
    $articulo = $articulos[$tipo] ?? __('ui.sharing.place');
    $textoInvitacion = $descripcion ?: __('ui.sharing.invite', ['article' => $articulo]);
@endphp

<div class="rounded-4 p-4 border bg-white" style="border-color: var(--color-border);">
    <h3 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">{{ __('ui.common.share') }}</h3>
    <p class="small mb-3" style="color: var(--color-muted-fg);">{{ $textoInvitacion }}</p>

        <div class="d-flex flex-wrap gap-2" data-titulo="{{ $titulo }}" data-url="{{ url()->current() }}">
        <button type="button" class="btn-compartir-nativo btn btn-sm rounded-pill px-3 d-flex align-items-center gap-1"
                style="border: 1px solid var(--color-border); color: var(--color-fg);">
            📤 <span>{{ __('ui.common.share') }}</span>
        </button>
        <button type="button" class="btn-compartir-whatsapp d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff;"
                title="WhatsApp">
            💬
        </button>
        <button type="button" class="btn-compartir-facebook d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff;"
                title="Facebook">
            📘
        </button>
        <button type="button" class="btn-compartir-twitter d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff;"
                title="Twitter / X">
            🐦
        </button>
        <button type="button" class="btn-compartir-copiar d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff; color: var(--color-primary);"
                title="Copiar enlace">
            🔗
        </button>
    </div>

    <a href="{{ route('mapa') }}" class="btn w-100 rounded-pill mt-3 py-2"
       style="border: 1px solid var(--color-border); color: var(--color-fg);">
        📍 {{ __('ui.common.map') }}
    </a>
</div>

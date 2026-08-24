@props(['titulo', 'descripcion' => null, 'tipo'])

@php
    $articulos = [
        'lugar' => 'este lugar',
        'hotel' => 'este hotel',
        'restaurante' => 'este restaurante',
        'tour' => 'este tour',
        'agencia' => 'esta agencia',
        'guia' => 'esta guía',
    ];
    $articulo = $articulos[$tipo] ?? 'este';
    $textoInvitacion = $descripcion ?: "¿Conoces {$articulo}? Invita a otros a descubrirlo.";
@endphp

<div class="rounded-4 p-4 border bg-white" style="border-color: var(--color-border);">
    <h3 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">Comparte</h3>
    <p class="small mb-3" style="color: var(--color-muted-fg);">{{ $textoInvitacion }}</p>

    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-sm rounded-pill px-3 d-flex align-items-center gap-1"
                style="border: 1px solid var(--color-border); color: var(--color-fg);"
                onclick="compartirNativo('{{ $titulo }}', '{{ url()->current() }}')">
            📤 <span>Compartir</span>
        </button>
        <button type="button" class="d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff;"
                title="WhatsApp"
                onclick="window.open('https://wa.me/?text=' + encodeURIComponent('{{ $titulo }} {{ url()->current() }}'), '_blank')">
            💬
        </button>
        <button type="button" class="d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff;"
                title="Facebook"
                onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('{{ url()->current() }}'), '_blank')">
            📘
        </button>
        <button type="button" class="d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff;"
                title="Twitter / X"
                onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent('{{ $titulo }}') + '&url=' + encodeURIComponent('{{ url()->current() }}'), '_blank')">
            🐦
        </button>
        <button type="button" class="d-flex align-items-center justify-content-center rounded-circle"
                style="width: 38px; height: 38px; border: 1px solid var(--color-border); background: #fff; color: var(--color-primary);"
                title="Copiar enlace"
                onclick="copiarEnlace('{{ url()->current() }}')">
            🔗
        </button>
    </div>

    <a href="{{ route('mapa') }}" class="btn w-100 rounded-pill mt-3 py-2"
       style="border: 1px solid var(--color-border); color: var(--color-fg);">
        📍 Ver en el mapa
    </a>
</div>

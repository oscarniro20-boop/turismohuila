@props([
    'imagen' => null,
    'titulo',
    'categoria' => null,
    'favoritoTipo' => null,
    'favoritoId' => null,
    'esFavorito' => false,
])

<div class="position-relative rounded-4 overflow-hidden mb-4" style="box-shadow: var(--shadow-ring);">
    @if($imagen)
        <img src="{{ $imagen }}" alt="{{ $titulo }}" class="w-100" style="height: 380px; object-fit: cover;">
    @else
        <div class="w-100 d-flex align-items-center justify-content-center" style="height: 240px; background: var(--color-muted); font-size: 3rem;">🏞️</div>
    @endif

    <div class="position-absolute bottom-0 start-0 w-100 p-4 p-md-5"
         style="background: linear-gradient(to top, rgba(20,18,12,0.85) 0%, rgba(20,18,12,0.35) 60%, rgba(20,18,12,0) 100%);">
        @if($categoria)
            <span class="eyebrow d-inline-block mb-2" style="color: var(--color-gold);">{{ $categoria }}</span>
        @endif

        <div class="d-flex align-items-start justify-content-between gap-3">
            <h1 class="text-white mb-0" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: clamp(1.6rem, 3.4vw, 2.5rem); line-height: 1.15;">
                {{ $titulo }}
            </h1>
            @auth
                @if($favoritoTipo && !auth()->user()->esAdmin())
                    <x-favorito-boton :tipo="$favoritoTipo" :id="$favoritoId" :activo="$esFavorito" />
                @endif
            @endauth
        </div>

        @isset($meta)
            <div class="d-flex flex-wrap align-items-center gap-3 mt-2 small fw-medium" style="color: rgba(255,255,255,0.92);">
                {{ $meta }}
            </div>
        @endisset
    </div>
</div>

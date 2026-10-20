@props([
    'imagen' => null,
    'titulo',
    'ubicacion' => null,
    'rating' => null,
    'descripcion' => null,
    'precio' => null,
    'precioLabel' => null,
    'url',
    'editUrl' => null,
    'deleteUrl' => null,
    'favoritoTipo' => null,
    'favoritoId' => null,
    'esFavorito' => false,
    'icono' => '🏞️',
])

<div class="h-100 hover-lift rounded-4" style="box-shadow: var(--shadow-ring);">
    <a href="{{ $url }}" class="text-decoration-none d-block position-relative rounded-top-4 overflow-hidden"
       style="aspect-ratio: 4 / 3; background-color: var(--color-muted);">
        @if($imagen)
            <img src="{{ $imagen }}" alt="{{ $titulo }}" class="w-100 h-100" style="object-fit: cover;">
        @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); color: #fff; font-size: 3rem;">{{ $icono }}</div>
        @endif

        @if(!is_null($rating) && $rating > 0)
            <span class="position-absolute top-0 end-0 m-3 px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1 small fw-medium"
                  style="background: rgba(255,255,255,0.95); color: var(--color-gold);">
                @for($i = 1; $i <= 5; $i++)
                    <span aria-hidden="true" style="font-size: .75rem;">{{ $i <= round($rating) ? '★' : '☆' }}</span>
                @endfor
                <span style="color: var(--color-fg);">{{ number_format($rating, 1) }}</span>
            </span>
        @endif

        @auth
            <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                @if($favoritoTipo !== null)
                    <x-favorito-boton :tipo="$favoritoTipo" :id="$favoritoId" :activo="$esFavorito" />
                @endif

                @if(auth()->user()->esAdmin())
                        @if($editUrl)
                        <a href="{{ $editUrl }}" title="{{ __('ui.common.edit') }}" onclick="event.stopPropagation();"
                           class="d-flex align-items-center justify-content-center rounded-circle"
                           style="width:32px; height:32px; background: rgba(255,255,255,0.94); text-decoration:none;">✏️</a>
                    @endif
                    @if($deleteUrl)
                        <form action="{{ $deleteUrl }}" method="POST" data-confirm="{{ __('ui.common.delete') . ' ' . $titulo . '?' }}" onsubmit="event.stopPropagation(); return confirm(this.dataset.confirm);" onclick="event.stopPropagation();">
                            @csrf @method('DELETE')
                            <button type="submit" title="{{ __('ui.common.delete') }}" class="d-flex align-items-center justify-content-center rounded-circle border-0"
                                    style="width:32px; height:32px; background: rgba(255,255,255,0.94);">🗑️</button>
                        </form>
                    @endif
                @endif
            </div>
        @endauth
    </a>

    <div class="p-3">
        @if($ubicacion)
            <p class="eyebrow mb-1 d-flex align-items-center gap-1" style="color: var(--color-muted-fg);">
                <span aria-hidden="true">📍</span> {{ $ubicacion }}
            </p>
        @endif

        <a href="{{ $url }}" class="text-decoration-none">
            <h3 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem; color: var(--color-fg);">
                {{ $titulo }}
            </h3>
        </a>

        @if($descripcion)
            <p class="small mb-0" style="color: var(--color-muted-fg);">{{ $descripcion }}</p>
        @endif

        @if(!is_null($precio))
            <div class="d-flex align-items-baseline gap-2 mt-3 pt-3" style="border-top: 1px solid var(--color-border);">
                <span class="fw-semibold" style="color: var(--color-primary); font-size: 1.1rem;">
                    $ {{ number_format($precio, 0, ',', '.') }}
                </span>
                @if($precioLabel)
                    <span class="small" style="color: var(--color-muted-fg);">{{ $precioLabel }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
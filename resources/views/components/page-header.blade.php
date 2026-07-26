@props(['titulo', 'descripcion' => null])

<div class="position-relative overflow-hidden mb-4" style="border-bottom: 1px solid var(--color-border);">
    <div class="glow-decor" style="top:-60px; right:-60px; width:220px; height:220px; background: var(--color-secondary);"></div>

    <div class="container position-relative py-5" style="z-index:1;">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3">
            <div style="max-width: 640px;">
                <h1 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: clamp(1.75rem, 4vw, 2.75rem); color: var(--color-fg);">
                    {{ $titulo }}
                </h1>
                @if($descripcion)
                    <p class="mb-0" style="color: var(--color-muted-fg);">{{ $descripcion }}</p>
                @endif
            </div>
            {{ $slot ?? '' }}
        </div>
    </div>
</div>
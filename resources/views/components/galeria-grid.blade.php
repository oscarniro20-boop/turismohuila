@props(['imagenes', 'alt' => 'Imagen'])

@if($imagenes && $imagenes->count() > 0)
<div class="mt-4">
    <h2 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.4rem;">
        📷 Galería de Fotos
    </h2>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        @foreach($imagenes as $imagen)
            <div class="col">
                <div class="hover-lift rounded-4 overflow-hidden" style="aspect-ratio: 4 / 3; box-shadow: var(--shadow-ring);">
                    <img
                        src="{{ $imagen->url }}"
                        alt="{{ $alt }}"
                        class="w-100 h-100"
                        style="object-fit: cover;"
                        loading="lazy"
                    />
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

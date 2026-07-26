@props(['imagenes', 'alt' => 'Imagen'])

@if($imagenes && $imagenes->count() > 0)
<div class="mt-6">
    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
        📷 Galería de Fotos
    </h3>

    <!-- Contenedor Grid Responsivo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($imagenes as $imagen)
            <div class="overflow-hidden rounded-xl shadow-md border border-gray-100 group relative aspect-[4/3]">
                <img 
                    src="{{ $imagen->url }}" 
                    alt="{{ $alt }}" 
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    loading="lazy"
                />
            </div>
        @endforeach
    </div>
</div>
@endif

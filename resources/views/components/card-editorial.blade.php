@props([
    'imagen', 
    'titulo', 
    'subtitulo', 
    'descripcion', 
    'url', 
    'etiqueta' => 'EXPLORAR', 
    'icono' => '⛰️',
    'rutaEditar' => null,
    'rutaEliminar' => null
])

<div class="relative h-[480px] rounded-[2rem] overflow-hidden group shadow-lg transition-all duration-300 hover:-translate-y-1">
    
    <!-- Imagen de Fondo -->
    @if($imagen)
        <img 
            src="{{ $imagen }}" 
            alt="{{ $titulo }}" 
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
        />
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-stone-300 to-stone-400"></div>
    @endif

    <!-- Gradiente Oscuro Superpuesto (para legibilidad del texto) -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>

    <!-- Botones de Administración (esquina superior derecha) -->
    @if($rutaEditar || $rutaEliminar)
        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            @if($rutaEditar)
                <a href="{{ $rutaEditar }}" class="bg-white/90 hover:bg-white text-stone-800 p-2 rounded-lg shadow-lg transition-colors" title="Editar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            @endif
            @if($rutaEliminar)
                <form action="{{ $rutaEliminar }}" method="POST" onsubmit="return confirm('¿Eliminar este elemento?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500/90 hover:bg-red-500 text-white p-2 rounded-lg shadow-lg transition-colors" title="Eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Contenido de la Tarjeta (Texto abajo) -->
    <div class="absolute inset-0 p-8 flex flex-col justify-end text-white">
        
        <!-- Etiqueta superior con icono -->
        <div class="flex items-center gap-2 text-amber-400 font-medium text-xs tracking-wider uppercase mb-2">
            <span>{{ $icono }} {{ $etiqueta }}</span>
        </div>

        <!-- Título Principal -->
        <h3 class="font-serif text-3xl font-bold tracking-tight text-white mb-2">
            {{ $titulo }}
        </h3>

        <!-- Subtítulo o Descripción -->
        <p class="text-stone-300 text-sm mb-6 line-clamp-2">
            {{ $subtitulo ?? $descripcion }}
        </p>

        <!-- Botón / Link con Flecha -->
        <a href="{{ $url }}" class="inline-flex items-center gap-2 text-sm font-medium text-white hover:text-amber-300 transition-colors group/link">
            <span>Descubrir</span>
            <span class="transition-transform group-hover/link:translate-x-1">→</span>
        </a>

    </div>
</div>
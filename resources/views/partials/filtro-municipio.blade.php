{{--
    Partial reutilizable: filtro por municipio (server-side) + buscador instantáneo (JS).
    Variables esperadas al incluir:
    - $rutaIndex: nombre de la ruta index (ej: 'lugares.index')
    - $municipios: colección/array de municipios (viene del controlador)
    Si no se pasan, usa valores por defecto seguros (no rompe la vista).
--}}
@php
    $rutaIndex = $rutaIndex ?? request()->route()?->getName();
    $municipios = $municipios ?? config('municipios.huila');
@endphp
<div class="d-flex flex-column flex-sm-row gap-2 mb-4">
    <form method="GET" action="{{ $rutaIndex ? route($rutaIndex) : url()->current() }}" class="d-flex">
        <select name="municipio" class="form-select" style="border-radius: 2rem; border-color: var(--color-border);" onchange="this.form.submit()">
            <option value="">Todos los municipios</option>
            @foreach($municipios as $municipio)
                <option value="{{ $municipio }}" {{ request('municipio') == $municipio ? 'selected' : '' }}>
                    {{ $municipio }}
                </option>
            @endforeach
        </select>
    </form>
    @if(request()->filled('municipio'))
        <a href="{{ $rutaIndex ? route($rutaIndex) : url()->current() }}" class="btn btn-sm align-self-start align-self-sm-center"
           style="border: 1px solid var(--color-border); border-radius: 2rem; color: var(--color-muted-fg);">
            ✕ Quitar filtro
        </a>
    @endif
    <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o municipio..."
           style="border-radius: 2rem; border-color: var(--color-border); max-width: 320px;">
</div>

<script>
document.getElementById('buscador')?.addEventListener('input', function() {
    const texto = this.value.toLowerCase();
    document.querySelectorAll('.tarjeta-item').forEach(function(card) {
        const contenido = card.textContent.toLowerCase();
        card.style.display = contenido.includes(texto) ? '' : 'none';
    });
});
</script>
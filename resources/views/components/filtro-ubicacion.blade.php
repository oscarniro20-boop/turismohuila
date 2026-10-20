@props([
    'accion' => null,
    'municipio' => true,
])

@php
    $mapaUbicacion = \App\Support\Colombia::mapa();
    $depSel = request()->query('departamento');
    $munSel = request()->query('municipio');
    $munsDisponibles = ($depSel && \App\Support\Colombia::existeDepartamento($depSel))
        ? \App\Support\Colombia::municipios($depSel)
        : [];
    $colDep = $municipio ? 'col-md-4' : 'col-md-6';
    $colBtn = $municipio ? 'col-md-4' : 'col-md-2';
@endphp

<form method="GET" @if($accion) action="{{ $accion }}" @endif class="card card-body shadow-sm mb-4">
    <div class="row g-2 align-items-end" data-ubicacion-bloque>
        <div class="{{ $colDep }}">
            <label class="form-label small fw-semibold mb-1">Departamento</label>
            <select name="departamento" class="form-select form-select-sm" data-ubicacion-departamento>
                <option value="">Todos los departamentos</option>
                @foreach($mapaUbicacion as $dep => $muns)
                    <option value="{{ $dep }}" {{ (string) $depSel === (string) $dep ? 'selected' : '' }}>{{ $dep }}</option>
                @endforeach
            </select>
        </div>

        @if($municipio)
        <div class="col-md-4">
            <label class="form-label small fw-semibold mb-1">Municipio</label>
            <select name="municipio" class="form-select form-select-sm" data-ubicacion-municipio>
                <option value="">Todos los municipios</option>
                @foreach($munsDisponibles as $m)
                    <option value="{{ $m }}" {{ (string) $munSel === (string) $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="{{ $colBtn }} d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-success flex-grow-1">Filtrar</button>
            @if($depSel || ($municipio && $munSel))
                <a href="{{ $accion ?? url()->current() }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
            @endif
        </div>
    </div>

    <script type="application/json" data-ubicacion-mapa>@json($mapaUbicacion)</script>
</form>

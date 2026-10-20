@props([
    'departamento' => null,
    'municipio' => null,
])

@php
    $mapaUbicacion = \App\Support\Colombia::mapa();
    $depSel = old('departamento', $departamento);
    $munSel = old('municipio', $municipio);
    $munsDisponibles = ($depSel && \App\Support\Colombia::existeDepartamento($depSel))
        ? \App\Support\Colombia::municipios($depSel)
        : [];
@endphp

<div class="row" data-ubicacion-bloque>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Departamento</label>
        <select name="departamento" class="form-select" data-ubicacion-departamento>
            <option value="">Seleccione departamento...</option>
            @foreach($mapaUbicacion as $dep => $muns)
                <option value="{{ $dep }}" {{ (string) $depSel === (string) $dep ? 'selected' : '' }}>{{ $dep }}</option>
            @endforeach
        </select>
        @error('departamento')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Municipio</label>
        <select name="municipio" class="form-select" data-ubicacion-municipio>
            <option value="">Seleccione municipio...</option>
            @foreach($munsDisponibles as $m)
                <option value="{{ $m }}" {{ (string) $munSel === (string) $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        @error('municipio')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <script type="application/json" data-ubicacion-mapa>@json($mapaUbicacion)</script>
</div>

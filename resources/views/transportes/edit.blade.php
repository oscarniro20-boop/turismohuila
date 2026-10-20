@extends('layouts.app')

@section('title', 'Editar Transporte - Turismo por Colombia')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h4 class="mb-0">✏️ Editar Transporte</h4>
            </div>
            <div class="card-body">

                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('transportes.update', $transporte) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            value="{{ old('nombre', $transporte->nombre) }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $transporte->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <x-form-ubicacion :departamento="old('departamento', $transporte->departamento ?? 'Huila')" :municipio="old('municipio', $transporte->municipio)" />
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo" class="form-select">
                                @error('tipo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @foreach(['Bus', 'Microbús', 'Van', 'Taxi turístico', 'Transporte privado', 'Transporte rural', 'Otro'] as $t)
                                    <option value="{{ $t }}" {{ old('tipo', $transporte->tipo) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud', $transporte->latitud ?? '') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud', $transporte->longitud ?? '') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Precio (opcional)</label>
                            <input type="number" name="precio" class="form-control"
                                min="0" value="{{ old('precio', $transporte->precio) }}">
                            @error('precio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Capacidad (opcional)</label>
                            <input type="number" name="capacidad" class="form-control"
                                min="1" value="{{ old('capacidad', $transporte->capacidad) }}">
                            @error('capacidad')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                value="{{ old('telefono', $transporte->telefono) }}">
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen Principal</label>
                        @if($transporte->imagen)
                            <div><img src="{{ $transporte->url_imagen }}" alt="Imagen" style="max-height: 150px;"></div>
                        @endif
                        <input type="file" name="imagen" class="form-control mt-1" accept="image/*">
                    </div>

                    @if($transporte->imagenes->count() > 0)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Galería actual (marca las que quieras eliminar)</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($transporte->imagenes as $img)
                                    <label class="position-relative">
                                        <input type="checkbox" name="eliminar_imagenes[]" value="{{ $img->id }}" style="position:absolute; top:6px; left:6px; z-index:2;">
                                        <img src="{{ $img->url_galeria }}" alt="" style="height:90px; width:auto; border-radius:.5rem;">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (Múltiples)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes seleccionar múltiples imágenes para la galería</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transportes.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-warning">Actualizar Transporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

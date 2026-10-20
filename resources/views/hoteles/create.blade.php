@extends('layouts.app')

@section('title', 'Crear Hotel - Turismo por Colombia')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">🏨 Nuevo Hotel</h4>
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

                <form action="{{ route('hoteles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            placeholder="Ej: Hotel Timanco" value="{{ old('nombre') }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4"
                            placeholder="Describe el hotel...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <x-form-ubicacion :departamento="old('departamento', 'Huila')" :municipio="old('municipio')" />
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estrellas</label>
                            <select name="estrellas" class="form-select">
                                @error('estrellas')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <option value="1" {{ old('estrellas', 3) == 1 ? 'selected' : '' }}>⭐ 1 Estrella</option>
                                <option value="2" {{ old('estrellas', 3) == 2 ? 'selected' : '' }}>⭐⭐ 2 Estrellas</option>
                                <option value="3" {{ old('estrellas', 3) == 3 ? 'selected' : '' }}>⭐⭐⭐ 3 Estrellas</option>
                                <option value="4" {{ old('estrellas', 3) == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Estrellas</option>
                                <option value="5" {{ old('estrellas', 3) == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Estrellas</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Precio por noche ($)</label>
                            <input type="number" name="precio_noche" class="form-control"
                                placeholder="0" min="0" value="{{ old('precio_noche') }}">
                            @error('precio_noche')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                placeholder="Ej: 3001234567" value="{{ old('telefono') }}">
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                            placeholder="Ej: Calle 10 # 5-20" value="{{ old('direccion') }}">
                        @error('direccion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen Principal</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (Múltiples)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes seleccionar múltiples imágenes para la galería</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('hoteles.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-success">Guardar Hotel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
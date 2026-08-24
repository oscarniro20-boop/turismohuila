
@extends('layouts.app')

@section('title', 'Editar Lugar - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h4 class="mb-0">✏️ Editar Lugar Turístico</h4>
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

                <form action="{{ route('lugares.update', $lugar) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            value="{{ old('nombre', $lugar->nombre) }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $lugar->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Municipio</label>
                            <select name="municipio" class="form-select">
                                <option value="">Seleccione municipio...</option>
                                @error('municipio')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @foreach(config('municipios.huila') as $m)
                                    <option value="{{ $m }}" {{ old('municipio', $lugar->municipio) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="">Seleccione...</option>
                                @error('categoria')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <option value="natural" {{ old('categoria', $lugar->categoria) == 'natural' ? 'selected' : '' }}>🌿 Natural</option>
                                <option value="cultural" {{ old('categoria', $lugar->categoria) == 'cultural' ? 'selected' : '' }}>🏛️ Cultural</option>
                                <option value="aventura" {{ old('categoria', $lugar->categoria) == 'aventura' ? 'selected' : '' }}>🧗 Aventura</option>
                                <option value="religioso" {{ old('categoria', $lugar->categoria) == 'religioso' ? 'selected' : '' }}>⛪ Religioso</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud', $lugar->latitud ?? '') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud', $lugar->longitud ?? '') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen Principal</label>
                        @if($lugar->imagen)
                            <div><img src="{{ $lugar->url_imagen }}" alt="Imagen" style="max-height: 150px;"></div>
                        @endif
                        <input type="file" name="imagen" class="form-control mt-1" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imágenes Adicionales (Galería)</label>
                        @if($lugar->imagenes->count() > 0)
                            <div class="row mb-2">
                                @foreach($lugar->imagenes as $img)
                                    <div class="col-md-3 mb-2">
                                        <img src="{{ $img->url }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Selecciona nuevas imágenes para reemplazar la galería actual</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('lugares.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-warning">Actualizar Lugar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

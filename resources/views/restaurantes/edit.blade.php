@extends('layouts.app')

@section('title', 'Editar Restaurante - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h4 class="mb-0">✏️ Editar Restaurante</h4>
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

                <form action="{{ route('restaurantes.update', $restaurante) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            value="{{ old('nombre', $restaurante->nombre) }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $restaurante->descripcion) }}</textarea>
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
                                    <option value="{{ $m }}" {{ old('municipio', $restaurante->municipio) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Cocina</label>
                            <select name="tipo_cocina" class="form-select">
                                <option value="">Seleccione...</option>
                                @error('tipo_cocina')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <option value="Típica huilense" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Típica huilense' ? 'selected' : '' }}>🍛 Típica huilense</option>
                                <option value="Parrilla" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Parrilla' ? 'selected' : '' }}>🥩 Parrilla</option>
                                <option value="Mariscos" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Mariscos' ? 'selected' : '' }}>🦐 Mariscos</option>
                                <option value="Italiana" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Italiana' ? 'selected' : '' }}>🍝 Italiana</option>
                                <option value="Criolla" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Criolla' ? 'selected' : '' }}>🫕 Criolla</option>
                                <option value="Vegetariana" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Vegetariana' ? 'selected' : '' }}>🥗 Vegetariana</option>
                                <option value="Internacional" {{ old('tipo_cocina', $restaurante->tipo_cocina) == 'Internacional' ? 'selected' : '' }}>🌍 Internacional</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud', $restaurante->latitud ?? '') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud', $restaurante->longitud ?? '') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Precio promedio ($)</label>
                            <input type="number" name="precio_promedio" class="form-control"
                                min="0" value="{{ old('precio_promedio', $restaurante->precio_promedio) }}">
                            @error('precio_promedio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                value="{{ old('telefono', $restaurante->telefono) }}">
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                            value="{{ old('direccion', $restaurante->direccion) }}">
                        @error('direccion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen Principal</label>
                        @if($restaurante->imagen)
                            <div><img src="{{ asset($restaurante->imagen) }}" alt="Imagen" style="max-height: 150px;"></div>
                        @endif
                        <input type="file" name="imagen" class="form-control mt-1" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (Múltiples)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes seleccionar múltiples imágenes para la galería</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('restaurantes.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-warning">Actualizar Restaurante</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
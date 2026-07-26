@extends('layouts.app')

@section('title', 'Crear Guía - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">🎯 Nuevo Guía Turístico</h4>
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

                <form action="{{ route('guias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control"
                            placeholder="Ej: Juan Pérez García"
                            value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción / Biografía</label>
                        <textarea name="bio" class="form-control" rows="3"
                            placeholder="Cuéntanos sobre la experiencia y especialidad de este guía...">{{ old('bio') }}</textarea>
                        @error('bio')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto de Perfil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Opcional - JPG, PNG (máx 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (Múltiples)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes seleccionar múltiples imágenes para la galería</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Lugares que visita este guía</label>
                        @if($lugares->count() > 0)
                            <div class="row row-cols-2 row-cols-md-3 g-2">
                                @foreach($lugares as $lugar)
                                    <div class="col">
                                        <div class="form-check border rounded p-2">
                                            <input class="form-check-input" type="checkbox" name="lugares[]" value="{{ $lugar->id }}"
                                                id="lugar{{ $lugar->id }}"
                                                {{ in_array($lugar->id, old('lugares', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lugar{{ $lugar->id }}">{{ $lugar->nombre }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <strong>⚠️ No hay lugares disponibles</strong>
                                <p>Primero debes crear lugares turísticos antes de poder asociarlos a un guía.</p>
                                <a href="{{ route('lugares.create') }}" class="btn btn-sm btn-primary">Crear Lugar</a>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Idiomas</label>
                            <input type="text" name="idiomas" class="form-control"
                                placeholder="Ej: Español, Inglés, Francés"
                                value="{{ old('idiomas') }}">
                            @error('idiomas')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Años de Experiencia</label>
                            <input type="number" name="experiencia_anios" class="form-control"
                                min="0" value="{{ old('experiencia_anios') }}">
                            @error('experiencia_anios')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control"
                                placeholder="Ej: +573001234567"
                                value="{{ old('whatsapp') }}">
                            @error('whatsapp')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                placeholder="Ej: 3001234567"
                                value="{{ old('telefono') }}">
                            @error('telefono')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guias.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-success">Guardar Guía</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
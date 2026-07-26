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
                                <option value="Acevedo" {{ old('municipio', $lugar->municipio) == 'Acevedo' ? 'selected' : '' }}>Acevedo</option>
                                <option value="Aipe" {{ old('municipio', $lugar->municipio) == 'Aipe' ? 'selected' : '' }}>Aipe</option>
                                <option value="Algeciras" {{ old('municipio', $lugar->municipio) == 'Algeciras' ? 'selected' : '' }}>Algeciras</option>
                                <option value="Altamira" {{ old('municipio', $lugar->municipio) == 'Altamira' ? 'selected' : '' }}>Altamira</option>
                                <option value="Baraya" {{ old('municipio', $lugar->municipio) == 'Baraya' ? 'selected' : '' }}>Baraya</option>
                                <option value="Campoalegre" {{ old('municipio', $lugar->municipio) == 'Campoalegre' ? 'selected' : '' }}>Campoalegre</option>
                                <option value="Colombia" {{ old('municipio', $lugar->municipio) == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                                <option value="Elías" {{ old('municipio', $lugar->municipio) == 'Elías' ? 'selected' : '' }}>Elías</option>
                                <option value="Garzón" {{ old('municipio', $lugar->municipio) == 'Garzón' ? 'selected' : '' }}>Garzón</option>
                                <option value="Gigante" {{ old('municipio', $lugar->municipio) == 'Gigante' ? 'selected' : '' }}>Gigante</option>
                                <option value="Guadalupe" {{ old('municipio', $lugar->municipio) == 'Guadalupe' ? 'selected' : '' }}>Guadalupe</option>
                                <option value="Hobo" {{ old('municipio', $lugar->municipio) == 'Hobo' ? 'selected' : '' }}>Hobo</option>
                                <option value="Iquira" {{ old('municipio', $lugar->municipio) == 'Iquira' ? 'selected' : '' }}>Iquira</option>
                                <option value="Íquira" {{ old('municipio', $lugar->municipio) == 'Íquira' ? 'selected' : '' }}>Íquira</option>
                                <option value="Isnos" {{ old('municipio', $lugar->municipio) == 'Isnos' ? 'selected' : '' }}>Isnos</option>
                                <option value="La Argentina" {{ old('municipio', $lugar->municipio) == 'La Argentina' ? 'selected' : '' }}>La Argentina</option>
                                <option value="La Plata" {{ old('municipio', $lugar->municipio) == 'La Plata' ? 'selected' : '' }}>La Plata</option>
                                <option value="Nátaga" {{ old('municipio', $lugar->municipio) == 'Nátaga' ? 'selected' : '' }}>Nátaga</option>
                                <option value="Neiva" {{ old('municipio', $lugar->municipio) == 'Neiva' ? 'selected' : '' }}>Neiva</option>
                                <option value="Oporapa" {{ old('municipio', $lugar->municipio) == 'Oporapa' ? 'selected' : '' }}>Oporapa</option>
                                <option value="Paicol" {{ old('municipio', $lugar->municipio) == 'Paicol' ? 'selected' : '' }}>Paicol</option>
                                <option value="Palermo" {{ old('municipio', $lugar->municipio) == 'Palermo' ? 'selected' : '' }}>Palermo</option>
                                <option value="Palestina" {{ old('municipio', $lugar->municipio) == 'Palestina' ? 'selected' : '' }}>Palestina</option>
                                <option value="Pitalito" {{ old('municipio', $lugar->municipio) == 'Pitalito' ? 'selected' : '' }}>Pitalito</option>
                                <option value="Rivera" {{ old('municipio', $lugar->municipio) == 'Rivera' ? 'selected' : '' }}>Rivera</option>
                                <option value="Saladoblanco" {{ old('municipio', $lugar->municipio) == 'Saladoblanco' ? 'selected' : '' }}>Saladoblanco</option>
                                <option value="San Agustín" {{ old('municipio', $lugar->municipio) == 'San Agustín' ? 'selected' : '' }}>San Agustín</option>
                                <option value="Santa María" {{ old('municipio', $lugar->municipio) == 'Santa María' ? 'selected' : '' }}>Santa María</option>
                                <option value="Suaza" {{ old('municipio', $lugar->municipio) == 'Suaza' ? 'selected' : '' }}>Suaza</option>
                                <option value="Tarqui" {{ old('municipio', $lugar->municipio) == 'Tarqui' ? 'selected' : '' }}>Tarqui</option>
                                <option value="Tesalia" {{ old('municipio', $lugar->municipio) == 'Tesalia' ? 'selected' : '' }}>Tesalia</option>
                                <option value="Tello" {{ old('municipio', $lugar->municipio) == 'Tello' ? 'selected' : '' }}>Tello</option>
                                <option value="Teruel" {{ old('municipio', $lugar->municipio) == 'Teruel' ? 'selected' : '' }}>Teruel</option>
                                <option value="Timana" {{ old('municipio', $lugar->municipio) == 'Timana' ? 'selected' : '' }}>Timana</option>
                                <option value="Villavieja" {{ old('municipio', $lugar->municipio) == 'Villavieja' ? 'selected' : '' }}>Villavieja</option>
                                <option value="Yaguará" {{ old('municipio', $lugar->municipio) == 'Yaguará' ? 'selected' : '' }}>Yaguará</option>
                            </select>
                        </div>
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
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen Principal</label>
                        @if($lugar->imagen)
                            <div><img src="{{ asset($lugar->imagen) }}" alt="Imagen" style="max-height: 150px;"></div>
                        @endif
                        <input type="file" name="imagen" class="form-control mt-1" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imágenes Adicionales (Galería)</label>
                        @if($lugar->imagenes->count() > 0)
                            <div class="row mb-2">
                                @foreach($lugar->imagenes as $img)
                                    <div class="col-md-3 mb-2">
                                        <img src="{{ asset($img->ruta) }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Selecciona nuevas imágenes para reemplazar la galería actual</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Calificación (0-5)</label>
                        <input type="number" name="calificacion" class="form-control"
                                min="0" max="5" step="0.1"
                                value="{{ old('calificacion', $lugar->calificacion) }}">
                        @error('calificacion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
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

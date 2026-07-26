@extends('layouts.app')

@section('title', 'Crear Hotel - TurismoHuila')

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
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Municipio</label>
                            <select name="municipio" class="form-select">
                                <option value="">Seleccione municipio...</option>
                                @error('municipio')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <option value="Acevedo" {{ old('municipio') == 'Acevedo' ? 'selected' : '' }}>Acevedo</option>
                                <option value="Aipe" {{ old('municipio') == 'Aipe' ? 'selected' : '' }}>Aipe</option>
                                <option value="Algeciras" {{ old('municipio') == 'Algeciras' ? 'selected' : '' }}>Algeciras</option>
                                <option value="Altamira" {{ old('municipio') == 'Altamira' ? 'selected' : '' }}>Altamira</option>
                                <option value="Baraya" {{ old('municipio') == 'Baraya' ? 'selected' : '' }}>Baraya</option>
                                <option value="Campoalegre" {{ old('municipio') == 'Campoalegre' ? 'selected' : '' }}>Campoalegre</option>
                                <option value="Colombia" {{ old('municipio') == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                                <option value="Elías" {{ old('municipio') == 'Elías' ? 'selected' : '' }}>Elías</option>
                                <option value="Garzón" {{ old('municipio') == 'Garzón' ? 'selected' : '' }}>Garzón</option>
                                <option value="Gigante" {{ old('municipio') == 'Gigante' ? 'selected' : '' }}>Gigante</option>
                                <option value="Guadalupe" {{ old('municipio') == 'Guadalupe' ? 'selected' : '' }}>Guadalupe</option>
                                <option value="Hobo" {{ old('municipio') == 'Hobo' ? 'selected' : '' }}>Hobo</option>
                                <option value="Iquira" {{ old('municipio') == 'Iquira' ? 'selected' : '' }}>Iquira</option>
                                <option value="Íquira" {{ old('municipio') == 'Íquira' ? 'selected' : '' }}>Íquira</option>
                                <option value="Isnos" {{ old('municipio') == 'Isnos' ? 'selected' : '' }}>Isnos</option>
                                <option value="La Argentina" {{ old('municipio') == 'La Argentina' ? 'selected' : '' }}>La Argentina</option>
                                <option value="La Plata" {{ old('municipio') == 'La Plata' ? 'selected' : '' }}>La Plata</option>
                                <option value="Nátaga" {{ old('municipio') == 'Nátaga' ? 'selected' : '' }}>Nátaga</option>
                                <option value="Neiva" {{ old('municipio') == 'Neiva' ? 'selected' : '' }}>Neiva</option>
                                <option value="Oporapa" {{ old('municipio') == 'Oporapa' ? 'selected' : '' }}>Oporapa</option>
                                <option value="Paicol" {{ old('municipio') == 'Paicol' ? 'selected' : '' }}>Paicol</option>
                                <option value="Palermo" {{ old('municipio') == 'Palermo' ? 'selected' : '' }}>Palermo</option>
                                <option value="Palestina" {{ old('municipio') == 'Palestina' ? 'selected' : '' }}>Palestina</option>
                                <option value="Pitalito" {{ old('municipio') == 'Pitalito' ? 'selected' : '' }}>Pitalito</option>
                                <option value="Rivera" {{ old('municipio') == 'Rivera' ? 'selected' : '' }}>Rivera</option>
                                <option value="Saladoblanco" {{ old('municipio') == 'Saladoblanco' ? 'selected' : '' }}>Saladoblanco</option>
                                <option value="San Agustín" {{ old('municipio') == 'San Agustín' ? 'selected' : '' }}>San Agustín</option>
                                <option value="Santa María" {{ old('municipio') == 'Santa María' ? 'selected' : '' }}>Santa María</option>
                                <option value="Suaza" {{ old('municipio') == 'Suaza' ? 'selected' : '' }}>Suaza</option>
                                <option value="Tarqui" {{ old('municipio') == 'Tarqui' ? 'selected' : '' }}>Tarqui</option>
                                <option value="Tesalia" {{ old('municipio') == 'Tesalia' ? 'selected' : '' }}>Tesalia</option>
                                <option value="Tello" {{ old('municipio') == 'Tello' ? 'selected' : '' }}>Tello</option>
                                <option value="Teruel" {{ old('municipio') == 'Teruel' ? 'selected' : '' }}>Teruel</option>
                                <option value="Timana" {{ old('municipio') == 'Timana' ? 'selected' : '' }}>Timana</option>
                                <option value="Villavieja" {{ old('municipio') == 'Villavieja' ? 'selected' : '' }}>Villavieja</option>
                                <option value="Yaguará" {{ old('municipio') == 'Yaguará' ? 'selected' : '' }}>Yaguará</option>
                            </select>
                        </div>
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
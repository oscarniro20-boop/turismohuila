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
                                <option value="Acevedo" {{ old('municipio', $restaurante->municipio) == 'Acevedo' ? 'selected' : '' }}>Acevedo</option>
                                <option value="Aipe" {{ old('municipio', $restaurante->municipio) == 'Aipe' ? 'selected' : '' }}>Aipe</option>
                                <option value="Algeciras" {{ old('municipio', $restaurante->municipio) == 'Algeciras' ? 'selected' : '' }}>Algeciras</option>
                                <option value="Altamira" {{ old('municipio', $restaurante->municipio) == 'Altamira' ? 'selected' : '' }}>Altamira</option>
                                <option value="Baraya" {{ old('municipio', $restaurante->municipio) == 'Baraya' ? 'selected' : '' }}>Baraya</option>
                                <option value="Campoalegre" {{ old('municipio', $restaurante->municipio) == 'Campoalegre' ? 'selected' : '' }}>Campoalegre</option>
                                <option value="Colombia" {{ old('municipio', $restaurante->municipio) == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                                <option value="Elías" {{ old('municipio', $restaurante->municipio) == 'Elías' ? 'selected' : '' }}>Elías</option>
                                <option value="Garzón" {{ old('municipio', $restaurante->municipio) == 'Garzón' ? 'selected' : '' }}>Garzón</option>
                                <option value="Gigante" {{ old('municipio', $restaurante->municipio) == 'Gigante' ? 'selected' : '' }}>Gigante</option>
                                <option value="Guadalupe" {{ old('municipio', $restaurante->municipio) == 'Guadalupe' ? 'selected' : '' }}>Guadalupe</option>
                                <option value="Hobo" {{ old('municipio', $restaurante->municipio) == 'Hobo' ? 'selected' : '' }}>Hobo</option>
                                <option value="Iquira" {{ old('municipio', $restaurante->municipio) == 'Iquira' ? 'selected' : '' }}>Iquira</option>
                                <option value="Íquira" {{ old('municipio', $restaurante->municipio) == 'Íquira' ? 'selected' : '' }}>Íquira</option>
                                <option value="Isnos" {{ old('municipio', $restaurante->municipio) == 'Isnos' ? 'selected' : '' }}>Isnos</option>
                                <option value="La Argentina" {{ old('municipio', $restaurante->municipio) == 'La Argentina' ? 'selected' : '' }}>La Argentina</option>
                                <option value="La Plata" {{ old('municipio', $restaurante->municipio) == 'La Plata' ? 'selected' : '' }}>La Plata</option>
                                <option value="Nátaga" {{ old('municipio', $restaurante->municipio) == 'Nátaga' ? 'selected' : '' }}>Nátaga</option>
                                <option value="Neiva" {{ old('municipio', $restaurante->municipio) == 'Neiva' ? 'selected' : '' }}>Neiva</option>
                                <option value="Oporapa" {{ old('municipio', $restaurante->municipio) == 'Oporapa' ? 'selected' : '' }}>Oporapa</option>
                                <option value="Paicol" {{ old('municipio', $restaurante->municipio) == 'Paicol' ? 'selected' : '' }}>Paicol</option>
                                <option value="Palermo" {{ old('municipio', $restaurante->municipio) == 'Palermo' ? 'selected' : '' }}>Palermo</option>
                                <option value="Palestina" {{ old('municipio', $restaurante->municipio) == 'Palestina' ? 'selected' : '' }}>Palestina</option>
                                <option value="Pitalito" {{ old('municipio', $restaurante->municipio) == 'Pitalito' ? 'selected' : '' }}>Pitalito</option>
                                <option value="Rivera" {{ old('municipio', $restaurante->municipio) == 'Rivera' ? 'selected' : '' }}>Rivera</option>
                                <option value="Saladoblanco" {{ old('municipio', $restaurante->municipio) == 'Saladoblanco' ? 'selected' : '' }}>Saladoblanco</option>
                                <option value="San Agustín" {{ old('municipio', $restaurante->municipio) == 'San Agustín' ? 'selected' : '' }}>San Agustín</option>
                                <option value="Santa María" {{ old('municipio', $restaurante->municipio) == 'Santa María' ? 'selected' : '' }}>Santa María</option>
                                <option value="Suaza" {{ old('municipio', $restaurante->municipio) == 'Suaza' ? 'selected' : '' }}>Suaza</option>
                                <option value="Tarqui" {{ old('municipio', $restaurante->municipio) == 'Tarqui' ? 'selected' : '' }}>Tarqui</option>
                                <option value="Tesalia" {{ old('municipio', $restaurante->municipio) == 'Tesalia' ? 'selected' : '' }}>Tesalia</option>
                                <option value="Tello" {{ old('municipio', $restaurante->municipio) == 'Tello' ? 'selected' : '' }}>Tello</option>
                                <option value="Teruel" {{ old('municipio', $restaurante->municipio) == 'Teruel' ? 'selected' : '' }}>Teruel</option>
                                <option value="Timana" {{ old('municipio', $restaurante->municipio) == 'Timana' ? 'selected' : '' }}>Timana</option>
                                <option value="Villavieja" {{ old('municipio', $restaurante->municipio) == 'Villavieja' ? 'selected' : '' }}>Villavieja</option>
                                <option value="Yaguará" {{ old('municipio', $restaurante->municipio) == 'Yaguará' ? 'selected' : '' }}>Yaguará</option>
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
@extends('layouts.app')

@section('title', 'Crear Tour - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">🚌 Nuevo Tour</h4>
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

                <form action="{{ route('tours.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Tour</label>
                        <input type="text" name="nombre" class="form-control"
                            placeholder="Ej: Tour Desierto de la Tatacoa" value="{{ old('nombre') }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Agencias que ofrecen este tour (y su precio)</label>
                        @foreach(\App\Models\Agencia::all() as $agencia)
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-auto">
                                    <input type="checkbox" name="agencias[]" value="{{ $agencia->id }}" id="ag{{ $agencia->id }}" {{ collect(old('agencias'))->contains($agencia->id) ? 'checked' : '' }}>
                                </div>
                                <div class="col-4"><label for="ag{{ $agencia->id }}">{{ $agencia->nombre }}</label></div>
                                <div class="col-4">
                                    <input type="number" name="precios[{{ $agencia->id }}]" class="form-control form-control-sm" placeholder="Precio" step="0.01" min="0" value="{{ old("precios.$agencia->id") }}">
                                </div>
                            </div>
                        @endforeach
                        <div class="form-text"><a href="{{ route('agencias.create') }}" target="_blank">+ Crear nueva agencia</a></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4"
                            placeholder="Describe el tour...">{{ old('descripcion') }}</textarea>
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
                            <label class="form-label fw-bold">Duración (días)</label>
                            <input type="number" name="duracion_dias" class="form-control"
                                min="1" value="{{ old('duracion_dias', 1) }}">
                            @error('duracion_dias')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Precio por persona ($)</label>
                            <input type="number" name="precio" class="form-control"
                                placeholder="0" min="0" value="{{ old('precio') }}">
                            @error('precio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cupo máximo</label>
                            <input type="number" name="cupo_maximo" class="form-control"
                                min="1" value="{{ old('cupo_maximo', 10) }}">
                            @error('cupo_maximo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">¿Qué incluye?</label>
                        <textarea name="incluye" class="form-control" rows="3"
                            placeholder="Ej: Transporte, guía, almuerzo...">{{ old('incluye') }}</textarea>
                        @error('incluye')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono de Contacto</label>
                        <input type="text" name="telefono_contacto" class="form-control"
                            placeholder="Ej: +573001234567"
                            value="{{ old('telefono_contacto') }}">
                        @error('telefono_contacto')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (varias)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tours.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-success">Guardar Tour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
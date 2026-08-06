@extends('layouts.app')

@section('title', 'Lugares Turísticos - TurismoHuila')

@section('content')
<x-page-header titulo="Lugares turísticos" descripcion="Desde el desierto rojizo hasta el macizo colombiano: los rincones más icónicos del Huila.">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <button type="button" class="btn border-0" data-bs-toggle="modal" data-bs-target="#modalNuevoLugar" style="background-color: var(--color-primary); color:#fff;">+ Nuevo Lugar</button>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    @include('partials.filtro-municipio', ['rutaIndex' => 'lugares.index', 'municipios' => $municipios])

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($lugares as $lugar)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$lugar->imagen ? asset($lugar->imagen) : null"
                    :titulo="$lugar->nombre"
                    :ubicacion="$lugar->municipio . ' · ' . $lugar->categoria"
                    :rating="$lugar->resenas_avg_calificacion"
                    :descripcion="Str::limit($lugar->descripcion, 110)"
                    :url="route('lugares.show', $lugar)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('lugares.edit', $lugar) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('lugares.destroy', $lugar) : null"
                    favoritoTipo="lugar"
                    :favoritoId="$lugar->id"
                    :esFavorito="in_array($lugar->id, $favoritosIds ?? [])"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay lugares registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="modalNuevoLugar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('lugares.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nuevo Lugar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
                    <input type="text" name="municipio" class="form-control mb-2" placeholder="Municipio" required>
                    <input type="text" name="categoria" class="form-control mb-2" placeholder="Categoría" required>
                    <textarea name="descripcion" class="form-control mb-2" placeholder="Descripción" required></textarea>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="number" name="latitud" step="any" class="form-control" placeholder="Latitud">
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="number" name="longitud" step="any" class="form-control" placeholder="Longitud">
                        </div>
                    </div>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background-color: var(--color-primary); color:#fff;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    new bootstrap.Modal(document.getElementById('modalNuevoLugar')).show();
</script>
@endif
@endsection

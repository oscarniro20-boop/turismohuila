@extends('layouts.app')

@section('title', 'Mis Favoritos - TurismoHuila')

@section('content')
<x-page-header titulo="Mis favoritos" descripcion="Los lugares, hoteles, restaurantes, tours, guías y agencias que guardaste." />

<div class="container pb-5">
    @if($favoritos->isEmpty())
        <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
            Aún no tienes favoritos guardados.
        </div>
    @else
        @foreach(['lugar' => 'Lugares', 'hotel' => 'Hoteles', 'restaurante' => 'Restaurantes', 'tour' => 'Tours', 'guia' => 'Guías', 'agencia' => 'Agencias'] as $tipo => $titulo)
            @if($favoritos->has($tipo))
                <h4 class="mb-3 mt-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">{{ $titulo }}</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    @foreach($favoritos[$tipo] as $item)
                        <div class="col">
                            @if($tipo === 'guia')
                                <x-card-turismo
                                    :imagen="$item->url_imagen"
                                    :titulo="$item->nombre"
                                    :ubicacion="$item->zonas ?? 'Guía turístico certificado'"
                                    :descripcion="Str::limit($item->bio ?? $item->experiencia_anios . ' años de experiencia · ' . $item->idiomas, 110)"
                                    :url="route('guias.show', $item)"
                                    favoritoTipo="guia"
                                    :favoritoId="$item->id"
                                    :esFavorito="true"
                                />
                            @elseif($tipo === 'hotel')
                                <x-card-turismo
                                    :imagen="$item->url_imagen"
                                    :titulo="$item->nombre"
                                    :ubicacion="$item->municipio"
                                    :descripcion="Str::limit($item->descripcion, 110)"
                                    :precio="$item->precio_noche"
                                    precioLabel="/ noche"
                                    :url="route('hoteles.show', $item)"
                                    favoritoTipo="hotel"
                                    :favoritoId="$item->id"
                                    :esFavorito="true"
                                />
                            @elseif($tipo === 'restaurante')
                                <x-card-turismo
                                    :imagen="$item->url_imagen"
                                    :titulo="$item->nombre"
                                    :ubicacion="$item->municipio . ' · ' . $item->tipo_cocina"
                                    :descripcion="Str::limit($item->descripcion, 110)"
                                    :precio="$item->precio_promedio"
                                    precioLabel="promedio / persona"
                                    :url="route('restaurantes.show', $item)"
                                    favoritoTipo="restaurante"
                                    :favoritoId="$item->id"
                                    :esFavorito="true"
                                />
                            @elseif($tipo === 'tour')
                                <x-card-turismo
                                    :imagen="$item->url_imagen"
                                    :titulo="$item->nombre"
                                    :ubicacion="$item->municipio"
                                    :descripcion="Str::limit($item->descripcion, 110)"
                                    :precio="$item->precio"
                                    :precioLabel="$item->duracion_dias . ' día(s)'"
                                    :url="route('tours.show', $item)"
                                    favoritoTipo="tour"
                                    :favoritoId="$item->id"
                                    :esFavorito="true"
                                />
                            @elseif($tipo === 'agencia')
                                <x-card-turismo
                                    :imagen="$item->url_imagen"
                                    :titulo="$item->nombre"
                                    :ubicacion="$item->municipio"
                                    :descripcion="Str::limit($item->descripcion, 110)"
                                    :url="route('agencias.show', $item)"
                                    favoritoTipo="agencia"
                                    :favoritoId="$item->id"
                                    :esFavorito="true"
                                />
                            @else
                                <x-card-turismo
                                    :imagen="$item->url_imagen"
                                    :titulo="$item->nombre"
                                    :ubicacion="$item->municipio . ' · ' . $item->categoria"
                                    :descripcion="Str::limit($item->descripcion, 110)"
                                    :url="route('lugares.show', $item)"
                                    favoritoTipo="lugar"
                                    :favoritoId="$item->id"
                                    :esFavorito="true"
                                />
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
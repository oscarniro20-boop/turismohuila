@extends('layouts.app')

@section('title', 'Bienvenido - TurismoHuila')

@section('content')
<!-- Hero principal -->
<section class="position-relative" style="min-height: 70vh; background: url('{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}') center/cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.55), rgba(0,0,0,0.75));"></div>
    <div class="container position-relative h-100 d-flex align-items-center" style="min-height: 70vh;">
        <div class="text-white" style="max-width: 640px;">
            <span class="badge bg-warning text-dark mb-3">HUILA, COLOMBIA</span>
            <h1 class="display-4 fw-bold mb-3" style="font-family: 'Fraunces', Georgia, serif;">
                El Huila que <em style="color: var(--color-gold);">te espera</em>
            </h1>
            <p class="lead mb-4">Descubre paisajes, cultura y experiencias únicas en el corazón de Colombia.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('tours.index') }}" class="btn btn-lg rounded-pill px-4" style="background-color: var(--color-secondary); color:#fff; border-color: var(--color-secondary);">Ver tours</a>
                <a href="{{ route('lugares.index') }}" class="btn btn-lg rounded-pill px-4" style="color:#fff; border: 1px solid #fff;">Explorar lugares</a>
            </div>
        </div>
    </div>
</section>

    <div class="container py-5">
        <div class="row g-4 mb-5">
            @foreach([
                ['icon'=>'🏔️','titulo'=>'Naturaleza','desc'=>'Desiertos, cascadas y termales.','ruta'=>'lugares.index'],
                ['icon'=>'🏛️','titulo'=>'Cultura','desc'=>'San Agustín, arte precolombino.','ruta'=>'lugares.index'],
                ['icon'=>'🌊','titulo'=>'Aventura','desc'=>'Rafting, pesca y astroturismo.','ruta'=>'tours.index'],
            ] as $cat)
                <div class="col-md-4">
                    <a href="{{ route($cat['ruta']) }}" class="text-decoration-none d-block p-4 rounded-4 h-100 hover-lift" style="background: var(--color-muted); color: var(--color-fg);">
                        <div style="font-size: 2.5rem;">{{ $cat['icon'] }}</div>
                        <h4 class="mt-2 mb-1">{{ $cat['titulo'] }}</h4>
                        <p class="small mb-0" style="color: var(--color-muted-fg);">{{ $cat['desc'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            @foreach([
                ['icon'=>'🏞️','label'=>'Lugares','ruta'=>'lugares.index'],
                ['icon'=>'🏨','label'=>'Hoteles','ruta'=>'hoteles.index'],
                ['icon'=>'🍽️','label'=>'Restaurantes','ruta'=>'restaurantes.index'],
                ['icon'=>'🚌','label'=>'Tours','ruta'=>'tours.index'],
                ['icon'=>'🗺️','label'=>'Guías','ruta'=>'guias.index'],
                ['icon'=>'🧳','label'=>'Agencias','ruta'=>'agencias.index'],
            ] as $mod)
                <div class="col-6 col-md-2 text-center">
                    <a href="{{ route($mod['ruta']) }}" class="text-decoration-none d-block p-3 rounded-3" style="color: var(--color-fg); border: 1px solid var(--color-border);">
                        <div style="font-size: 1.75rem;">{{ $mod['icon'] }}</div>
                        <div class="small mt-1">{{ $mod['label'] }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

<!-- Sección: Todo lo que necesitas para tu viaje -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Todo lo que necesitas para tu viaje</h2>
        <div class="row g-3">
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('lugares.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <div class="display-5 mb-2" style="color: var(--color-primary);">🏞️</div>
                            <h6 class="mb-0" style="color: var(--color-fg);">Lugares turísticos</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('hoteles.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <div class="display-5 mb-2" style="color: var(--color-primary);">🏨</div>
                            <h6 class="mb-0" style="color: var(--color-fg);">Hoteles</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('restaurantes.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <div class="display-5 mb-2" style="color: var(--color-primary);">🍽️</div>
                            <h6 class="mb-0" style="color: var(--color-fg);">Restaurantes</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('tours.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <div class="display-5 mb-2" style="color: var(--color-primary);">🚌</div>
                            <h6 class="mb-0" style="color: var(--color-fg);">Tours</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('guias.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <div class="display-5 mb-2" style="color: var(--color-primary);">🗺️</div>
                            <h6 class="mb-0" style="color: var(--color-fg);">Guías</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('agencias.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <div class="display-5 mb-2" style="color: var(--color-primary);">🧳</div>
                            <h6 class="mb-0" style="color: var(--color-fg);">Agencias</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

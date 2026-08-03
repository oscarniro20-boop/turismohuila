@extends('layouts.app')

@section('content')
    <div class="position-relative" style="min-height: 560px; overflow: hidden;">
        @if($heroLugar && $heroLugar->imagen)
            <img src="{{ asset($heroLugar->imagen) }}" alt="{{ $heroLugar->nombre }}" class="position-absolute w-100 h-100" style="top:0; left:0; object-fit: cover;">
        @else
            <div class="position-absolute w-100 h-100" style="top:0; left:0; background-color: var(--color-primary);"></div>
        @endif
        <div class="position-absolute w-100 h-100" style="top:0; left:0; background: linear-gradient(180deg, rgba(19,45,30,0.65) 0%, rgba(19,45,30,0.45) 55%, rgba(19,45,30,0.75) 100%);"></div>
        <div class="position-relative container py-5" style="z-index: 2; min-height: 560px; display:flex; flex-direction:column; justify-content:center;">
            <div class="d-inline-flex align-items-center gap-2 mb-4 px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); width: fit-content; color: #fff; letter-spacing: 0.08em; font-size: 0.8rem; text-transform: uppercase;"><span aria-hidden="true">✨</span> Huila · Colombia</div>
            <h1 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; color: #fff; font-size: clamp(2.25rem, 5vw, 3.75rem); max-width: 720px; line-height: 1.1;">El Huila que <em style="color: var(--color-gold); font-style: italic;">enamora</em>.</h1>
            <p class="mt-3" style="color: rgba(255,255,255,0.85); max-width: 560px; font-size: 1.1rem;">Desde el desierto rojizo de la Tatacoa hasta las estatuas milenarias de San Agustín. Planea tu viaje con guías locales, tours curados y hoteles verificados.</p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="{{ route('tours.index') }}" class="btn d-inline-flex align-items-center gap-2 border-0" style="background-color: var(--color-secondary); color: #fff; border-radius: 2rem; padding: .75rem 1.75rem; font-weight: 500;">Reservar experiencia <span aria-hidden="true">→</span></a>
                <a href="{{ route('lugares.index') }}" class="btn" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.5); color: #fff; border-radius: 2rem; padding: .75rem 1.75rem; font-weight: 500;">Explorar lugares</a>
            </div>
        </div>
    </div>

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
@endsection

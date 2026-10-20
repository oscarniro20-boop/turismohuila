@extends('layouts.app')

@section('title', 'Bienvenido - Turismo por Colombia')

@section('content')
<!-- Hero principal -->
<section class="position-relative" style="min-height: 80vh; background: url('{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}') center/cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7));"></div>
    <div class="container position-relative h-100 d-flex align-items-center" style="min-height: 80vh;">
        <div class="text-white" style="max-width: 700px;">
            <span class="badge bg-warning text-dark mb-3 px-3 py-2">{{ __('ui.welcome.badge') }}</span>
            <h1 class="display-3 fw-bold mb-3" style="font-family: 'Fraunces', Georgia, serif; line-height: 1.2;">
                {{ __('ui.welcome.hero_title') }}
            </h1>
            <p class="lead mb-4 fs-5">{{ __('ui.welcome.hero_description') }}</p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('tours.index') }}" class="btn btn-lg rounded-pill px-4 py-3" style="background-color: var(--color-secondary); color:#fff; border-color: var(--color-secondary); box-shadow: 0 4px 15px rgba(0,0,0,0.2);">{{ __('ui.welcome.see_tours') }}</a>
                <a href="{{ route('lugares.index') }}" class="btn btn-lg rounded-pill px-4 py-3" style="color:#fff; border: 2px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">{{ __('ui.welcome.explore_places') }}</a>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Tres maneras de vivir el Huila -->
<section class="py-5" style="background: #fafafa;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">{{ __('ui.welcome.ways_title') }}</h2>
            <p class="lead text-muted mt-3">{{ __('ui.welcome.ways_description') }}</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px; transition: all 0.3s ease;">
                    <div class="position-relative overflow-hidden" style="height: 280px;">
                        <img src="{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}" alt="Naturaleza" class="w-100 h-100 object-fit-cover" style="transition: transform 0.5s ease;">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white">
                            <h3 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">{{ __('ui.welcome.nature') }}</h3>
                            <p class="small mb-3">{{ __('ui.welcome.nature_description') }}</p>
                            <a href="{{ route('lugares.index') }}" class="btn btn-light btn-sm rounded-pill px-4">{{ __('ui.common.explore') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px; transition: all 0.3s ease;">
                    <div class="position-relative overflow-hidden" style="height: 280px;">
                        <img src="{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}" alt="Cultura" class="w-100 h-100 object-fit-cover" style="transition: transform 0.5s ease;">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white">
                            <h3 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">{{ __('ui.welcome.culture') }}</h3>
                            <p class="small mb-3">{{ __('ui.welcome.culture_description') }}</p>
                            <a href="{{ route('lugares.index') }}" class="btn btn-light btn-sm rounded-pill px-4">{{ __('ui.common.explore') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px; transition: all 0.3s ease;">
                    <div class="position-relative overflow-hidden" style="height: 280px;">
                        <img src="{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}" alt="Aventura" class="w-100 h-100 object-fit-cover" style="transition: transform 0.5s ease;">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white">
                            <h3 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">{{ __('ui.welcome.adventure') }}</h3>
                            <p class="small mb-3">{{ __('ui.welcome.adventure_description') }}</p>
                            <a href="{{ route('tours.index') }}" class="btn btn-light btn-sm rounded-pill px-4">{{ __('ui.common.explore') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Todo lo que necesitas para tu viaje -->
<section class="py-5" style="background: #fff;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">{{ __('ui.welcome.needs_title') }}</h2>
            <p class="lead text-muted mt-3">{{ __('ui.welcome.needs_description') }}</p>
        </div>
        <div class="row g-4 row-cols-2 row-cols-md-4">
            <div class="col">
                <a href="{{ route('lugares.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🏞️</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.places') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.explore_places') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('hoteles.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🏨</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.hotels') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.needs_description') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('restaurantes.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🍽️</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.restaurants') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.culture_description') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('tours.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🚌</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.tours') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.ways_description') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('guias.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🗺️</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.guides') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.connection_description') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('agencias.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🧳</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.agencies') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.cta_description') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('transportes.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🚐</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">{{ __('ui.nav.transport') }}</h6>
                            <p class="small text-muted mb-3">{{ __('ui.welcome.connection_description') }}</p>
                            <span class="text-success fw-semibold small">{{ __('ui.common.explore') }} →</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Destinos de Colombia -->
<section class="py-5" style="background: #fafafa;">
    <div class="container py-5">
        <div class="text-center mb-4">
            <span class="d-block mb-2 small fw-semibold text-uppercase" style="color: var(--color-primary); letter-spacing: 0.1em;">{{ __('ui.welcome.destinos_kicker') }}</span>
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">{{ __('ui.welcome.destinos_title') }}</h2>
            <p class="lead text-muted mt-3 mb-4">{{ __('ui.welcome.destinos_description') }}</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
            @foreach(\App\Support\Colombia::nombresRegiones() as $region)
                <a href="{{ route('destinos.index') }}#{{ \Illuminate\Support\Str::slug($region) }}"
                   class="btn btn-sm rounded-pill px-3 py-2 border fw-medium text-nowrap"
                   style="border-color: var(--color-border); background: #fff; color: var(--color-fg);">
                    <span aria-hidden="true">{{ \App\Support\Colombia::infoRegion($region)['icono'] ?? '📍' }}</span> {{ $region }}
                </a>
            @endforeach
        </div>
        <div class="text-center">
            <a href="{{ route('destinos.index') }}" class="btn rounded-pill px-4 py-2" style="background-color: var(--color-primary); color: #fff;">
                {{ __('ui.welcome.see_destinos') }} →
            </a>
        </div>
    </div>
</section>

<!-- Sección: Estadísticas -->
<section class="py-5" style="background: var(--color-primary);">
    <div class="container py-5">
        <div class="text-center mb-4">
            <span class="d-block mb-2 small fw-semibold text-uppercase" style="color: #E8A33D; letter-spacing: 0.1em;">{{ __('ui.welcome.statistics') }}</span>
            <h2 class="fw-bold mb-3 text-white" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">
                {{ __('ui.welcome.connection_title') }}
            </h2>
            <p class="text-light small mb-0 mx-auto" style="max-width: 600px;">
                {{ __('ui.welcome.connection_description') }}
            </p>
        </div>
        <div class="row g-3 row-cols-2 row-cols-md-4 text-center text-white">
            <div class="col">
                <div class="rounded-4 p-4 h-100 d-flex flex-column align-items-center justify-content-center" style="background: rgba(255,255,255,0.08);">
                    <span class="mb-2" style="font-size: 2rem;">📍</span>
                    <div class="display-4 fw-bold" style="font-family: 'Fraunces', Georgia, serif;">{{ $totalExperiencias }}+</div>
                    <div class="small">{{ __('ui.welcome.experiences') }}</div>
                </div>
            </div>
            <div class="col">
                <div class="rounded-4 p-4 h-100 d-flex flex-column align-items-center justify-content-center" style="background: rgba(255,255,255,0.08);">
                    <span class="mb-2" style="font-size: 2rem;">🧑‍🤝‍🧑</span>
                    <div class="display-4 fw-bold" style="font-family: 'Fraunces', Georgia, serif;">7</div>
                    <div class="small">{{ __('ui.welcome.ways') }}</div>
                </div>
            </div>
            <div class="col">
                <div class="rounded-4 p-4 h-100 d-flex flex-column align-items-center justify-content-center" style="background: rgba(255,255,255,0.08);">
                    <span class="mb-2" style="font-size: 2rem;">⭐</span>
                    <div class="display-4 fw-bold" style="font-family: 'Fraunces', Georgia, serif;">{{ $calidadMedia }}</div>
                    <div class="small">{{ __('ui.welcome.average_reviews') }}</div>
                </div>
            </div>
            <div class="col">
                <div class="rounded-4 p-4 h-100 d-flex flex-column align-items-center justify-content-center" style="background: rgba(255,255,255,0.08);">
                    <span class="mb-2" style="font-size: 2rem;">🏛️</span>
                    <div class="display-4 fw-bold" style="font-family: 'Fraunces', Georgia, serif;">37</div>
                    <div class="small">{{ __('ui.welcome.municipalities') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Galería -->
<section class="py-5" style="background: #fafafa;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">{{ __('ui.welcome.gallery_title') }}</h2>
            <p class="lead text-muted mt-3">{{ __('ui.welcome.gallery_description') }}</p>
        </div>
        <div class="row g-3">
            @foreach($galeria as $img)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="position-relative overflow-hidden" style="border-radius: 16px; height: 250px; cursor: pointer;">
                        <img src="{{ str_starts_with($img->ruta, 'http') ? $img->ruta : asset($img->ruta) }}" alt="{{ $img->lugar ?? 'Huila' }}" class="w-100 h-100 object-fit-cover" style="transition: transform 0.5s ease;">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0); transition: background 0.3s ease;"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white" style="opacity: 0; transition: opacity 0.3s ease;">
                            <h6 class="fw-semibold mb-0">{{ $img->lugar ?? 'Huila' }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Sección: Testimonios -->
<section class="py-5" style="background: var(--color-bg);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">{{ __('ui.welcome.testimonials') }}</h2>
            <p class="lead text-muted mt-3">{{ __('ui.welcome.testimonials_description') }}</p>
        </div>
        @if($testimonios->isNotEmpty())
            <div class="row g-4">
                @foreach($testimonios as $testimonio)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px; transition: all 0.3s ease;">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <x-estrellas :calificacion="$testimonio->calificacion" size="1.1rem" />
                                </div>
                                <p class="mb-4" style="color: var(--color-fg);">&ldquo;{{ $testimonio->comentario }}&rdquo;</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: var(--color-primary); color: #fff; font-weight: 600; font-family: 'Fraunces', Georgia, serif;">
                                        {{ strtoupper(substr(optional($testimonio->user)->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <div class="text-truncate">
                                        <strong class="d-block text-truncate">{{ optional($testimonio->user)->name ?? 'Viajero' }}</strong>
                                        <small class="text-muted d-block text-truncate">{{ $testimonio->nombre_servicio }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted mb-0">{{ __('ui.welcome.no_testimonials') }}</p>
        @endif
    </div>
</section>

@guest
    <section class="py-5" style="background: linear-gradient(135deg, var(--color-secondary), var(--color-primary)); position: relative; overflow: hidden;">
        <div class="container text-center text-white position-relative" style="z-index: 2;">
            <h2 class="display-4 fw-bold mb-3" style="font-family: 'Fraunces', Georgia, serif;">
                {{ __('ui.welcome.cta_title') }}
            </h2>
            <p class="lead mb-4 fs-5">{{ __('ui.welcome.cta_description') }}</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold" style="box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                {{ __('ui.welcome.create_account') }}
            </a>
        </div>
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}') center/cover no-repeat; opacity: 0.1;"></div>
    </section>
@endguest

@endsection

@push('styles')
<style>
    /* Animaciones suaves */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeIn 0.6s ease-out;
    }

    /* Hover effects */
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }

    .card:hover img {
        transform: scale(1.1);
    }

    /* Galería hover */
    .position-relative:hover .position-absolute {
        background: rgba(0,0,0,0.4) !important;
    }

    .position-relative:hover .text-white {
        opacity: 1 !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .display-3 {
            font-size: 2rem;
        }
        
        .display-4 {
            font-size: 1.75rem;
        }
        
        .display-5 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Animación de aparición al hacer scroll
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeIn 0.6s ease-out';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Bienvenido - TurismoHuila')

@section('content')
<!-- Hero principal -->
<section class="position-relative" style="min-height: 80vh; background: url('{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}') center/cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.7));"></div>
    <div class="container position-relative h-100 d-flex align-items-center" style="min-height: 80vh;">
        <div class="text-white" style="max-width: 700px;">
            <span class="badge bg-warning text-dark mb-3 px-3 py-2">HUILA, COLOMBIA</span>
            <h1 class="display-3 fw-bold mb-3" style="font-family: 'Fraunces', Georgia, serif; line-height: 1.2;">
                El Huila que <em style="color: var(--color-gold);">te espera</em>
            </h1>
            <p class="lead mb-4 fs-5">Descubre paisajes, cultura y experiencias únicas en el corazón de Colombia.</p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('tours.index') }}" class="btn btn-lg rounded-pill px-4 py-3" style="background-color: var(--color-secondary); color:#fff; border-color: var(--color-secondary); box-shadow: 0 4px 15px rgba(0,0,0,0.2);">Ver tours</a>
                <a href="{{ route('lugares.index') }}" class="btn btn-lg rounded-pill px-4 py-3" style="color:#fff; border: 2px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">Explorar lugares</a>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Tres maneras de vivir el Huila -->
<section class="py-5" style="background: #fafafa;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">Tres maneras de vivir el Huila</h2>
            <p class="lead text-muted mt-3">Experiencias únicas te esperan en cada rincón</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px; transition: all 0.3s ease;">
                    <div class="position-relative overflow-hidden" style="height: 280px;">
                        <img src="{{ asset('imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg') }}" alt="Naturaleza" class="w-100 h-100 object-fit-cover" style="transition: transform 0.5s ease;">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white">
                            <h3 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">Naturaleza</h3>
                            <p class="small mb-3">Desiertos, cascadas y termales</p>
                            <a href="{{ route('lugares.index') }}" class="btn btn-light btn-sm rounded-pill px-4">Explorar</a>
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
                            <h3 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">Cultura</h3>
                            <p class="small mb-3">San Agustín, arte precolombino</p>
                            <a href="{{ route('lugares.index') }}" class="btn btn-light btn-sm rounded-pill px-4">Explorar</a>
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
                            <h3 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.75rem;">Aventura</h3>
                            <p class="small mb-3">Rafting, pesca y astroturismo</p>
                            <a href="{{ route('tours.index') }}" class="btn btn-light btn-sm rounded-pill px-4">Explorar</a>
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
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">Todo lo que necesitas para tu viaje</h2>
            <p class="lead text-muted mt-3">Encuentra todo lo que necesitas para una experiencia inolvidable</p>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('lugares.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🏞️</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">Lugares turísticos</h6>
                            <p class="small text-muted mb-3">Explora los principales destinos del Huila</p>
                            <span class="text-success fw-semibold small">Explorar →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('hoteles.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🏨</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">Hoteles</h6>
                            <p class="small text-muted mb-3">Encuentra el alojamiento perfecto</p>
                            <span class="text-success fw-semibold small">Explorar →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('restaurantes.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🍽️</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">Restaurantes</h6>
                            <p class="small text-muted mb-3">Degusta la gastronomía local</p>
                            <span class="text-success fw-semibold small">Explorar →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('tours.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🚌</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">Tours</h6>
                            <p class="small text-muted mb-3">Vive experiencias únicas</p>
                            <span class="text-success fw-semibold small">Explorar →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('guias.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🗺️</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">Guías</h6>
                            <p class="small text-muted mb-3">Expertos locales te acompañan</p>
                            <span class="text-success fw-semibold small">Explorar →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('agencias.index') }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm text-center py-4 px-3" style="border-radius: 16px; transition: all 0.3s ease; background: #f8f9fa;">
                        <div class="card-body">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px; background: rgba(var(--color-primary-rgb), 0.1);">
                                <span style="font-size: 2rem;">🧳</span>
                            </div>
                            <h6 class="fw-semibold mb-2" style="color: var(--color-fg);">Agencias</h6>
                            <p class="small text-muted mb-3">Planifica tu viaje perfecto</p>
                            <span class="text-success fw-semibold small">Explorar →</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Estadísticas -->
<section class="py-5" style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
    <div class="container py-5">
        <div class="row g-4 text-center text-white">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">🏞️</span>
                </div>
                 <div class="display-4 fw-bold">{{ $lugaresCount }}</div>
                <div class="small text-white-50">Lugares turísticos</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">🏨</span>
                </div>
                <div class="display-4 fw-bold">{{ $hotelesCount }}</div>
                <div class="small text-white-50">Hoteles</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">🍽️</span>
                </div>
                <div class="display-4 fw-bold">{{ $restaurantesCount }}</div>
                <div class="small text-white-50">Restaurantes</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">🚌</span>
                </div>
                <div class="display-4 fw-bold">{{ $toursCount }}</div>
                <div class="small text-white-50">Tours</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">🗺️</span>
                </div>
                <div class="display-4 fw-bold">{{ $guiasCount }}</div>
                <div class="small text-white-50">Guías</div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">🏢</span>
                </div>
                <div class="display-4 fw-bold">{{ $agenciasCount }}</div>
                <div class="small text-white-50">Agencias</div>
            </div>
        </div>
    </div>
</section>

<!-- Sección: Galería -->
<section class="py-5" style="background: #fafafa;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">Galería de experiencias</h2>
            <p class="lead text-muted mt-3">Descubre la belleza del Huila a través de nuestras imágenes</p>
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
            <h2 class="display-5 fw-bold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">Testimonios</h2>
            <p class="lead text-muted mt-3">Experiencias reales de quienes ya vivieron el Huila</p>
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
            <p class="text-center text-muted mb-0">Aún no hay testimonios. ¡Sé el primero en compartir tu experiencia!</p>
        @endif
    </div>
</section>

@guest
    <section class="py-5" style="background: linear-gradient(135deg, var(--color-secondary), var(--color-primary)); position: relative; overflow: hidden;">
        <div class="container text-center text-white position-relative" style="z-index: 2;">
            <h2 class="display-4 fw-bold mb-3" style="font-family: 'Fraunces', Georgia, serif;">
                ¿Listo para descubrir el Huila?
            </h2>
            <p class="lead mb-4 fs-5">Crea tu cuenta y comienza a planificar tu próxima aventura.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 py-3 fw-semibold" style="box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                Crear cuenta
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
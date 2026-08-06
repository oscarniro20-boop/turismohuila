@extends('layouts.app')

@section('title', 'Nosotros')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Sobre TurismoHuila</h1>
            <p class="lead text-muted mb-4">
                TurismoHuila es la plataforma que centraliza la oferta turística del departamento del Huila. 
                Conectamos a visitantes y operadores locales en un solo lugar, facilitando el descubrimiento 
                de lugares, hoteles, restaurantes, tours, guías y agencias verificadas.
            </p>

            <div class="row g-4 my-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">📍 Localidad</h5>
                            <p class="card-text text-muted">
                                Información curada por municipios y zonas del Huila, con datos prácticos 
                                para planificar tu visita.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🤝 Confianza</h5>
                            <p class="card-text text-muted">
                                Perfiles verificados y reseñas reales de usuarios para que elijas con seguridad.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🔍 Descubrimiento</h5>
                            <p class="card-text text-muted">
                                Explora opciones que se adaptan a tus gustos: naturaleza, cultura, gastronomía y aventura.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🌱 Sostenibilidad</h5>
                            <p class="card-text text-muted">
                                Promovemos el turismo responsable y el apoyo a emprendimientos locales del Huila.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-success mt-4">
                <h5 class="alert-heading">¿Eres operador turístico?</h5>
                <p class="mb-0">
                    Si tienes un hotel, restaurante, tour, agencia o servicio guiado, 
                    <a href="{{ route('contacto') }}" class="alert-link">escríbenos</a> 
                    para incluirte en la plataforma.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
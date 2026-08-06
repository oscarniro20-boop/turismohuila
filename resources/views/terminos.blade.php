@extends('layouts.app')

@section('title', 'Términos y Condiciones')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Términos y Condiciones</h1>

            <p class="text-muted mb-4">Última actualización: {{ date('Y-m-d') }}</p>

            <h2 class="h4 mt-4 mb-3">1. Aceptación de los términos</h2>
            <p>Al acceder y usar TurismoHuila, usted acepta cumplir con estos términos y condiciones. Si no está de acuerdo, le solicitamos no utilizar la plataforma.</p>

            <h2 class="h4 mt-4 mb-3">2. Descripción del servicio</h2>
            <p>TurismoHuila es una plataforma digital que centraliza información turística del departamento del Huila. Ofrecemos:</p>
            <ul>
                <li>Directorio de lugares, hoteles, restaurantes, tours, guías y agencias.</li>
                <li>Sistema de reseñas y calificaciones.</li>
                <li>Herramientas de contacto con prestadores de servicios.</li>
            </ul>
            <p>No vendemos directamente servicios turísticos; facilitamos la conexión entre usuarios y prestadores.</p>

            <h2 class="h4 mt-4 mb-3">3. Responsabilidades del usuario</h2>
            <p>Al publicar reseñas o comentarios, usted se compromete a:</p>
            <ul>
                <li>Proporcionar información veraz y basada en experiencias reales.</li>
                <li>No publicar contenido ofensivo, discriminatorio o spam.</li>
                <li>Respetar la propiedad intelectual y los derechos de terceros.</li>
            </ul>
            <p>Nos reservamos el derecho de eliminar contenido que incumpla estas normas.</p>

            <h2 class="h4 mt-4 mb-3">4. Limitación de responsabilidad</h2>
            <p>TurismoHuila no garantiza la disponibilidad, precios, calidad o exactitud de la información publicada por prestadores turísticos. No somos responsables de:</p>
            <ul>
                <li>Cambios de precio o disponibilidad de servicios.</li>
                <li>Experiencias negativas derivadas de servicios contratados con terceros.</li>
                <li>Interrupciones del servicio por mantenimiento o causas técnicas.</li>
            </ul>

            <h2 class="h4 mt-4 mb-3">5. Cambios en los términos</h2>
            <p>Podemos actualizar estos términos ocasionalmente. Le notificaremos sobre cambios significativos mediante un aviso en la plataforma. El uso continuado del sitio implica aceptación de los nuevos términos.</p>

            <h2 class="h4 mt-4 mb-3">6. Contacto</h2>
            <p>Para consultas sobre estos términos, contáctenos en <a href="{{ route('contacto') }}">nuestra página de contacto</a> o al correo <a href="mailto:info@turismohuila.com">info@turismohuila.com</a>.</p>
        </div>
    </div>
</div>
@endsection
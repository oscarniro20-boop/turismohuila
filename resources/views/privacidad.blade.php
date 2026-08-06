@extends('layouts.app')

@section('title', 'Política de Privacidad')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Política de Privacidad</h1>

            <p class="text-muted mb-4">Última actualización: {{ date('Y-m-d') }}</p>

            <h2 class="h4 mt-4 mb-3">1. Información que recopilamos</h2>
            <p>En TurismoHuila recopilamos información que usted proporciona directamente:</p>
            <ul>
                <li><strong>Datos de registro:</strong> nombre, correo electrónico y contraseña cuando crea una cuenta.</li>
                <li><strong>Reseñas y comentarios:</strong> calificaciones, comentarios y fotos que publique sobre lugares, hoteles, restaurantes, tours, guías o agencias.</li>
                <li><strong>Mensajes de contacto:</strong> nombre, correo y contenido cuando usa el formulario de contacto.</li>
            </ul>

            <h2 class="h4 mt-4 mb-3">2. Cómo usamos su información</h2>
            <p>Utilizamos sus datos para:</p>
            <ul>
                <li>Operar y mantener la plataforma.</li>
                <li>Permitirle publicar reseñas y guardar favoritos.</li>
                <li>Responder a sus consultas a través del formulario de contacto.</li>
                <li>Mejorar la experiencia de usuario y la calidad del contenido.</li>
            </ul>

            <h2 class="h4 mt-4 mb-3">3. Almacenamiento y seguridad</h2>
            <p>Sus datos se almacenan en servidores seguros. Implementamos medidas técnicas y organizativas para proteger su información contra acceso no autorizado, pérdida o alteración. Sin embargo, ningún sistema es completamente seguro.</p>

            <h2 class="h4 mt-4 mb-3">4. Cookies</h2>
            <p>Utilizamos cookies esenciales de sesión para el funcionamiento básico del sitio (autenticación, preferencias de navegación). No usamos cookies de seguimiento publicitario de terceros.</p>

            <h2 class="h4 mt-4 mb-3">5. Derechos del usuario</h2>
            <p>Usted puede solicitar la eliminación de su cuenta o de contenido publicado escribiéndonos a través de la página de <a href="{{ route('contacto') }}">Contacto</a>. Procesaremos su solicitud en un plazo razonable.</p>

            <h2 class="h4 mt-4 mb-3">6. Contacto</h2>
            <p>Para consultas sobre privacidad, escríbanos a <a href="mailto:info@turismohuila.com">info@turismohuila.com</a>.</p>
        </div>
    </div>
</div>
@endsection
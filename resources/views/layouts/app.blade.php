<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TurismoHuila</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    @include('components.navbar')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="mt-auto position-relative overflow-hidden" style="background-color: var(--color-primary); color: #fff;">
        <div class="glow-decor" style="top:-140px; left:50%; transform:translateX(-50%); width:380px; height:380px; background: var(--color-gold); opacity:0.2;"></div>
        <div class="container py-4 position-relative" style="z-index:1;">
            <div class="row g-4">
                <!-- Col 1: TurismoHuila -->
                <div class="col-md-3">
                    <h6 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">TurismoHuila</h6>
                    <p class="mb-0 small" style="opacity: 0.9;">Descubre las maravillas del Huila. Naturaleza, cultura y aventura en un solo lugar.</p>
                </div>
                <!-- Col 2: PLATAFORMA -->
                <div class="col-md-3">
                    <h6 class="mb-2 eyebrow" style="color: var(--color-gold);">Plataforma</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><a href="{{ route('nosotros') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Nosotros</a></li>
                        <li><a href="{{ route('contacto') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Contacto</a></li>
                        <li><a href="{{ route('mapa') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Mapa interactivo</a></li>
                    </ul>
                </div>
                <!-- Col 3: EXPLORAR -->
                <div class="col-md-3">
                    <h6 class="mb-2 eyebrow" style="color: var(--color-gold);">Explorar</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><a href="{{ route('lugares.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Lugares turísticos</a></li>
                        <li><a href="{{ route('hoteles.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Hoteles</a></li>
                        <li><a href="{{ route('tours.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Tours</a></li>
                        <li><a href="{{ route('guias.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Guías</a></li>
                        <li><a href="{{ route('agencias.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Agencias</a></li>
                    </ul>
                </div>
                <!-- Col 4: LEGAL -->
                <div class="col-md-3">
                    <h6 class="mb-2 eyebrow" style="color: var(--color-gold);">Legal</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><a href="{{ route('terminos') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Términos de uso</a></li>
                        <li><a href="{{ route('privacidad') }}" class="text-white text-decoration-none" style="opacity: 0.9;">Política de privacidad</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
            <p class="mb-0 small text-center" style="opacity: 0.8;">
                © {{ date('Y') }} TurismoHuila. Todos los derechos reservados.
            </p>
        </div>
    </footer>
    @include('components.modal-busqueda')
    @stack('scripts')
</body>
</html>
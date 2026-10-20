<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Turismo por Colombia</title>
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
                <!-- Col 1: Turismo por Colombia -->
                <div class="col-md-3">
                    <h6 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Turismo por Colombia</h6>
                    <p class="mb-0 small" style="opacity: 0.9;">{{ __('ui.footer.description') }}</p>
                </div>
                <!-- Col 2: PLATAFORMA -->
                <div class="col-md-3">
                    <h6 class="mb-2 eyebrow" style="color: var(--color-gold);">{{ __('ui.footer.platform') }}</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><a href="{{ route('nosotros') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.footer.about') }}</a></li>
                        <li><a href="{{ route('contacto') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.footer.contact') }}</a></li>
                        <li><a href="{{ route('mapa') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.footer.interactive_map') }}</a></li>
                    </ul>
                </div>
                <!-- Col 3: EXPLORAR -->
                <div class="col-md-3">
                    <h6 class="mb-2 eyebrow" style="color: var(--color-gold);">{{ __('ui.footer.explore') }}</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><a href="{{ route('lugares.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.places') }}</a></li>
                        <li><a href="{{ route('hoteles.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.hotels') }}</a></li>
                        <li><a href="{{ route('restaurantes.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.restaurants') }}</a></li>
                        <li><a href="{{ route('tours.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.tours') }}</a></li>
                        <li><a href="{{ route('guias.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.guides') }}</a></li>
                        <li><a href="{{ route('agencias.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.agencies') }}</a></li>
                        <li><a href="{{ route('transportes.index') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.nav.transport') }}</a></li>
                    </ul>
                </div>
                <!-- Col 4: LEGAL -->
                <div class="col-md-3">
                    <h6 class="mb-2 eyebrow" style="color: var(--color-gold);">{{ __('ui.footer.legal') }}</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><a href="{{ route('terminos') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.footer.terms') }}</a></li>
                        <li><a href="{{ route('privacidad') }}" class="text-white text-decoration-none" style="opacity: 0.9;">{{ __('ui.footer.privacy') }}</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
            <p class="mb-0 small text-center" style="opacity: 0.8;">
                © {{ date('Y') }} Turismo por Colombia. {{ __('ui.footer.rights') }}
            </p>
        </div>
    </footer>
    @include('components.modal-busqueda')
    @stack('scripts')
</body>
</html>
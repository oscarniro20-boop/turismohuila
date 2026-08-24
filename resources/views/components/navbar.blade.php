<nav class="sticky-top" style="background-color: color-mix(in oklab, var(--color-bg) 75%, transparent); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between py-3">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background-color: var(--color-primary); color:#fff; font-family:'Fraunces', Georgia, serif;">H</span>
                <span style="font-family:'Fraunces', Georgia, serif; font-weight:600; font-size:1.15rem;"><span style="color: var(--color-fg);">Turismo</span><span style="color: var(--color-primary);">Huila</span></span>
            </a>
            @php
                $navItems = [
                    'mapa' => ['label' => 'Mapa', 'match' => 'mapa*'],
                    'lugares.index' => ['label' => 'Lugares', 'match' => 'lugares*'],
                    'hoteles.index' => ['label' => 'Hoteles', 'match' => 'hoteles*'],
                    'restaurantes.index' => ['label' => 'Restaurantes', 'match' => 'restaurantes*'],
                    'tours.index' => ['label' => 'Tours', 'match' => 'tours*'],
                    'guias.index' => ['label' => 'Guías', 'match' => 'guias*'],
                    'agencias.index' => ['label' => 'Agencias', 'match' => 'agencias*'],
                ];
            @endphp
            <div class="d-none d-lg-flex align-items-center gap-1">
                @foreach($navItems as $route => $item)
                    <a href="{{ route($route) }}" class="px-3 py-2 text-decoration-none small fw-medium rounded-3" style="color: {{ request()->is($item['match']) ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is($item['match']) ? 'var(--color-muted)' : 'transparent' }};">{{ $item['label'] }}</a>
                @endforeach
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('resenas.index') }}" class="px-3 py-2 text-decoration-none small fw-medium rounded-3" style="color: {{ request()->is('resenas*') ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is('resenas*') ? 'var(--color-muted)' : 'transparent' }};">Reseñas</a>
                    @endif
                @endauth
                <button class="btn btn-sm border-0 px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalBusqueda" style="color: var(--color-muted-fg);">
                    <span class="small fw-medium">Buscar</span>
                </button>
            </div>
            <div class="d-none d-lg-flex align-items-center gap-3">
                @auth
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none small fw-medium dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--color-fg);">
                            {{ auth()->user()->esAdmin() ? 'admin' : explode(' ', auth()->user()->name)[0] }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 py-2 mt-1" style="min-width: 200px;">
                            <li><a href="{{ route('profile.edit') }}" class="dropdown-item small py-2">Mi perfil</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item small py-2">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm border-0 small" style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .4rem 1rem;">Ingresar</a>
                @endauth
            </div>
            <div class="d-lg-none d-flex align-items-center gap-1">
                <button class="border-0 bg-transparent p-2" type="button" data-bs-toggle="modal" data-bs-target="#modalBusqueda" aria-label="Buscar" style="color: var(--color-fg);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
                <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="border-0 bg-transparent p-2 d-inline-flex" aria-label="Perfil" style="color: var(--color-fg);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                                <button id="menuToggleBtn" type="button" class="border-0 bg-transparent p-2" aria-label="Abrir menú de navegación" aria-controls="menuOverlay" aria-expanded="false" style="color: var(--color-fg);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

{{-- Overlay móvil a pantalla completa --}}
<div id="menuOverlay" class="d-lg-none" role="dialog" aria-modal="true" aria-label="Menú de navegación" tabindex="-1" style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:1060; background-color:var(--color-bg); overflow-y:auto;">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-3">
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" data-cierre-menu>
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background-color: var(--color-primary); color:#fff; font-family:'Fraunces', Georgia, serif;">H</span>
                    <span style="font-family:'Fraunces', Georgia, serif; font-weight:600; font-size:1.15rem;"><span style="color: var(--color-fg);">Turismo</span><span style="color: var(--color-primary);">Huila</span></span>
                </a>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="border-0 bg-transparent p-2" data-bs-toggle="modal" data-bs-target="#modalBusqueda" data-cierre-menu aria-label="Buscar" style="color: var(--color-fg);">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="border-0 bg-transparent p-2 d-inline-flex" data-cierre-menu aria-label="Perfil" style="color: var(--color-fg);">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                    <button type="button" class="btn border-0 p-2 fs-4 lh-1" id="menuCloseBtn" aria-label="Cerrar menú" data-cierre-menu style="color: var(--color-fg);">&times;</button>
                </div>
            </div>

                        <div class="d-flex flex-column gap-2 pb-4">
                @foreach($navItems as $route => $item)
                    <a href="{{ route($route) }}" class="px-3 py-3 text-decoration-none rounded-3 fw-medium" data-cierre-menu style="color: {{ request()->is($item['match']) ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is($item['match']) ? 'var(--color-muted)' : 'transparent' }}; font-size: 1.05rem;">{{ $item['label'] }}</a>
                @endforeach
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('resenas.index') }}" class="px-3 py-3 text-decoration-none rounded-3 fw-medium" data-cierre-menu style="color: {{ request()->is('resenas*') ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is('resenas*') ? 'var(--color-muted)' : 'transparent' }}; font-size: 1.05rem;">Reseñas</a>
                    @endif
                @endauth
                <button type="button" class="btn border-0 text-start px-3 py-3 fw-medium" data-bs-toggle="modal" data-bs-target="#modalBusqueda" data-cierre-menu style="color: var(--color-muted-fg); background-color: transparent; font-size: 1.05rem;">Buscar</button>

                <hr class="my-3" style="border-color: var(--color-border);">

                @auth
                    <a href="{{ route('profile.edit') }}" class="px-3 py-3 text-decoration-none rounded-3 fw-medium" data-cierre-menu style="color: var(--color-muted-fg); font-size: 1.05rem;">Mi perfil</a>
                    <form method="POST" action="{{ route('logout') }}" class="px-3 mt-2">
                        @csrf
                        <button type="submit" class="btn w-100 border-0 fw-medium" data-cierre-menu style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1rem;">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="mx-3 btn border-0 text-center fw-medium" data-cierre-menu style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1rem;">Ingresar</a>
                @endauth
            </div>
        </div>
    </div>

<style>
    #menuOverlay {
        visibility: hidden;
        opacity: 0;
        transition: opacity .2s ease;
    }
    #menuOverlay.show {
        visibility: visible;
        opacity: 1;
    }
    body.menu-open { overflow: hidden; }
</style>

@once('scripts')
<script>
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('menuOverlay');
            if (!overlay) return;

            const toggleBtn = document.getElementById('menuToggleBtn');

            const abrir = function () {
                const prev = document.activeElement;
                if (toggleBtn && prev !== toggleBtn && prev && prev.id) {
                    overlay.dataset.returnFocus = prev.id;
                }
                overlay.classList.add('show');
                document.body.classList.add('menu-open');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    toggleBtn.setAttribute('aria-label', 'Cerrar menú');
                    toggleBtn.focus();
                }
            };

            const cerrar = function () {
                overlay.classList.remove('show');
                document.body.classList.remove('menu-open');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.setAttribute('aria-label', 'Abrir menú de navegación');
                }
                const rid = overlay.dataset.returnFocus;
                if (rid) {
                    const rb = document.getElementById(rid);
                    if (rb) { rb.focus({ preventScroll: true }); }
                    delete overlay.dataset.returnFocus;
                }
            };

            const toggle = function (e) {
                if (e) { e.preventDefault(); e.stopPropagation(); }
                overlay.classList.contains('show') ? cerrar() : abrir();
            };

            // Abrir/cerrar desde la hamburguesa
            toggleBtn && toggleBtn.addEventListener('click', toggle);

            // Cerrar al click en el fondo del overlay (fuera del contenido)
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) cerrar();
            });

            // Cerrar con Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('show')) {
                    cerrar();
                }
            });

            // Cerrar al pulsar cualquier elemento marcado con data-cierre-menu
            overlay.querySelectorAll('[data-cierre-menu]').forEach(function (el) {
                el.addEventListener('click', cerrar);
            });

            // Al abrir el modal de búsqueda, cerrar el overlay para que el modal
            // (z-index 1050) quede por encima del overlay (z-index 1060).
            const modal = document.getElementById('modalBusqueda');
            if (modal) {
                modal.addEventListener('show.bs.modal', cerrar);
            }
        });
    })();
</script>
@endonce

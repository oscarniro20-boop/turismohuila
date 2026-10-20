<nav class="navbar navbar-expand-xl sticky-top" style="background-color: color-mix(in oklab, var(--color-bg) 75%, transparent); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 p-0 my-2" href="{{ route('home') }}">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background-color: var(--color-primary); color:#fff; font-family:'Fraunces', Georgia, serif;">H</span>
            <span style="font-family:'Fraunces', Georgia, serif; font-weight:600; font-size:1.15rem;"><span style="color: var(--color-fg);">Turismo</span><span style="color: var(--color-primary);"> por Colombia</span></span>
        </a>
        @php
            $navItems = [
                'destinos.index' => ['label' => __('ui.nav.destinos'), 'match' => 'destinos*'],
                'mapa' => ['label' => __('ui.nav.map'), 'match' => 'mapa*'],
                'lugares.index' => ['label' => __('ui.nav.places'), 'match' => 'lugares*'],
                'hoteles.index' => ['label' => __('ui.nav.hotels'), 'match' => 'hoteles*'],
                'restaurantes.index' => ['label' => __('ui.nav.restaurants'), 'match' => 'restaurantes*'],
                'tours.index' => ['label' => __('ui.nav.tours'), 'match' => 'tours*'],
                'guias.index' => ['label' => __('ui.nav.guides'), 'match' => 'guias*'],
                'agencias.index' => ['label' => __('ui.nav.agencies'), 'match' => 'agencias*'],
                'transportes.index' => ['label' => __('ui.nav.transport'), 'match' => 'transportes*'],
            ];
        @endphp
        <button class="navbar-toggler border-0 ms-auto d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menú de navegación">
            <span class="navbar-toggler-icon"></span>
        </button>
        {{-- flex-wrap (antes flex-nowrap): cerca de ~1200 px el menú completo
             no cabe en una fila y el desbordamiento horizontal empujaba la
             barra; con wrap, la zona de acciones baja de fila en su lugar. --}}
        <div class="collapse navbar-collapse flex-wrap" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-xl-0 align-items-xl-center gap-xl-2 flex-nowrap">
                @foreach($navItems as $route => $item)
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 small fw-medium rounded-3 text-nowrap {{ request()->is($item['match']) ? 'active' : '' }}" href="{{ route($route) }}" style="color: {{ request()->is($item['match']) ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is($item['match']) ? 'var(--color-muted)' : 'transparent' }};">{{ $item['label'] }}</a>
                    </li>
                @endforeach
                @auth
                    @if(auth()->user()->esAdmin())
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 small fw-medium rounded-3 text-nowrap {{ request()->is('resenas*') ? 'active' : '' }}" href="{{ route('resenas.index') }}" style="color: {{ request()->is('resenas*') ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is('resenas*') ? 'var(--color-muted)' : 'transparent' }};">{{ __('ui.nav.reviews') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 small fw-medium rounded-3 text-nowrap {{ request()->is('admin') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" style="color: {{ request()->is('admin') ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is('admin') ? 'var(--color-muted)' : 'transparent' }};">{{ __('ui.nav.admin') }}</a>
                        </li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-2 flex-nowrap">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 small fw-medium d-inline-block text-nowrap" href="#" data-bs-toggle="modal" data-bs-target="#modalBusqueda" style="color: var(--color-muted-fg);">
                        {{ __('ui.nav.search') }}
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 small fw-medium" href="#" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--color-muted-fg);">
                        {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 py-2">
                        <li>
                            <form method="POST" action="{{ route('locale.update') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ app()->getLocale() === 'es' ? 'en' : 'es' }}">
                                <button type="submit" class="dropdown-item small py-2">
                                    {{ app()->getLocale() === 'es' ? __('ui.english') : __('ui.spanish') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 small fw-medium" href="#" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--color-fg);">
                            <span>{{ auth()->user()->esAdmin() ? 'admin' : explode(' ', auth()->user()->name)[0] }}</span>
                            @if(auth()->user()->esAdmin())
                                <span class="badge rounded-pill" style="background-color: var(--color-primary); color:#fff; font-size:.65rem; font-weight:600; padding:.25em .55em;">ADMIN</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-xl-start shadow-sm rounded-3 border-0 py-2 mt-xl-1" style="min-width: 200px;">
                            <li><a href="{{ route('profile.edit') }}" class="dropdown-item small py-2">{{ __('ui.nav.profile') }}</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item small py-2">{{ __('ui.nav.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn border-0 fw-medium my-2 my-xl-0" style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .45rem 1rem;">{{ __('ui.nav.login') }}</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
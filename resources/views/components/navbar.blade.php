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
            </div>
            <div class="d-none d-lg-flex align-items-center gap-3">
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('resenas.index') }}" class="small text-decoration-none" style="color: var(--color-muted-fg);">Reseñas</a>
                    @endif
                    <a href="{{ route('resenas.mis') }}" class="small text-decoration-none" style="color: var(--color-muted-fg);">Mis Reseñas</a>
                    <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none small fw-medium" style="color: var(--color-fg);"><span aria-hidden="true">👤</span>{{ auth()->user()->esAdmin() ? 'admin' : explode(' ', auth()->user()->name)[0] }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf<button type="submit" class="btn btn-sm border-0 small" style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .4rem 1rem;">Salir</button></form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm border-0 small" style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .4rem 1rem;">Ingresar</a>
                @endauth
            </div>
            <button class="navbar-toggler d-lg-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPrincipal" aria-controls="navPrincipal" aria-expanded="false" aria-label="Abrir menú de navegación"><span class="navbar-toggler-icon"></span></button>
        </div>
        <div class="collapse d-lg-none pb-3" id="navPrincipal">
            <div class="d-flex flex-column gap-1">
                @foreach($navItems as $route => $item)
                    <a href="{{ route($route) }}" class="px-3 py-2 text-decoration-none rounded-3" style="color: {{ request()->is($item['match']) ? 'var(--color-fg)' : 'var(--color-muted-fg)' }}; background-color: {{ request()->is($item['match']) ? 'var(--color-muted)' : 'transparent' }};">{{ $item['label'] }}</a>
                @endforeach
                <hr class="my-2" style="border-color: var(--color-border);">
                @auth
                    <a href="{{ route('resenas.mis') }}" class="px-3 py-2 text-decoration-none" style="color: var(--color-muted-fg);">Mis Reseñas</a>
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-decoration-none" style="color: var(--color-fg);">Mi Perfil</a>
                    @if(auth()->user()->esAdmin())<a href="{{ route('resenas.index') }}" class="px-3 py-2 text-decoration-none" style="color: var(--color-fg);">Reseñas</a>@endif
                    <form method="POST" action="{{ route('logout') }}" class="px-3">@csrf<button type="submit" class="btn w-100 border-0" style="background-color: var(--color-primary); color:#fff; border-radius: 2rem;">Salir</button></form>
                @else
                    <a href="{{ route('login') }}" class="mx-3 btn border-0 text-center" style="background-color: var(--color-primary); color:#fff; border-radius: 2rem;">Ingresar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

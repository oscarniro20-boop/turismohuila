@props(['items' => []])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}" class="migas-link" style="color: var(--color-muted-fg); text-decoration: none;">Inicio</a>
        </li>
        @foreach($items as $item)
            @if(isset($item['url']))
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}" class="migas-link" style="color: var(--color-muted-fg); text-decoration: none;">{{ $item['label'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--color-fg);">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
@props(['calificacion' => 0, 'size' => '1rem'])
<span class="d-inline-flex align-items-center gap-1">
    @for($i = 1; $i <= 5; $i++)
        <span aria-hidden="true" style="color: var(--color-gold); font-size: {{ $size }};">{{ $i <= round($calificacion) ? '★' : '☆' }}</span>
    @endfor
</span>
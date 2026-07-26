@props(['calificacion' => 0])
<span class="d-inline-flex align-items-center gap-1">
    @for($i = 1; $i <= 5; $i++)
        <span aria-hidden="true" style="color: var(--color-gold); font-size: {{ $size ?? '1rem' }};">{{ $i <= round($calificacion) ? '★' : '☆' }}</span>
    @endfor
</span>
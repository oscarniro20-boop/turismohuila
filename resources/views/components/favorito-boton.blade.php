@props(['tipo', 'id', 'activo' => false])

<button type="button"
        class="favorito-btn {{ $activo ? 'activo' : '' }} d-flex align-items-center justify-content-center rounded-circle border-0"
        data-favorito-tipo="{{ $tipo }}"
        data-favorito-id="{{ $id }}"
        title="Favorito"
        style="width:32px; height:32px; background: rgba(255,255,255,0.94); font-size: 1rem;">
    {{ $activo ? '❤️' : '🤍' }}
</button>
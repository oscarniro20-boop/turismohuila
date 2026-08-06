@props(['titulo', 'url' => null])

@php
    $url = $url ?? url()->current();
@endphp

<div class="dropdown d-inline-block">
    <button type="button"
            class="boton-compartir d-flex align-items-center justify-content-center rounded-circle border-0"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="Compartir"
            style="width:32px; height:32px; background: rgba(255,255,255,0.94); font-size: 1rem;">
        📲
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a href="https://wa.me/?text={{ urlencode($titulo . ' ' . $url) }}"
               target="_blank" rel="noopener noreferrer"
               class="dropdown-item d-flex align-items-center gap-2">
                <span>📲</span> WhatsApp
            </a>
        </li>
        <li>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
               target="_blank" rel="noopener noreferrer"
               class="dropdown-item d-flex align-items-center gap-2">
                <span>📘</span> Facebook
            </a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <button type="button"
                    onclick="copiarEnlace('{{ $url }}')"
                    class="dropdown-item d-flex align-items-center gap-2">
                <span>🔗</span> Copiar enlace
            </button>
        </li>
    </ul>
</div>

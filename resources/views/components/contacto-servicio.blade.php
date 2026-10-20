@props([
    'telefono' => null,
    'email' => null,
    'whatsapp' => null,
    'texto' => null,
])

{{-- Componente único de contacto para páginas de detalle.
     Regla de datos: si el servicio no tiene teléfono ni correo, no se renderiza. --}}
@if($telefono || $email || $whatsapp)
@php
    $numWa = preg_replace('/\D/', '', $whatsapp ?? $telefono ?? '');
@endphp
<div class="rounded-4 p-4 mb-4 mt-3"
     style="background: #fff; border: 1px solid var(--color-border); box-shadow: var(--shadow-ring);">
    <span class="d-block mb-1 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">{{ __('ui.contact.plan') }}</span>
    <h3 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem;">{{ __('ui.contact.interested') }}</h3>
    <p class="small mb-3" style="color: var(--color-muted-fg);">{{ $texto ?: __('ui.contact.default') }}</p>
    <div class="d-flex flex-column gap-2">
        @if($telefono || $whatsapp)
            <a href="https://wa.me/57{{ $numWa }}" target="_blank" rel="noopener"
               class="btn rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 py-2"
               style="background: var(--color-primary); color: #fff;">
                💬 {{ __('ui.contact.whatsapp') }}
            </a>
        @endif
        @if($telefono)
            <a href="tel:{{ $telefono }}"
               class="btn rounded-pill d-flex align-items-center justify-content-center gap-2 py-2"
               style="border: 1px solid var(--color-border); color: var(--color-fg);">
                📞 {{ __('ui.contact.call') }}
            </a>
        @endif
        @if($email)
            <a href="mailto:{{ $email }}"
               class="btn rounded-pill d-flex align-items-center justify-content-center gap-2 py-2"
               style="border: 1px solid var(--color-border); color: var(--color-fg);">
                ✉️ {{ __('ui.contact.email') }}
            </a>
        @endif
    </div>
</div>
@endif
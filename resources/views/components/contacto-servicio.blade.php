@props([
    'telefono' => null,
    'email' => null,
    'whatsapp' => null,
    'texto' => '¿Interesado? Contacta directamente para reservar o preguntar disponibilidad.',
])

{{-- Componente único de contacto para páginas de detalle.
     Regla de datos: si el servicio no tiene teléfono ni correo, no se renderiza. --}}
@if($telefono || $email || $whatsapp)
@php
    $numWa = preg_replace('/\D/', '', $whatsapp ?? $telefono ?? '');
@endphp
<div class="rounded-4 p-4 mb-4 mt-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3"
     style="background: #fff; border: 1px solid var(--color-border); box-shadow: var(--shadow-ring);">
    <div>
        <h3 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.15rem;">¿Interesado?</h3>
        <p class="small mb-0" style="color: var(--color-muted-fg);">{{ $texto }}</p>
    </div>
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 flex-shrink-0">
        @if($telefono || $whatsapp)
            <a href="https://wa.me/57{{ $numWa }}" target="_blank" rel="noopener"
               class="btn rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 px-4 py-2"
               style="background: var(--color-primary); color: #fff; white-space: nowrap;">
                💬 Contactar por WhatsApp
            </a>
            @if($telefono)
                <span class="small text-center" style="color: var(--color-muted-fg); white-space: nowrap;">📞 {{ $telefono }}</span>
            @endif
        @endif
        @if($email)
            <a href="mailto:{{ $email }}" class="btn rounded-pill py-2 px-4"
               style="border: 1px solid var(--color-border); color: var(--color-fg); white-space: nowrap;">
                ✉️ Enviar correo
            </a>
        @endif
    </div>
</div>
@endif
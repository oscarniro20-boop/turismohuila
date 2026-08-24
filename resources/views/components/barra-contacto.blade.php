@props([
    'telefono' => null,
    'email' => null,
    'texto' => '¿Interesado? Contacta directamente para reservar o preguntar disponibilidad.',
])

@if($telefono || $email)
<div class="rounded-4 p-4 mb-4 mt-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background: #fff; box-shadow: var(--shadow-ring);">
    <div>
        <h3 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.15rem;">¿Interesado?</h3>
        <p class="small mb-0" style="color: var(--color-muted-fg);">{{ $texto }}</p>
    </div>
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 flex-shrink-0">
        @if($telefono)
            @php $numero = preg_replace('/\D/', '', $telefono); @endphp
            <a href="https://wa.me/57{{ $numero }}" target="_blank" rel="noopener"
               class="btn rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 px-4 py-2"
               style="background: var(--color-primary); color: #fff; white-space: nowrap;">
                💬 Contactar por WhatsApp
            </a>
            <span class="small text-center" style="color: var(--color-muted-fg); white-space: nowrap;">📞 {{ $telefono }}</span>
        @endif
        @if($email)
            <a href="mailto:{{ $email }}" class="btn rounded-pill py-2 px-4" style="border: 1px solid var(--color-border); color: var(--color-fg); white-space: nowrap;">✉️ Enviar correo</a>
        @endif
    </div>
</div>
@endif

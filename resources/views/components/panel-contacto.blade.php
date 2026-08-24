@props([
    'telefono' => null,
    'email' => null,
    'texto' => '¿Interesado? Contacta directamente para reservar o preguntar disponibilidad.',
])

@if($telefono || $email)
<div class="rounded-4 p-4 border bg-white" style="border-color: var(--color-border);">
    <h3 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">¿Interesado?</h3>
    <p class="small mb-3" style="color: var(--color-muted-fg);">{{ $texto }}</p>

    @if($telefono)
        @php $numero = preg_replace('/\D/', '', $telefono); @endphp
        <a href="https://wa.me/57{{ $numero }}" target="_blank" rel="noopener"
           class="btn w-100 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 py-2"
           style="background: var(--color-primary); color: #fff;">
            💬 Contactar por WhatsApp
        </a>
        <p class="small text-center mt-2 mb-0" style="color: var(--color-muted-fg);">📞 {{ $telefono }}</p>
    @endif

    @if($email)
        <a href="mailto:{{ $email }}"
           class="btn w-100 rounded-pill mt-2 py-2"
           style="border: 1px solid var(--color-border); color: var(--color-fg);">
            ✉️ Enviar correo
        </a>
    @endif
</div>
@endif

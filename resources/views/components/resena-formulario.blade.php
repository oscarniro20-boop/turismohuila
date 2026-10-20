@props([
    'tipo',
    'referenciaId',
    'nombre' => '',
])

{{-- Formulario de reseña integrado en la página del servicio.
     Usa el MISMO endpoint y validaciones que la vista independiente:
     route('resenas.store') + ResenaController@store. No duplica lógica backend. --}}
<div class="rounded-4 p-4 mt-3 mb-2"
     style="background: #fff; border: 1px solid var(--color-border); box-shadow: var(--shadow-ring);">

    @if(session('success'))
        <div class="alert alert-success rounded-4 py-2 px-3 small mb-3" role="status">
            ✅ {{ session('success') }}
        </div>
    @endif

    <h4 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.15rem;">
        ⭐ {{ __('ui.review.your_rating') }}
    </h4>
    @if($nombre)
        <p class="small mb-3" style="color: var(--color-muted-fg);">
            {{ __('ui.review.tell_experience_at', ['name' => $nombre]) }}
        </p>
    @else
        <p class="small mb-3" style="color: var(--color-muted-fg);">{{ __('ui.review.tell_experience') }}</p>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 py-2 px-3 small mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('resenas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tipo" value="{{ $tipo }}">
        <input type="hidden" name="referencia_id" value="{{ $referenciaId }}">
        {{-- Vuelve a esta misma página del servicio tras publicar --}}
        <input type="hidden" name="retorno" value="{{ url()->current() }}">

        <fieldset class="mb-3 border-0 p-0 m-0">
            <legend class="form-label mb-1 small fw-semibold">{{ __('ui.review.rating') }}</legend>
            <div class="resena-estrellas" role="radiogroup" aria-label="Calificación de 1 a 5 estrellas">
                @for($i = 5; $i >= 1; $i--)
                    <input type="radio" id="estrella-{{ $tipo }}-{{ $referenciaId }}-{{ $i }}"
                           name="calificacion" value="{{ $i }}"
                           @if((int) old('calificacion') === $i) checked @endif
                           @if($i === 5 && !old('calificacion')) required @endif>
                    <label for="estrella-{{ $tipo }}-{{ $referenciaId }}-{{ $i }}" aria-label="{{ $i }} estrella{{ $i > 1 ? 's' : '' }}">★</label>
                @endfor
            </div>
            @error('calificacion')
                <div class="small text-danger mt-1">{{ $message }}</div>
            @enderror
        </fieldset>

        <div class="mb-3">
            <label for="comentario-{{ $tipo }}-{{ $referenciaId }}" class="form-label small fw-semibold">
                {{ __('ui.review.comment') }}
            </label>
            <textarea class="form-control" id="comentario-{{ $tipo }}-{{ $referenciaId }}"
                      name="comentario" rows="3" maxlength="1000"
                      placeholder="{{ __('ui.review.comment_placeholder') }}">{{ old('comentario') }}</textarea>
            @error('comentario')
                <div class="small text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn rounded-pill fw-semibold px-4 py-2"
                style="background: var(--color-primary); color: #fff;">
            {{ __('ui.review.publish') }}
        </button>
    </form>
</div>
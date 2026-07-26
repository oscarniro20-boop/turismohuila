<p class="text-muted small">Usa una contraseña larga y aleatoria para mayor seguridad.</p>

<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label class="form-label fw-bold">Contraseña Actual</label>
        <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
        @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Nueva Contraseña</label>
        <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror">
        @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>

    <button type="submit" class="btn btn-warning">Actualizar Contraseña</button>
    @if (session('status') === 'password-updated')
        <span class="text-success small ms-2">✔ Guardado.</span>
    @endif
</form>
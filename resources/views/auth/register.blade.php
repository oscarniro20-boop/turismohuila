<x-guest-layout>
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Crear Cuenta</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nombre</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Contraseña</label>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-control" required>
                </div>

                <input type="hidden" name="rol" value="turista">

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('login') }}" class="text-decoration-none small">¿Ya tienes cuenta?</a>
                    <button type="submit" class="btn btn-success">Registrarme</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
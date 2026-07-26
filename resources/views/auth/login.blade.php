<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - TurismoHuila</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="mb-0">🌿 TurismoHuila</h2>
                    <p class="mb-0 mt-2">Inicia sesión para continuar</p>
                </div>

                <div class="card-body p-4">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                            <input id="email" type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" 
                                   required autofocus autocomplete="username"
                                   placeholder="correo@ejemplo.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Contraseña</label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" 
                                   required autocomplete="current-password"
                                   placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                            <label class="form-check-label" for="remember_me">
                                Recordarme
                            </label>
                        </div>

                        <!-- Botón de Login -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light text-center py-3">
                    <p class="mb-0 text-muted">
                        🌿 Explora las maravillas del Huila
                    </p>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="text-center mt-3">
                <p class="text-muted small">
                    ¿No tienes cuenta? 
                    <a href="{{ route('register') }}" class="text-success text-decoration-none fw-bold">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
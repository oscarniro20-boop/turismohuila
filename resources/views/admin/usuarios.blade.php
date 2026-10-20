@extends('layouts.app')

@section('title', 'Gestión de usuarios')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <span class="d-block mb-1 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">ADMINISTRACIÓN</span>
            <h1 class="mb-0" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.9rem;">Gestión de usuarios</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.auditorias.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">📜 Auditoría</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">⬅️ Volver al panel</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert rounded-4 small" role="alert" style="background: rgba(40,167,69,.16); color: var(--color-fg); border: 1px solid rgba(40,167,69,.3);">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert rounded-4 small" role="alert" style="background: rgba(220,53,69,.14); color: var(--color-fg); border: 1px solid rgba(220,53,69,.3);">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            {{-- ── Crear usuario ─────────────────────────────────────────── --}}
            <div class="rounded-4 p-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <h2 class="h5 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Crear usuario</h2>

                <form method="POST" action="{{ route('admin.usuarios.store') }}" class="vstack gap-3">
                    @csrf

                    <div>
                        <label for="name" class="form-label small fw-semibold" style="color: var(--color-fg);">Nombre</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                        @error('name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="email" class="form-label small fw-semibold" style="color: var(--color-fg);">Correo electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                        @error('email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="password" class="form-label small fw-semibold" style="color: var(--color-fg);">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8">
                        @error('password')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label small fw-semibold" style="color: var(--color-fg);">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
                    </div>

                    <div>
                        <label for="rol" class="form-label small fw-semibold" style="color: var(--color-fg);">Rol</label>
                        <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                            <option value="turista" {{ old('rol') === 'turista' ? 'selected' : '' }}>Turista</option>
                            <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                        @error('rol')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn rounded-pill px-4" style="background: var(--color-primary); color: #fff;">➕ Crear usuario</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            {{-- ── Listado ──────────────────────────────────────────────── --}}
            <div class="rounded-4 p-4 mb-4" style="background: var(--color-muted); border: 1px solid var(--color-border);">
                <form method="GET" action="{{ route('admin.usuarios.index') }}" class="d-flex gap-2 mb-3">
                    <input type="search" class="form-control" name="q" value="{{ $busqueda }}" placeholder="Buscar por nombre o correo…" aria-label="Buscar usuarios">
                    <button type="submit" class="btn btn-sm rounded-pill px-3" style="border: 1px solid var(--color-border); color: var(--color-fg);">🔍 Buscar</button>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 small" style="--bs-table-hover-bg: var(--color-border);">
                        <thead>
                            <tr>
                                <th scope="col">Usuario</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <td>
                                        <div class="fw-semibold" style="color: var(--color-fg);">{{ $usuario->name }}</div>
                                        <div style="color: var(--color-muted-fg);">{{ $usuario->email }}</div>
                                    </td>
                                    <td>
                                        @if ($usuario->esAdmin())
                                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,167,69,.16); color: var(--color-fg);">Admin</span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-1" style="background: var(--color-border); color: var(--color-muted-fg);">Turista</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if ($usuario->estaActivo())
                                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(40,167,69,.16); color: var(--color-fg);">Activo</span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-1" style="background: rgba(220,53,69,.14); color: var(--color-danger);">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($usuario->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="d-flex flex-wrap gap-1 align-items-center">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="name" value="{{ $usuario->name }}">
                                            <input type="hidden" name="email" value="{{ $usuario->email }}">

                                            <select name="rol" class="form-select form-select-sm w-auto" aria-label="Rol de {{ $usuario->name }}">
                                                <option value="turista" {{ $usuario->rol === 'turista' ? 'selected' : '' }}>Turista</option>
                                                <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>

                                            <button type="submit" class="btn btn-sm rounded-pill px-3" style="border: 1px solid var(--color-border); color: var(--color-fg);">Guardar rol</button>
                                        </form>
                                        @endif

                                        @if ($usuario->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="mt-1" onsubmit="return confirm('¿Eliminar al usuario {{ $usuario->name }}? Se borrarán también sus reseñas y favoritos.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm rounded-pill px-3" style="border: 1px solid rgba(220,53,69,.4); color: var(--color-danger);">🗑️ Eliminar</button>
                                            </form>

                                        <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}" class="mt-1" onsubmit="return confirm('¿{{ $usuario->estaActivo() ? 'Desactivar' : 'Activar' }} la cuenta de {{ $usuario->name }}?')">
                                            @csrf
                                            <input type="hidden" name="activo" value="{{ $usuario->estaActivo() ? '0' : '1' }}">
                                            <button type="submit" class="btn btn-sm rounded-pill px-3" style="border: 1px solid {{ $usuario->estaActivo() ? 'rgba(220,53,69,.4)' : 'rgba(40,167,69,.4)' }}; color: {{ $usuario->estaActivo() ? 'var(--color-danger)' : '#198754' }};">{{ $usuario->estaActivo() ? '⏸️ Desactivar' : '✅ Activar' }}</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="color: var(--color-muted-fg);">No hay usuarios que coincidan con la búsqueda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $usuarios->links() }}
                </div>
            </div>

            <p class="small mb-0" style="color: var(--color-muted-fg);">
                El rol solo cambia desde este panel (lista cerrada admin/turista). Toda operación queda registrada en la
                <a href="{{ route('admin.auditorias.index') }}" style="color: var(--color-primary);">auditoría central</a>.
                No se puede eliminar ni desactivar la propia cuenta, ni eliminar ni desactivar al último administrador activo.
            </p>
        </div>
    </div>
</div>
@endsection

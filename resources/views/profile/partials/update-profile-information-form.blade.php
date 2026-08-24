<p class="text-muted small">Actualiza tu información de perfil y correo.</p>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label class="form-label fw-bold">Foto de Portada (Banner)</label>
        <div class="ratio-box ratio-16x5 mb-2">
            @if($user->foto_portada)
                <img src="{{ $user->url_imagen }}" class="img-fluid object-cover rounded" style="width:100%; height:100%; object-fit:cover;">
            @else
                <div class="d-flex align-items-center justify-center h-100 text-muted-foreground">
                    <svg class="h-6 w-6 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="23"/></svg>
                    <span class="text-xs">Sin foto de portada</span>
                </div>
            @endif
        </div>
        <input type="file" name="foto_portada" class="form-control" accept="image/*">
        <div class="form-text">Recomendado: 1200x400px</div>
        @error('foto_portada')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Nombre</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Correo Electrónico</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    @if (session('status') === 'profile-updated')
        <span class="text-success small ms-2">✔ Guardado.</span>
    @endif
</form>
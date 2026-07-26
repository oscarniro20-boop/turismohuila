<p class="text-muted small">Actualiza tu información de perfil y correo.</p>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label class="form-label fw-bold">Foto de Portada (Banner)</label>
        @if($user->foto_portada)
            <img src="{{ asset($user->foto_portada) }}" class="img-fluid rounded mb-2" style="max-height:200px; width:100%; object-fit:cover;">
        @endif
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
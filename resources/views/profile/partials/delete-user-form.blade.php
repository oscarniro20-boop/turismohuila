<p class="text-muted small">Al eliminar tu cuenta, todos tus datos se borrarán permanentemente. Esta acción no se puede deshacer.</p>

<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarEliminarCuenta">
    Eliminar Cuenta
</button>

<div class="modal fade" id="confirmarEliminarCuenta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title">¿Eliminar tu cuenta?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Esta acción es permanente. Ingresa tu contraseña para confirmar.</p>
                    <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Contraseña">
                    @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>
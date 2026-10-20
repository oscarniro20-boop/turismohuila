<p class="text-muted small">{{ __('ui.account.delete_warning') }}</p>

<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmarEliminarCuenta">
    {{ __('ui.profile.delete_account') }}
</button>

<div class="modal fade" id="confirmarEliminarCuenta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('ui.account.confirm_delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('ui.account.password_prompt') }}</p>
                    <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="{{ __('ui.account.password') }}">
                    @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.account.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('ui.profile.delete_account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
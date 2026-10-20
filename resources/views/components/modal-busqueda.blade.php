<div class="modal fade" id="modalBusqueda" tabindex="-1" aria-labelledby="modalBusquedaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBusquedaLabel">{{ __('ui.search.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.common.close') }}"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="busqueda-input" class="form-control form-control-lg" placeholder="{{ __('ui.search.placeholder') }}" autocomplete="off">
                <div id="busqueda-resultados" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
// Selects dependientes departamento → municipio (ámbito nacional)
// Usado por x-form-ubicacion y x-filtro-ubicacion. Datos en data-ubicacion-mapa.
(function () {
    function poblarMunicipios(bloque, departamento, seleccionado) {
        const select = bloque.querySelector('[data-ubicacion-municipio]');
        if (!select) return;

        let mapa = {};
        const script = bloque.querySelector('[data-ubicacion-mapa]');
        if (script) {
            try { mapa = JSON.parse(script.textContent); } catch (e) { mapa = {}; }
        }

        const municipios = (departamento && mapa[departamento]) ? mapa[departamento] : [];

        select.innerHTML = '';
        const optVacio = document.createElement('option');
        optVacio.value = '';
        optVacio.textContent = select.name ? optVacio.textContent : optVacio.textContent;
        select.appendChild(optVacio);

        municipios.forEach(function (mun) {
            const opt = document.createElement('option');
            opt.value = mun;
            opt.textContent = mun;
            if (seleccionado && mun === seleccionado) opt.selected = true;
            select.appendChild(opt);
        });

        if (municipios.length === 0) {
            optVacio.textContent = 'Seleccione primero un departamento';
        } else {
            optVacio.textContent = select.closest('form') ? 'Todos los municipios' : 'Seleccione municipio...';
        }
    }

    document.querySelectorAll('[data-ubicacion-bloque]').forEach(function (bloque) {
        const depSelect = bloque.querySelector('[data-ubicacion-departamento]');
        const munSelect = bloque.querySelector('[data-ubicacion-municipio]');
        if (!depSelect || !munSelect) return;

        // Recordar el municipio preseleccionado (edición) para restaurarlo al cambiar.
        const munInicial = munSelect.value;

        depSelect.addEventListener('change', function () {
            poblarMunicipios(bloque, depSelect.value, null);
        });
    });
})();

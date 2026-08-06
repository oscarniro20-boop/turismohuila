import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createApp } from 'vue';

// Función global para copiar enlace al portapapeles
window.copiarEnlace = function (url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            alert('¡Enlace copiado!');
        }).catch(() => {
            fallbackCopiar(url);
        });
    } else {
        fallbackCopiar(url);
    }
};

function fallbackCopiar(url) {
    const textarea = document.createElement('textarea');
    textarea.value = url;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        alert('¡Enlace copiado!');
    } catch (err) {
        console.error('No se pudo copiar:', err);
    }
    document.body.removeChild(textarea);
}

// Listener delegado para botones de favoritos
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.favorito-btn');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const tipo = btn.dataset.favoritoTipo;
    const id = btn.dataset.favoritoId;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/favoritos/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            tipo: tipo,
            referencia_id: id,
        }),
    })
    .then(response => response.json())
    .then(data => {
        btn.classList.toggle('activo', data.activo);
        btn.innerHTML = data.activo ? '❤️' : '🤍';
    })
    .catch(() => {
        // Si no está autenticado, redirige al login
        window.location.href = '/login';
    });
});

document.querySelectorAll('[data-vue-app]').forEach(el => {
    const items = JSON.parse(el.dataset.items || '[]');
    const userRol = el.dataset.rol || 'invitado';
    const userId = parseInt(el.dataset.userid || '0');

    createApp({
        data() {
            return {
                busqueda: '',
                items: items,
                userRol: userRol,
                userId: userId,
            }
        },
        computed: {
            itemsFiltrados() {
                if (!this.busqueda) return this.items;
                return this.items.filter(item =>
                    item.nombre.toLowerCase().includes(this.busqueda.toLowerCase()) ||
                    (item.municipio && item.municipio.toLowerCase().includes(this.busqueda.toLowerCase()))
                );
            }
        },
        methods: {
            puedeEditar(item) {
                if (this.userRol === 'admin') return true;
                if (item.user_id === this.userId) return true;
                return false;
            },
            eliminar(event) {
                if (confirm('¿Seguro que deseas eliminar este registro?')) {
                    event.target.submit();
                }
            }
        }
    }).mount(el);
});

// ── Búsqueda global ────────────────────────────────────────────────────────
(function () {
    const input = document.getElementById('busqueda-input');
    const resultados = document.getElementById('busqueda-resultados');
    if (!input || !resultados) return;

    let debounceTimer;

    const iconosPorTipo = {
        lugar: '📍',
        hotel: '🏨',
        restaurante: '🍽️',
        tour: '🥾',
        guia: '🧑‍🏫',
        agencia: '🏢',
    };

    const nombresPorTipo = {
        lugar: 'Lugares',
        hotel: 'Hoteles',
        restaurante: 'Restaurantes',
        tour: 'Tours',
        guia: 'Guías',
        agencia: 'Agencias',
    };

    function agruparPorTipo(items) {
        const grupos = {};
        items.forEach(item => {
            if (!grupos[item.tipo]) grupos[item.tipo] = [];
            grupos[item.tipo].push(item);
        });
        return grupos;
    }

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    function renderizarResultados(data) {
        const grupos = agruparPorTipo(data.resultados || []);
        const tipos = Object.keys(nombresPorTipo);

        if (tipos.every(tipo => !grupos[tipo] || grupos[tipo].length === 0)) {
            resultados.innerHTML = '<p class="text-muted small">No se encontraron resultados.</p>';
            return;
        }

        const fragment = document.createDocumentFragment();
        tipos.forEach(tipo => {
            const items = grupos[tipo] || [];
            if (items.length === 0) return;

            const contenedor = document.createElement('div');
            contenedor.className = 'mb-2';

            const encabezado = document.createElement('small');
            encabezado.className = 'text-uppercase fw-bold text-muted';
            encabezado.textContent = nombresPorTipo[tipo];
            contenedor.appendChild(encabezado);

            items.forEach(item => {
                const enlace = document.createElement('a');
                enlace.href = item.url;
                enlace.className = 'd-block text-decoration-none py-2 border-bottom';
                enlace.style.color = 'var(--color-fg)';

                const flex = document.createElement('div');
                flex.className = 'd-flex align-items-center gap-2';

                const icono = document.createElement('span');
                icono.textContent = iconosPorTipo[item.tipo] || '🔎';
                flex.appendChild(icono);

                const info = document.createElement('div');
                const nombre = document.createElement('div');
                nombre.className = 'fw-medium';
                nombre.textContent = item.nombre;
                const subtitulo = document.createElement('div');
                subtitulo.className = 'small text-muted';
                subtitulo.textContent = item.subtitulo;
                info.appendChild(nombre);
                info.appendChild(subtitulo);
                flex.appendChild(info);

                enlace.appendChild(flex);
                contenedor.appendChild(enlace);
            });

            fragment.appendChild(contenedor);
        });

        resultados.innerHTML = '';
        resultados.appendChild(fragment);
    }

    input.addEventListener('input', function () {
        const valor = input.value.trim();

        clearTimeout(debounceTimer);

        if (valor.length < 2) {
            resultados.innerHTML = '<p class="text-muted small">Escribe al menos 2 caracteres</p>';
            return;
        }

        resultados.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success" role="status"></div></div>';

        debounceTimer = setTimeout(function () {
            fetch('/buscar?q=' + encodeURIComponent(valor))
                .then(response => response.json())
                .then(renderizarResultados)
                .catch(() => {
                    resultados.innerHTML = '<p class="text-danger small">Error al buscar. Intenta de nuevo.</p>';
                });
        }, 300);
    });

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalBusqueda'));
            modal.show();
        }
    });

    document.getElementById('modalBusqueda').addEventListener('hidden.bs.modal', function () {
        input.value = '';
        resultados.innerHTML = '';
    });
})();

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createApp } from 'vue';

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
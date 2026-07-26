import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createApp } from 'vue';

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
                if (this.userRol === 'prestador' && item.user_id === this.userId) return true;
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
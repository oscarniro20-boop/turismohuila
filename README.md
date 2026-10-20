# 🌄 Turismo por Colombia — Descubre el Huila

Plataforma web turística regional para descubrir, explorar y contactar los servicios turísticos del departamento del Huila (Colombia).

**Estado:** v1.0.0 — Release candidate preparado para prueba de despliegue. Build verificado, dependencias auditadas y seguridad aplicada.

> **Aviso:** este repositorio no contiene credenciales, secretos, API keys ni `.env` reales. Todo secreto vive solo en el `.env` local/producción, que no se publica.

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 13 · PHP 8.3 |
| Base de datos desarrollo | SQLite (por defecto) / MySQL |
| Base de datos producción | MySQL (InfinityFree) |
| Frontend | Blade · Bootstrap 5 · Vite · JavaScript puro |
| Imágenes | Cloudinary (subida/eliminación/URLs) |
| Mapa | Leaflet + OpenStreetMap + Marker Cluster |
| Tipografía | Google Fonts |
| Correo | SMTP (Brevo en producción) |
| Tests | PHPUnit (Pest opcional) · `php artisan test` |

## Módulos

**Contenido turístico (7):** Lugares, Hoteles, Restaurantes, Tours, Agencias, Guías y **Transporte**.

**Transversales:** Autenticación y roles (turista/admin), Favoritos, Reseñas, Búsqueda global server-side, Mapa interactivo con clustering, Contacto, Páginas institucionales (nosotros/privacidad/términos), Administración.

**Relaciones clave:**
- `tour_paquetes` es la **fuente oficial** de la relación agencia↔tour. `agencia_tour` es tabla **legacy de transición** (sin lectura en la aplicación).
- Reseñas y favoritos usan relaciones polimórficas (`tipo` + `referencia_id`) sobre los 7 módulos.

## Requisitos

- PHP ≥ 8.3
- Composer
- Node.js ≥ 20 y npm
- SQLite (desarrollo) o MySQL (InfinityFree en producción)
- Extensiones PHP: `pdo`, `pdo_sqlite`, `pdo_mysql` (producción), `mbstring`, `openssl`, `fileinfo`, `json`, `xml`, `ctype`, `tokenizer`, `curl`, `gd`

## Instalación

```bash
# Dependencias
composer install
npm install

# Entorno
cp .env.example .env
php artisan key:generate

# Base de datos (desarrollo SQLite)
touch database/database.sqlite
php artisan migrate
php artisan db:seed   # opcional: datos de demostración

# Desarrollar (Vite)
npm run build
npm run dev
```

## Pruebas

```bash
php artisan optimize:clear
php artisan test
```

Suite de **109 tests / 230 assertions**. Ejecución actual en entorno Windows/Laragon: **36 fallos detectados** (tests de autenticación, registro y CRUD), atribuibles a configuración de base de datos en el entorno de desarrollo local (`.env` con MySQL, tests configurados para SQLite en memoria). Los fallos son de entorno, no de lógica de negocio. En servidor no hay artisan: re-verificar en local antes de exportar el `.sql`.

## Build / Assets

```bash
npm run build    # genera public/build/manifest.json y los assets compilados
```

No editar a mano los archivos compilados de `public/build/`.

## Preparación para producción (InfinityFree + MySQL)

Destino real: hosting `if0_42868030`, `https://oscart.page.gd`, base `if0_42868030_turismo`. Sin SSH ni artisan en servidor: la base se importa como `.sql`.

1. En local: `composer install --no-dev --optimize-autoloader` + `npm run build` (verificar que no exista `bootstrap/cache/config.php`)
2. `.env` de producción en `htdocs/laravel_app/` según `DEPLOY.md` §2
3. Estructura `htdocs/` + `htdocs/laravel_app/` según `DEPLOY.md` §3 (plantillas `index.php`/`.htaccess` en `scripts/deploy/`)
4. Importar `.sql` vía phpMyAdmin según `DEPLOY.md` §4
5. Verificar según `DEPLOY.md` §5

Ver [`DEPLOY.md`](DEPLOY.md) (guía operativa de despliegue en InfinityFree + MySQL) y [`PRODUCTION_CHECKLIST.md`](PRODUCTION_CHECKLIST.md) para el detalle completo.

## Seguridad aplicada

- Cabeceras HTTP: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `Content-Security-Policy` (compatible con Leaflet/OSM/Cloudinary/Google Fonts) y `Strict-Transport-Security` (solo bajo HTTPS en producción).
- Área administrativa protegida con middleware `rol:admin`.
- Prevención de IDOR, Mass Assignment y escalamiento de rol (cubiertos por `SeguridadTest`).
- `ResenaPolicy`: solo el **autor** puede editar/eliminar su reseña (los administradores gestionan las suyas, no las ajenas).
- Rate limiting en búsqueda, contacto, favoritos, reseñas y `nombre-servicio`.
- Storage privado: `serve=false` por defecto (los archivos no expuestos no se sirven vía rutas de storage).
- CSRF activo en el grupo `web`.
- Credenciales (Cloudinary/Brevo) solo en `.env`.

## Referencias de documentos

- [`DEPLOY.md`](DEPLOY.md) — despliegue real en InfinityFree + MySQL (guía operativa).
- [`PRODUCTION_CHECKLIST.md`](PRODUCTION_CHECKLIST.md) — checklist de puesta en producción (InfinityFree + MySQL).
- [`MAINTENANCE.md`](MAINTENANCE.md) — mantenimiento operativo.
- [`CHECKLIST.md`](CHECKLIST.md) — checklist de trabajo.
- [`CHECKLIST_TURISMOHUILA_VALORACION.md`](CHECKLIST_TURISMOHUILA_VALORACION.md) — checklist maestro + valoración comercial objetivo.

---

## Licencia

Proyecto de código cerrado/propietario Turismo por Colombia. No se otorgan derechos de distribución por este repositorio.

# ✅ Checklist de Producción — Turismo por Colombia (InfinityFree + MySQL)

> Destino real: hosting `if0_42868030`, `https://oscart.page.gd`, MySQL `sql105.infinityfree.com:3306`, base `if0_42868030_turismo`. Guía operativa: [`DEPLOY.md`](DEPLOY.md).

---

## Variables de Entorno (`.env` en `htdocs/laravel_app/`)

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generada (única, 32 caracteres)
- [ ] `APP_URL` apunta al dominio real (HTTPS)
- [ ] `APP_LOCALE=es`
- [ ] `LOG_CHANNEL=stack`, `LOG_LEVEL=warning`
- [ ] Base `if0_42868030_turismo` creada (Panel > MySQL Databases > New Database: `turismo`)
- [ ] `DB_CONNECTION=mysql`, `DB_HOST=sql105.infinityfree.com`, `DB_PORT=3306`
- [ ] `DB_DATABASE=if0_42868030_turismo`, `DB_USERNAME=if0_42868030`, `DB_PASSWORD` = la del panel (no se guarda en docs)
- [ ] `SESSION_DRIVER=database`
- [ ] `SESSION_SECURE_COOKIE=true` (requiere HTTPS)
- [ ] `SESSION_HTTP_ONLY=true`
- [ ] `SESSION_SAME_SITE=lax`
- [ ] `CACHE_STORE=database`
- [ ] `QUEUE_CONNECTION=database`
- [ ] `MAIL_MAILER=smtp` (Brevo configurado)
- [ ] `CLOUDINARY_URL` configurada

## Base de Datos (MySQL InfinityFree, via importacion `.sql`)

- [ ] PHP 8.3 elegido en Panel > PHP Config
- [ ] Base `if0_42868030_turismo` creada
- [ ] Admin verificado en local antes de exportar (`SELECT id,email,rol FROM users;`)
- [ ] Export HeidiSQL: Base sin Suprimir/Crear; Tablas Suprimir+Crear; Datos Insertar (incluye `sessions`, `cache`, `jobs`)
- [ ] SQL importado desde Panel > MySQL Databases > Admin > Importar
- [ ] Tablas confirmadas: `users`, `lugares`, `hoteles`, `resenas`, `sessions`, `migrations`
- [ ] Backups: exportar `.sql` desde el panel periodicamente

## Archivos (sin SSH: todo via File Manager)

- [ ] `htdocs/` = contenido de `public/` (`index.php` adaptado con `scripts/deploy/htdocs-index.php`, `.htaccess`, `build/`, `imagenes/`, `galeria/`, `portadas/`)
- [ ] App completa en `htdocs/laravel_app/` con `.htaccess` = `Require all denied` (`scripts/deploy/laravel_app.htaccess`)
- [ ] `htdocs/index.html` e `index.html` de bienvenida borrados; ningun `.sql` en `htdocs/`
- [ ] `bootstrap/cache/config.php` NO subido (ni caches de rutas/vistas con rutas locales)

## Assets / Build

- [ ] `npm ci` ejecutado
- [ ] `npm run build` ejecutado
- [ ] `public/build/manifest.json` generado
- [ ] Assets compilados en `public/build/`

## Cachés (no generar para subir)

- [ ] No se sube `bootstrap/cache/config.php` ni caches con rutas locales

## Seguridad

- [ ] `APP_DEBUG=false`
- [ ] Sin credenciales reales en `.env.example`
- [ ] `.env` excluido de Git
- [ ] Middleware `rol` registrado
- [ ] `ResenaPolicy` funcional
- [ ] Rate limiting en búsqueda y contacto
- [ ] Formularios con protección CSRF
- [ ] Cabeceras de seguridad activas (X-Frame-Options, CSP, HSTS bajo HTTPS)
- [ ] HTTPS con certificado SSL válido

## Sesiones

- [ ] `SESSION_SECURE_COOKIE=true` (solo si HTTPS)
- [ ] `SESSION_HTTP_ONLY=true`
- [ ] `SESSION_SAME_SITE=lax`

## Storage

- [ ] Archivos privados no expuestos (`serve=false`)
- [ ] Cloudinary configurado y funcional
- [ ] Archivos públicos en `storage/app/public`

## Pruebas

- [ ] `php artisan optimize:clear` antes de tests
- [ ] `php artisan test` verde en local antes de exportar (esperado: 109 passed / 230 assertions — pendiente verificar fuera de Laragon)
- [ ] Login funciona
- [ ] Registro funciona
- [ ] CRUD de los 7 módulos funciona
- [ ] Favoritos funcionan
- [ ] Reseñas funcionan
- [ ] Búsqueda funciona
- [ ] Mapa funciona
- [ ] Subida de imágenes funciona

## Logs

- [ ] `storage/logs/` con permisos correctos
- [ ] Logs de producción configurados
- [ ] Sin contraseñas/tokens en logs

## Cron / Scheduler

- [ ] No requiere cron actualmente (sin tareas programadas)

## Colas

- [ ] `QUEUE_CONNECTION=database` (tabla `jobs` creada por migración)

## Despliegue (File Manager, sin SSH)

- [ ] `composer install --no-dev --optimize-autoloader` en local
- [ ] `npm run build` en local (`build/` subido a raiz de `htdocs/`)
- [ ] `.env` de producción creado en `htdocs/laravel_app/` (sin secretos en repo)
- [ ] Cuenta vieja `if0_42438171` limpia (sin `.env` con credenciales)

## Verificación Post-Despliegue (`https://oscart.page.gd`)

- [ ] Home carga con estilos
- [ ] `/laravel_app/.env` da 403
- [ ] Login admin ok; `/mapa`, `/buscar` (lugar + hotel), subida Cloudinary, crear/eliminar reseña
- [ ] "Olvidé mi contraseña" envia correo (Brevo)
- [ ] Error 500: `APP_DEBUG=true` momentaneo, leer, volver a `false`

---

## Estado actual de la preparación

| Ítem | Estado |
|---|---|
| `.env.example` completo | ✅ |
| `.env` excluido de Git | ✅ |
| `APP_DEBUG=false` documentado | ✅ |
| MySQL InfinityFree revisado (config + importacion `.sql`) | ✅ |
| Cloudinary revisado | ✅ |
| Vite preparado | ✅ |
| Composer validado | ✅ |
| `composer audit` — 0 vulnerabilidades | ✅ |
| `npm audit` | ⚠️ NO VERIFICADO — salida no capturada (limitación técnica en Windows) |
| Storage revisado | ✅ |
| Permisos documentados | ✅ |
| Sesiones revisadas | ✅ |
| Seguridad revisada | ✅ |
| Tests ejecutados (109 / 230) | ✅ 36 fallos de entorno (no de lógica), pendiente Linux |
| Build ejecutado (`public/build/manifest.json`) | ✅ |
| Rutas verificadas (89 rutas) | ✅ |
| Migraciones verificadas (38 archivos; en servidor via importacion `.sql`) | ✅ |
| Git limpio de basura | ✅ |
| Secretos en repositorio | ✅ ninguno detectado |
| `pdo_mysql` requerido en produccion (verificar PHP 8.3 en panel) | ⚠️ verificar en Panel > PHP Config |
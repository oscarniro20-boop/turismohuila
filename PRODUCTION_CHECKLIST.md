# ✅ Checklist de Producción - TurismoHuila

## Variables de Entorno

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generada (única)
- [ ] `APP_URL` apunta al dominio real
- [ ] Credenciales de BD correctas
- [ ] `SESSION_DRIVER=file` o `database`
- [ ] `CACHE_STORE=file`
- [ ] `QUEUE_CONNECTION=sync`

## Base de Datos

- [ ] Migraciones ejecutadas
- [ ] Seeders ejecutados (si aplica)
- [ ] Backups configurados
- [ ] Índices creados en columnas frecuentes

## Permisos

- [ ] `storage/` con permisos 755
- [ ] `bootstrap/cache/` con permisos 755
- [ ] `public/imagenes/` con permisos 755
- [ ] `public/galeria/` con permisos 755

## Assets

- [ ] `npm run build` ejecutado
- [ ] Assets compilados en `public/build/`

## Cachés

- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan event:cache`

## Seguridad

- [ ] `APP_DEBUG=false`
- [ ] Sin credenciales reales en `.env.example`
- [ ] Middleware `rol` registrado
- [ ] `ResenaPolicy` funcional
- [ ] Rate limiting en búsqueda y contacto
- [ ] Formularios con protección CSRF

## Pruebas

- [ ] Login funciona
- [ ] Registro funciona
- [ ] CRUD de lugares funciona
- [ ] CRUD de hoteles funciona
- [ ] CRUD de restaurantes funciona
- [ ] CRUD de tours funciona
- [ ] CRUD de guías funciona
- [ ] CRUD de agencias funciona
- [ ] Favoritos funcionan
- [ ] Reseñas funcionan
- [ ] Búsqueda funciona
- [ ] Mapa funciona
- [ ] Subida de imágenes funciona

## Logs

- [ ] `storage/logs/` con permisos correctos
- [ ] Logs de producción configurados

## Despliegue

- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] Archivos de producción subidos
- [ ] `.env` de producción creado
- [ ] `public_html/` configurado correctamente
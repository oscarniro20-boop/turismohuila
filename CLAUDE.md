# CLAUDE.md — Turismo por Colombia (contexto + auditoria para Claude)

> Pega este archivo completo como contexto inicial en una conversacion nueva con Claude / Claude Code.
> Repo: https://github.com/oscarniro20-boop/turismo-huila · Branch: `main` · Commit base: `2aaf49c`
> Generado: 2026-10-08 desde auditoria estatica del workspace (sin php/node en el entorno de auditoria).

## 1. Que es el proyecto

Plataforma turistica regional "Turismo por Colombia — Descubre el Huila" (Huila, Colombia).
Stack: **Laravel 13 · PHP 8.3 · Eloquent · Blade · Bootstrap 5 · Vite 8 · JS puro**.
DB: SQLite en desarrollo, MySQL en produccion InfinityFree (`if0_42868030_turismo`).
Imagenes: **Cloudinary** con fallback a disco local `public/` (`App\Services\CloudinaryImageService`).
Mapa: Leaflet + OSM + MarkerCluster. Correo: SMTP/Brevo. Tests: PHPUnit (`php artisan test`).

7 modulos: **lugares, hoteles, restaurantes, tours, agencias, guias, transportes** (+ resenas, favoritos, busqueda global, mapa, contacto, institucionales, admin, auth roles turista/admin, i18n es/en con `SetLocale` por sesion).

## 2. Estructura clave

```
app/Http/Controllers/ (Admin, Agencia, Bienvenida, Busqueda, Contacto, Favorito, Guia, Hotel, Lugar, Resena, Restaurante, Tour, Transporte + Auth/* + Profile)
app/Models/ (Agencia, Guia, Hotel, Lugar, Restaurante, Tour, Transporte + *Imagen por modulo + Resena, Favorito, User, TourPaquete)
app/Services/ (BusquedaService, MapaService, CloudinaryImageService, GaleriaImagenService)
app/Concerns/ (ManagesImages, HasGalleryImages, HasGalleryImageUrl, HasRating, HasUrlImagen)
app/Http/Middleware/ (VerificarRol alias 'rol', SetLocale, CabecerasSeguridad)
app/Policies/ResenaPolicy.php
routes/web.php + routes/auth.php (Breeze)
resources/views/ (bienvenida, mapa, 7 modulos index/show/create/edit, resenas/*, admin/dashboard, components/*)
database/migrations/ (38) · seeders (UserSeeder, TurismoSeeder)
lang/es/ui.php + lang/en/ (nuevo sin commitear)
config/municipios.php · bootstrap/app.php
```

## 3. Rutas (resumen de routes/web.php)

- `/` y `/bienvenida` → `BienvenidaController@index`. `POST /idioma` (es|en). `GET /buscar` (throttle 30,1) JSON.
- Vistas: `/nosotros /privacidad /terminos`, `/contacto` (GET+POST throttle 5,1), `/mapa`, `/sala-visita` (auth).
- Admin (`auth` + `rol:admin`, ANTES que publicas): 7 modulos `except(index,show)` + `resenas@index` + `GET /admin`.
- Turista (`auth`, throttle 10,1): `resenas except(index,show)` + `GET /resenas/nombre-servicio`.
- Publicas `only(index,show)` 7 modulos + `resenas@show`. Auth: `profile/*`, `mis-resenas`, `favoritos/toggle` (POST 30,1 JSON), `mis-favoritos` + `auth.php` Breeze.

## 4. Convenciones

- `tour_paquetes` = fuente oficial agencia<->tour; `agencia_tour` legacy (no leer).
- Resenas/favoritos **polimorficos** (`tipo` + `referencia_id`, morphMap: lugar|hotel|restaurante|tour|guia|agencia|transporte). Validar `in:...` + `getMorphedModel()` + `whereKey()->exists()`.
- Imagenes: principal `imagen` + galerias `*Imagenes(ruta)`. Subir via `ManagesImages::subirImagenPrincipal($file,'modulo')`; galeria `GaleriaImagenService::guardar($entidad,$files,'galeria/modulo')` (max 10, jpg,jpeg,png,webp 5MB). Borrar con `eliminarImagen()`.
- Ratings: `withAvg('resenas','calificacion')` + `withCount`. Traits `HasRating/HasUrlImagen`.
- Seguridad: `rol:admin`, `ResenaPolicy`, `retorno` solo si `str_starts_with(url('/'))`, throttles, CSRF web, headers `CabecerasSeguridad`, `user_id` siempre de `auth()->id()`.

## 5. Auditoria — criticos

1. **Bug validacion Transporte update** (`TransporteController.php:102`): `'mimes:jpg,jpeg,png,max:2048'` falta `|` antes de `max`. Fix: `mimes:jpg,jpeg,png,webp|max:2048`.
2. **Mass assignment `LugarController@store:55** `$request->all()` + `create($datos)`. Fix: `only([...])` o FormRequest (Agencia ya usa Store/Update Request: extender patron).
3. **ResenaPolicy vs README**: policy permite `esAdmin() || autor`, README dice "solo autor". Decidir regla y alinear codigo+docs+tests.
4. **Git sucio**: ~80 modificados + 5 untracked (AdminController, SetLocale, lang/en, lang/es/ui.php, admin/). Commitear en bloques antes de seguir.

## 6. Auditoria — rendimiento

5. **Admin dashboard** carga 7 modulos completos (`orderBy(nombre)->get()` + map con `calificacion_promedio` N+1). Fix: `select(id,nombre,municipio|zonas,updated_at)` + `withAvg/withCount` + top-N/paginacion + `Cache::remember(300)`.
6. **Bienvenida**: `LugarImagen::inRandomOrder()->take(8)` = ORDER BY RANDOM (lento en PG) + fallback 8 objetos duplicados + `$totalExperiencias` omite guias/agencias. Fix: `latest()->take(8)`, 1 placeholder, incluir 7 modulos.
7. **BusquedaService**: `where()->orWhere()` sin agrupar + LIKE sin escapar `%_` + 7 queries por keystroke. Fix: closure, escapar, indice fulltext/trigram PG a mediano plazo.
8. **MapaService**: sin limite ni cache. Fix: `Cache::remember('mapa-puntos',300,...)`.
9. **Resena index/misResenas**: `->get()` sin paginar. Fix: `paginate(12)`.
## 7. Auditoria — seguridad e higiene

10. CSP con `'unsafe-inline'` en script-src (temporal por scripts inline Blade). Plan: mover a resources/js + nonces. No quitar sin migrar.
11. `.env.example`: `APP_DEBUG=true`, `SESSION_SECURE_COOKIE=false`, `MAIL_*=log` — ok local; en prod exigir `false/true`/Brevo/HTTPS.
12. Doble `validate()` en Transporte store/update — unificar en uno solo.
13. `LugarController@update` borra galeria con `imagenes()->delete()` sin borrar archivos Cloudinary (fuga storage) y sin transaccion. Usar `eliminarTodasImagenes()` + `DB::transaction()`.
14. Tests declarados `109/230, 36 fallos` por entorno (MySQL local vs sqlite memoria). Re-verificar en local antes de exportar el `.sql` (en servidor no hay artisan).
15. `public/imagenes|galeria|portadas` (cientos de binarios) commiteados — inflan repo/ZIP. Mover a Cloudinary + gitignore o LFS.
16. `CHECKLIST.md` citado en README no existe en raiz (solo CHANGELOG, CHECKLIST_TURISMOHUILA_VALORACION, DEPLOY, MAINTENANCE, PRODUCTION_CHECKLIST, README). Arreglar enlace.
17. `lang/en/` untracked: verificar cobertura 1:1 de claves es/en antes de prod.

## 8. Comandos

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate && php artisan db:seed
npm run build && npm run dev
php artisan optimize:clear && php artisan test
composer audit && npm audit
```

Deploy InfinityFree+MySQL: `composer install --no-dev -o` en local > `npm run build` > importar `.sql` en `if0_42868030_turismo` > subir `htdocs/` + `htdocs/laravel_app/` > `APP_DEBUG=false SESSION_SECURE_COOKIE=true` + HTTPS + Brevo + CLOUDINARY_URL. No subir `bootstrap/cache/config.php`. Detalle en `DEPLOY.md`.

## 9. Tareas para Claude (en orden)

1. Fix mimes Transporte + unificar validate().
2. Cerrar mass-assignment Lugar store (only/FormRequest).
3. Alinear ResenaPolicy + README + tests.
4. Paginar resenas + aligerar admin dashboard (select/withAvg/cache).
5. Agrupar where/orWhere BusquedaService, escapar LIKE, cachear MapaService.
6. Transaccion + borrado Cloudinary en Lugar update.
7. Commits en bloques + gitignore binarios.
8. Re-verificar suite en local antes de exportar el `.sql` y actualizar cifras README/checklist.

## 10. Leer primero

`routes/web.php` · `bootstrap/app.php` · Controllers `{Lugar,Transporte,Resena,Favorito,Bienvenida,Admin}` · `Policies/ResenaPolicy.php` · Services `{Busqueda,Mapa,CloudinaryImage}` · `Concerns/ManagesImages.php` · Models `{User,Resena,Favorito}` · `.env.example` · `README.md` · `CHECKLIST_TURISMOHUILA_VALORACION.md` · `tests/Feature/SeguridadTest.php`

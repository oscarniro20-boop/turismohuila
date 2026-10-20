# Despliegue de Turismo por Colombia en InfinityFree (MySQL)

> Destino real: hosting `if0_42868030`, dominio `https://oscart.page.gd`, MySQL `sql105.infinityfree.com:3306`, base `if0_42868030_turismo`. No PostgreSQL, no Fly.io. Sin SSH ni artisan en servidor: la base se importa como `.sql` desde phpMyAdmin.

## 0. Antes de subir

1. Subidas solo desde el File Manager de `if0_42868030`.
2. Panel > Control Panel > PHP Config: PHP **8.3** (`composer.json` exige `^8.3`). Si solo hay 8.1/8.2, avisar antes de continuar.
3. Panel > MySQL Databases > New Database: escribir `turismo` para que quede `if0_42868030_turismo`.
4. Cuenta vieja `if0_42438171`: si quedaron archivos subidos antes (hay un `.env` con credenciales), borrarlos.
5. Confirmar en panel > Domains que `oscart.page.gd` esta ligado a `if0_42868030`.

## 1. Preparar el proyecto en local (cmd, no PowerShell)

```
cd C:\laragon\www\turismo-huila
git status --short
composer install --no-dev --optimize-autoloader
npm run build
dir bootstrap\cache
```

No debe existir `bootstrap\cache\config.php` (si existe, borrarlo: rutas locales rompen el servidor).

## 2. `.env` de produccion

Copiar el `.env` local a la carpeta de despliegue y dejar estas claves (conservar `APP_KEY` y `CLOUDINARY_URL` actuales; la contrasena MySQL sale del panel > MySQL Databases > "MySQL Password" y no se guarda en este archivo):

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://oscart.page.gd
DB_CONNECTION=mysql
DB_HOST=sql105.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_42868030_turismo
DB_USERNAME=if0_42868030
DB_PASSWORD=<contrasena MySQL del panel>
SESSION_SECURE_COOKIE=true
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=<usuario Brevo>
MAIL_PASSWORD=<clave Brevo NUEVA, rotar la anterior>
MAIL_ENCRYPTION=tls
```

`SESSION_DRIVER`, `CACHE_STORE` y `QUEUE_CONNECTION` son `database`: las tablas `sessions`, `cache`, `jobs` deben venir en el SQL importado.

## 3. Estructura en el servidor

```
htdocs/
  index.php, .htaccess, robots.txt, build/, imagenes/, galeria/, portadas/   <- contenido de public/
  laravel_app/                                                                <- todo el proyecto, con su .htaccess
```

1. Mover el contenido de `laravel_app/public/` a la raiz de `htdocs/`.
2. Editar `htdocs/index.php` con `scripts/deploy/htdocs-index.php` de este repo: las 3 lineas que usan `__DIR__.'/../...'` (maintenance.php, vendor/autoload.php, bootstrap/app.php) pasan a `__DIR__.'/laravel_app/...'`.
3. Crear `htdocs/laravel_app/.htaccess` con el contenido de `scripts/deploy/laravel_app.htaccess` (una sola linea: `Require all denied`).
4. Borrar `htdocs/index.html` (pagina de bienvenida de InfinityFree) y el archivo "files for your website".
5. Borrar de `htdocs/` cualquier `.sql`.

## 4. Base de datos

1. Verificar en local que existe un admin: `SELECT id,email,rol FROM users;` (HeidiSQL). InfinityFree no tiene SSH ni `artisan`; si la tabla esta vacia, crear el admin en local con tinker antes de exportar.
2. HeidiSQL > Exportar base de datos como SQL:
   - Base(s) de datos: Suprimir sin marcar, Crear sin marcar.
   - Tabla(s): Suprimir marcado, Crear marcado.
   - Datos: Insertar.
3. Panel > MySQL Databases > boton Admin de `if0_42868030_turismo` > pestana Importar > subir el `.sql` > Continuar.
4. Confirmar que aparecen `users`, `lugares`, `hoteles`, `resenas`, `sessions`, `migrations`, etc.

## 5. Verificacion

1. `https://oscart.page.gd` carga con estilos (si no, revisar `build/` en la raiz de `htdocs/`).
2. `https://oscart.page.gd/laravel_app/.env` debe dar 403.
3. Login admin.
4. `/mapa`, `/buscar` (buscar un nombre de lugar y de hotel), subir una imagen (Cloudinary), crear y eliminar una resena.
5. "Olvide mi contrasena": confirmar que llega el correo.
6. Si sale error 500: poner `APP_DEBUG=true` solo un momento para leer el error y volver a `false`.

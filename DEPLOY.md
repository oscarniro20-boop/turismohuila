# 🚀 Guía de Despliegue - TurismoHuila

## Despliegue en InfinityFree

### Requisitos Previos

- PHP 8.3
- MySQL
- Composer instalado localmente
- Node.js y NPM localmente

### 1. Preparación Local

```bash
# Instalar dependencias de producción
composer install --no-dev --optimize-autoloader

# Compilar assets
npm install
npm run build
```

### 2. Configuración de Variables de Entorno

Crea un archivo `.env` en el servidor (o sube uno desde local) con:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://turismohuila.epizy.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_XXXXXXX_turismo
DB_USERNAME=if0_XXXXXXX
DB_PASSWORD=tu_password

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.infinityfree.com
MAIL_PORT=25
```

### 3. Estructura en InfinityFree

```
public_html/
├── index.php          ← Modificado para Laravel
├── .htaccess          ← Reglas de reescritura
├── build/             ← Assets compilados
└── (sube únicamente el contenido de public/)

turismohuila/          ← Carpeta padre (fuera de public_html)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── artisan
└── .env
```

### 4. index.php Modificado

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determinar la ruta base (subir un nivel desde public_html)
$basePath = dirname(__DIR__);

// Registrar el autoloader de Composer
require $basePath.'/vendor/autoload.php';

// Arrancar Laravel
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

### 5. Permisos

```bash
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 775 storage/framework/cache/data/
chmod 775 storage/framework/sessions/
chmod 775 storage/framework/views/
```

### 6. Migraciones

**NOTA**: InfinityFree no proporciona acceso SSH. Ejecuta las migraciones localmente y exporta el SQL, o usa un cliente MySQL remoto como phpMyAdmin.

### 7. Cachés (ejecutadas localmente antes de subir)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 8. Archivos que NO deben subirse

- `.env` real (crea uno nuevo en el servidor)
- `vendor/` (sube el ZIP ya generado)
- `node_modules/`
- `tests/`
- `.git/`
- `storage/logs/*.log`
- `database/seeders/` (solo si no los necesitas)

### 9. Verificación Post-Despliegue

1. Acceder a la URL principal
2. Verificar que no aparezcan errores PHP
3. Probar login y registro
4. Probar búsqueda
5. Probar mapa
6. Verificar subida de imágenes
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Estructura InfinityFree: este index.php vive en htdocs/ y la app en htdocs/laravel_app/.
// Por eso las 3 rutas usan /laravel_app/ en lugar de /../ .
if (file_exists($maintenance = __DIR__.'/laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/laravel_app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/laravel_app/bootstrap/app.php';

$app->handleRequest(Request::capture());

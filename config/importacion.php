<?php

use App\Models\Agencia;
use App\Models\Guia;
use App\Models\Hotel;
use App\Models\Lugar;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Transporte;

/*
 * Importación masiva CSV de los módulos de turismo.
 *
 * El contrato CSV usa los NOMBRES REALES de atributos de cada modelo (no los
 * nombres de prototipos externos: no existen "precio_base", "sitio_web", etc.
 * en este esquema). Los medios (imagen/foto), relaciones y calificaciones se
 * gestionan por la interfaz web, no por CSV.
 */

return [

    // Límite de peso del archivo subido (KB). Controlado además por PHP upload_max_filesize.
    'max_peso_kb' => 2048,

    // Máximo de filas de datos por archivo (la cabecera no cuenta).
    'max_filas' => 5000,

    // Filas válidas que se confirman juntas en una transacción.
    'lote' => 100,

    // Máximo de errores detallados que se devuelven al panel (el resto solo se cuenta).
    'max_errores_detalle' => 50,

    'modulos' => [

        'lugares' => [
            'label' => 'Lugares',
            'modelo' => Lugar::class,
            'municipio_requerido' => true,
            'autor' => true,
            'clave_duplicados' => ['nombre', 'departamento', 'municipio'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Cañón del Río Magdalena'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'municipio' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Pitalito'],
                'categoria' => ['requerido' => true, 'reglas' => 'required|string|max:100', 'ejemplo' => 'Naturaleza'],
                'descripcion' => ['requerido' => true, 'reglas' => 'required|string', 'ejemplo' => 'Cañón natural al sur del departamento.'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '1.8537'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-76.0507'],
            ],
        ],

        'hoteles' => [
            'label' => 'Hoteles',
            'modelo' => Hotel::class,
            'municipio_requerido' => true,
            'clave_duplicados' => ['nombre', 'departamento', 'municipio', 'direccion'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Hotel Terrón Blanco'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'municipio' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Neiva'],
                'descripcion' => ['requerido' => true, 'reglas' => 'required|string', 'ejemplo' => 'Hotel céntrico con vista al río.'],
                'precio_noche' => ['requerido' => true, 'reglas' => 'required|numeric|min:0', 'ejemplo' => '180000'],
                'estrellas' => ['requerido' => true, 'reglas' => 'required|integer|min:1|max:5', 'ejemplo' => '4'],
                'direccion' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Cra 5 # 12-34'],
                'telefono' => ['requerido' => false, 'reglas' => 'nullable|string|max:20', 'ejemplo' => '8876543'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '2.9273'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-75.2819'],
            ],
        ],

        'restaurantes' => [
            'label' => 'Restaurantes',
            'modelo' => Restaurante::class,
            'municipio_requerido' => true,
            'clave_duplicados' => ['nombre', 'departamento', 'municipio', 'direccion'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'La Cosecha'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'municipio' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Neiva'],
                'descripcion' => ['requerido' => true, 'reglas' => 'required|string', 'ejemplo' => 'Cocina huilense de autor.'],
                'tipo_cocina' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Típica'],
                'precio_promedio' => ['requerido' => true, 'reglas' => 'required|numeric|min:0', 'ejemplo' => '35000'],
                'direccion' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Cra 7 # 15-20'],
                'telefono' => ['requerido' => false, 'reglas' => 'nullable|string|max:20', 'ejemplo' => '8871234'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '2.9349'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-75.2819'],
            ],
        ],

        'tours' => [
            'label' => 'Tours',
            'modelo' => Tour::class,
            'municipio_requerido' => true,
            'clave_duplicados' => ['nombre', 'departamento', 'municipio'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Tour Tatacoa'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'municipio' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Villavieja'],
                'descripcion' => ['requerido' => true, 'reglas' => 'required|string', 'ejemplo' => 'Recorrido guiado por el desierto.'],
                'precio' => ['requerido' => true, 'reglas' => 'required|numeric|min:0', 'ejemplo' => '80000'],
                'duracion_dias' => ['requerido' => true, 'reglas' => 'required|integer|min:1', 'ejemplo' => '1'],
                'incluye' => ['requerido' => true, 'reglas' => 'required|string', 'ejemplo' => 'Transporte y guía.'],
                'cupo_maximo' => ['requerido' => true, 'reglas' => 'required|integer|min:1', 'ejemplo' => '15'],
                'telefono_contacto' => ['requerido' => false, 'reglas' => 'nullable|string|max:50', 'ejemplo' => '8879999'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '3.2153'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-75.1589'],
            ],
        ],

        // Sin columna municipio: este módulo usa "zonas" como ámbito geográfico.
        'guias' => [
            'label' => 'Guías',
            'modelo' => Guia::class,
            'municipio_requerido' => false,
            'clave_duplicados' => ['nombre', 'departamento'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'María Pérez'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'bio' => ['requerido' => false, 'reglas' => 'nullable|string|max:1000', 'ejemplo' => 'Guía certificada con 10 años de experiencia.'],
                'idiomas' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Español, Inglés'],
                'experiencia_anios' => ['requerido' => false, 'reglas' => 'nullable|integer|min:0', 'ejemplo' => '10'],
                'zonas' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Neiva, Pitalito, San Agustín'],
                'whatsapp' => ['requerido' => false, 'reglas' => 'nullable|string|max:20', 'ejemplo' => '3001234567'],
                'email' => ['requerido' => false, 'reglas' => 'nullable|email|max:255', 'ejemplo' => 'guia@example.com'],
                'telefono' => ['requerido' => false, 'reglas' => 'nullable|string|max:20', 'ejemplo' => '8870000'],
                'activo' => ['requerido' => false, 'reglas' => 'nullable|boolean', 'ejemplo' => '1'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '2.9273'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-75.2819'],
            ],
        ],

        // Sin columna descripcion: este esquema no la tiene en agencias.
        'agencias' => [
            'label' => 'Agencias',
            'modelo' => Agencia::class,
            'municipio_requerido' => false,
            'clave_duplicados' => ['nombre'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Agencia Andina'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'municipio' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Neiva'],
                'telefono_contacto' => ['requerido' => false, 'reglas' => 'nullable|string|max:50', 'ejemplo' => '8876000'],
                'whatsapp' => ['requerido' => false, 'reglas' => 'nullable|string|max:50', 'ejemplo' => '3009876543'],
                'email' => ['requerido' => false, 'reglas' => 'nullable|email|max:255', 'ejemplo' => 'contacto@example.com'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '2.9273'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-75.2819'],
            ],
        ],

        'transportes' => [
            'label' => 'Transportes',
            'modelo' => Transporte::class,
            'municipio_requerido' => true,
            'clave_duplicados' => ['nombre', 'departamento', 'municipio', 'tipo'],
            'columnas' => [
                'nombre' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Rutas del Sur'],
                'departamento' => ['requerido' => false, 'reglas' => 'nullable|string|max:255', 'ejemplo' => 'Huila'],
                'municipio' => ['requerido' => true, 'reglas' => 'required|string|max:255', 'ejemplo' => 'Pitalito'],
                'descripcion' => ['requerido' => true, 'reglas' => 'required|string', 'ejemplo' => 'Servicio intermunicipal.'],
                'tipo' => ['requerido' => true, 'reglas' => 'required|string|max:100', 'ejemplo' => 'Bus'],
                'precio' => ['requerido' => false, 'reglas' => 'nullable|numeric|min:0', 'ejemplo' => '12000'],
                'capacidad' => ['requerido' => false, 'reglas' => 'nullable|integer|min:1', 'ejemplo' => '40'],
                'telefono' => ['requerido' => false, 'reglas' => 'nullable|string|max:20', 'ejemplo' => '8875555'],
                'latitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-90,90', 'ejemplo' => '1.8537'],
                'longitud' => ['requerido' => false, 'reglas' => 'nullable|numeric|between:-180,180', 'ejemplo' => '-76.0507'],
            ],
        ],

    ],
];

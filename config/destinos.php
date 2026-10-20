<?php

/**
 * Regiones naturales de Colombia para la experiencia de destinos.
 *
 * Clasificación de referencia de las 6 regiones naturales de Colombia
 * (convención geográfica DANE / educativa). Cada uno de los 33
 * departamentos del catálogo DIVIPOLA (config/colombia.php) se asigna a
 * su región principal; algunos departamentos abarcan varias regiones y
 * aquí se usa la predominante para agrupar la navegación.
 *
 * Este mapa vive en un archivo propio (no en config/colombia.php) para que
 * no se pierda si scripts/build-colombia.php regenera el catálogo DANE.
 */
return [
    // 'Región' => [departamentos...] (cada departamento aparece una sola vez)
    'regiones' => [
        'Andina' => [
            'Antioquia',
            'Bogotá D.C.',
            'Boyacá',
            'Caldas',
            'Cundinamarca',
            'Huila',
            'Norte de Santander',
            'Quindío',
            'Risaralda',
            'Santander',
            'Tolima',
            'Valle del Cauca',
        ],
        'Caribe' => [
            'Atlántico',
            'Bolívar',
            'Cesar',
            'Córdoba',
            'La Guajira',
            'Magdalena',
            'Sucre',
        ],
        'Pacífica' => [
            'Cauca',
            'Chocó',
            'Nariño',
        ],
        'Amazonia' => [
            'Amazonas',
            'Caquetá',
            'Guainía',
            'Guaviare',
            'Putumayo',
            'Vaupés',
        ],
        'Orinoquía' => [
            'Arauca',
            'Casanare',
            'Meta',
            'Vichada',
        ],
        'Insular' => [
            'Archipiélago de San Andrés, Providencia y Santa Catalina',
        ],
    ],

    // Metadatos de presentación por región (icono + descripción corta)
    'info' => [
        'Andina' => [
            'icono' => '⛰️',
            'descripcion' => 'Cordilleras, cafetería, ciudades históricas y paisajes de altura.',
        ],
        'Caribe' => [
            'icono' => '🏖️',
            'descripcion' => 'Playas, ciudades coloniales y la gastronomía del mar.',
        ],
        'Pacífica' => [
            'icono' => '🌊',
            'descripcion' => 'Selva húmeda, ballenas y biodiversidad en el litoral pacífico.',
        ],
        'Amazonia' => [
            'icono' => '🌳',
            'descripcion' => 'Selva amazónica, ríos y comunidades indígenas.',
        ],
        'Orinoquía' => [
            'icono' => '🐂',
            'descripcion' => 'Llanos, sabanas y la cultura de la tierra caliente.',
        ],
        'Insular' => [
            'icono' => '🏝️',
            'descripcion' => 'Islas y cayería del Caribe colombiano.',
        ],
    ],
];

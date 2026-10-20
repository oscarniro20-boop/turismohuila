<?php

/**
 * Generador de config/colombia.php a partir del CSV oficial DIVIPOLA (DANE).
 *
 * Fuente: https://www.datos.gov.co/resource/gdxc-w37w.csv
 * Columnas: cod_dpto,dpto,cod_mpio,nom_mpio,tipo_municipio,longitud,latitud
 *
 * Uso: php scripts/build-colombia.php
 *
 * - Parser robusto: fgetcsv respeta campos entrecomillados multilínea
 *   (p. ej. "SAN ANDRÉS, PROVIDENCIA Y\nSANTA CATALINA").
 * - Los códigos se conservan como cadenas (no se pierden ceros iniciales).
 * - Salida determinista y ordenada alfabéticamente.
 * - No inventa ni elimina información: usa exactamente los nombres del CSV.
 */

$csv = __DIR__ . '/../database/data/divipola.csv';

if (! is_file($csv)) {
    fwrite(STDERR, "No se encontró database/data/divipola.csv\n");
    exit(1);
}

/** Convierte "SAN ANDRÉS" -> "San Andrés" respetando tildes y conectores. */
function titulo(string $texto): string
{
    $minusculas = ['de', 'del', 'la', 'las', 'los', 'y', 'e', 'el'];
    $palabras = preg_split('/\s+/u', mb_strtolower(trim($texto), 'UTF-8')) ?: [];
    $salida = [];
    foreach ($palabras as $i => $p) {
        if ($i > 0 && in_array($p, $minusculas, true)) {
            $salida[] = $p;
        } else {
            $salida[] = mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($p, 1, null, 'UTF-8');
        }
    }

    return implode(' ', $salida);
}

/** Normaliza el nombre del departamento. Bogotá D.C. es un caso especial. */
function nombreDepartamento(string $dpto): string
{
    $d = trim($dpto);
    if (mb_strtoupper($d, 'UTF-8') === 'BOGOTÁ, D.C.') {
        return 'Bogotá D.C.';
    }

    return titulo($d);
}

/** Normaliza el nombre del municipio. */
function nombreMunicipio(string $nom): string
{
    $n = trim($nom);
    // Bogotá, D.C. como municipio único del departamento.
    if (mb_strtoupper($n, 'UTF-8') === 'BOGOTÁ, D.C.') {
        return 'Bogotá D.C.';
    }

    return titulo($n);
}

$handle = fopen($csv, 'r');
if ($handle === false) {
    fwrite(STDERR, "No se pudo abrir el CSV.\n");
    exit(1);
}

$cabecera = fgetcsv($handle, 0, ',', '"', '\\');
if ($cabecera === false) {
    fwrite(STDERR, "CSV vacío o inválido.\n");
    exit(1);
}
$cabecera = array_map(static fn ($c) => strtolower(trim((string) $c)), $cabecera);

$necesarias = ['cod_dpto', 'dpto', 'cod_mpio', 'nom_mpio'];
foreach ($necesarias as $col) {
    if (! in_array($col, $cabecera, true)) {
        fwrite(STDERR, "Falta la columna obligatoria: {$col}\n");
        exit(1);
    }
}

$idx = array_flip($cabecera);
$mapa = [];        // 'Departamento' => set de municipios
$municipios = [];  // 'Departamento|Municipio' => ['cod_dpto','cod_mpio']
$filas = 0;

while (($fila = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
    if (count($fila) === 1 && trim((string) $fila[0]) === '') {
        continue; // línea en blanco
    }
    $dep = nombreDepartamento((string) ($fila[$idx['dpto']] ?? ''));
    $mun = nombreMunicipio((string) ($fila[$idx['nom_mpio']] ?? ''));
    if ($dep === '' || $mun === '') {
        continue;
    }
    $codDpto = trim((string) ($fila[$idx['cod_dpto']] ?? ''));
    $codMpio = trim((string) ($fila[$idx['cod_mpio']] ?? ''));

    $mapa[$dep][$mun] = true;
    $municipios[$dep . '|' . $mun] = ['cod_dpto' => $codDpto, 'cod_mpio' => $codMpio];
    $filas++;
}
fclose($handle);

ksort($mapa, SORT_NATURAL | SORT_FLAG_CASE);

$salida = "<?php\n\nreturn [\n    'generado' => '" . date('Y-m-d') . "',\n    'filas_csv' => {$filas},\n";
$salida .= "    // 'Departamento' => ['Municipio', ...] (orden alfabético)\n    'departamentos' => [\n";
foreach ($mapa as $dep => $set) {
    $muns = array_keys($set);
    sort($muns, SORT_NATURAL | SORT_FLAG_CASE);
    $salida .= "        '" . addslashes($dep) . "' => [\n";
    foreach ($muns as $m) {
        $salida .= "            '" . addslashes($m) . "',\n";
    }
    $salida .= "        ],\n";
}
$salida .= "    ],\n";
ksort($municipios, SORT_NATURAL | SORT_FLAG_CASE);
$salida .= "    // 'Departamento|Municipio' => ['cod_dpto','cod_mpio'] (códigos oficiales como cadenas)\n    'municipios' => [\n";
foreach ($municipios as $clave => $codigos) {
    $salida .= "        '" . addslashes($clave) . "' => ['cod_dpto' => '" . $codigos['cod_dpto'] . "', 'cod_mpio' => '" . $codigos['cod_mpio'] . "'],\n";
}
$salida .= "    ],\n];\n";

$destino = __DIR__ . '/../config/colombia.php';
file_put_contents($destino, $salida);

$totalMunicipios = count($municipios);
echo "config/colombia.php generado: " . count($mapa) . " departamentos, {$totalMunicipios} municipios ({$filas} filas CSV).\n";

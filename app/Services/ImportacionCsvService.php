<?php

namespace App\Services;

use App\Exceptions\ImportacionCsvException;
use App\Rules\MunicipioDeDepartamento;
use App\Support\Colombia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Importación masiva CSV de los módulos de turismo.
 *
 * - La cabecera usa los NOMBRES REALES de atributos (lista permitida por
 *   módulo en config/importacion.php); una columna desconocida rechaza el
 *   archivo en lugar de descartar datos en silencio.
 * - Separadores admitidos: coma, punto y coma y tabulador (autodetectados).
 * - Codificación requerida: UTF-8 (se tolera BOM al inicio).
 * - Filas validadas con las mismas reglas geográficas de los controladores
 *   (catálogo DIVIPOLA + MunicipioDeDepartamento); nunca se reasignan
 *   municipios por aproximación.
 * - Solo inserta: nunca actualiza ni borra registros existentes.
 */
class ImportacionCsvService
{
    /** @return array<string, string> Categorías importables (lista permitida). */
    public static function categorias(): array
    {
        $categorias = [];

        foreach (config('importacion.modulos', []) as $clave => $definicion) {
            $categorias[$clave] = $definicion['label'];
        }

        return $categorias;
    }

    public function esCategoriaValida(string $categoria): bool
    {
        return config("importacion.modulos.$categoria") !== null;
    }

    /**
     * Definición del módulo o excepción controlada si la categoría no existe.
     *
     * @return array<string, mixed>
     */
    public function definicion(string $modulo): array
    {
        $definicion = config("importacion.modulos.$modulo");

        if (! is_array($definicion)) {
            throw new ImportacionCsvException('Categoría de importación no válida.');
        }

        return $definicion;
    }

    /**
     * Plantilla CSV descargable: cabecera con las columnas reales y una fila
     * de ejemplo. Usa punto y coma (Excel es-ES) y BOM UTF-8 para tildes.
     */
    public function plantilla(string $modulo): string
    {
        $definicion = $this->definicion($modulo);
        $columnas = array_keys($definicion['columnas']);

        $ejemplo = [];
        foreach ($columnas as $columna) {
            $ejemplo[] = $definicion['columnas'][$columna]['ejemplo'];
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columnas, ';', '"', '\\');
        fputcsv($handle, $ejemplo, ';', '"', '\\');
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return "\xEF\xBB\xBF".$csv;
    }

    /**
     * Importa un archivo CSV y devuelve el resumen de la operación.
     *
     * @return array{leidas:int, importadas:int, rechazadas:int, duplicados:int, errores:array<int, array{fila:int, mensaje:string}>, resultado:string}
     *
     * @throws ImportacionCsvException Error fatal del archivo (nada persistido).
     */
    public function importar(string $ruta, string $modulo, ?int $usuarioId = null): array
    {
        $definicion = $this->definicion($modulo);

        $manejo = @fopen($ruta, 'r');
        if ($manejo === false) {
            throw new ImportacionCsvException('No se pudo abrir el archivo subido.');
        }

        try {
            $primeraLinea = $this->siguienteLineaNoVacia($manejo);

            if ($primeraLinea === null) {
                throw new ImportacionCsvException('El archivo está vacío.');
            }

            if (str_starts_with($primeraLinea, "\xEF\xBB\xBF")) {
                $primeraLinea = substr($primeraLinea, 3);
            }

            if (! mb_check_encoding($primeraLinea, 'UTF-8')) {
                throw new ImportacionCsvException('El archivo no tiene una codificación UTF-8 válida.');
            }

            $separador = $this->detectarSeparador($primeraLinea);
            $encabezados = $this->parsearEncabezado($primeraLinea, $separador);

            $this->validarEncabezados($encabezados, $definicion);

            // Pasada 1: contar filas de datos ANTES de escribir cualquier registro.
            $totalFilas = 0;
            while (($campos = fgetcsv($manejo, null, $separador, '"', '\\')) !== false) {
                if (! $this->filaVacia($campos)) {
                    $totalFilas++;
                }
            }

            $maxFilas = (int) config('importacion.max_filas');
            if ($totalFilas > $maxFilas) {
                throw new ImportacionCsvException(
                    "El archivo supera el máximo permitido de {$maxFilas} filas de datos (se recibieron: {$totalFilas})."
                );
            }

            if ($totalFilas === 0) {
                throw new ImportacionCsvException('El archivo no contiene filas de datos.');
            }

            // Pasada 2: validación fila a fila y persistencia por lotes.
            rewind($manejo);
            fgetcsv($manejo, null, $separador, '"', '\\'); // consumir cabecera

            $resumen = [
                'leidas' => 0,
                'importadas' => 0,
                'rechazadas' => 0,
                'duplicados' => 0,
                'errores' => [],
            ];

            $vistas = [];   // claves de duplicado ya vistas dentro del archivo
            $buffer = [];   // filas válidas pendientes de confirmar en BD
            $numeroFila = 0;

            while (($campos = fgetcsv($manejo, null, $separador, '"', '\\')) !== false) {
                if ($this->filaVacia($campos)) {
                    continue;
                }

                $numeroFila++;
                $resumen['leidas']++;

                $validada = $this->validarFila($numeroFila, $campos, $encabezados, $definicion);

                if ($validada['errores'] !== []) {
                    $this->rechazar($resumen, $numeroFila, implode(' ', $validada['errores']));
                    continue;
                }

                $datos = $validada['datos'];
                $clave = $this->claveDuplicados($datos, $definicion['clave_duplicados']);

                if (isset($vistas[$clave])) {
                    $resumen['duplicados']++;
                    $this->rechazar($resumen, $numeroFila, 'Fila duplicada dentro del archivo (idéntica a una fila anterior); no se importa.');
                    continue;
                }

                if ($this->existeEnBase($definicion, $datos)) {
                    $resumen['duplicados']++;
                    $this->rechazar($resumen, $numeroFila, 'El registro ya existe en la base de datos; no se duplica.');
                    continue;
                }

                $vistas[$clave] = true;

                if (($definicion['autor'] ?? false) && $usuarioId !== null) {
                    $datos['user_id'] = $usuarioId;
                }

                $buffer[] = ['fila' => $numeroFila, 'datos' => $datos];

                if (count($buffer) >= (int) config('importacion.lote')) {
                    $this->persistirLote($definicion, $buffer, $resumen);
                    $buffer = [];
                }
            }

            if ($buffer !== []) {
                $this->persistirLote($definicion, $buffer, $resumen);
            }
        } finally {
            fclose($manejo);
        }

        $resumen['resultado'] = $resumen['importadas'] === $resumen['leidas']
            ? 'completa'
            : ($resumen['importadas'] > 0 ? 'parcial' : 'sin_importar');

        $this->registrarOperacion($modulo, $usuarioId, $resumen);

        return $resumen;
    }

    /**
     * Valida una fila contra las reglas del módulo y el catálogo geográfico.
     * Devuelve los datos saneados (solo columnas conocidas del módulo) o los
     * motivos de rechazo. Nunca corrige municipios por aproximación.
     *
     * @return array{datos: array<string, mixed>, errores: array<int, string>}
     */
    private function validarFila(int $numeroFila, array $campos, array $encabezados, array $definicion): array
    {
        if (count($campos) !== count($encabezados)) {
            return [
                'datos' => [],
                'errores' => ['La fila tiene '.count($campos).' columnas y la cabecera indica '.count($encabezados).'.'],
            ];
        }

        if (! mb_check_encoding(implode(' ', array_map(strval(...), $campos)), 'UTF-8')) {
            return ['datos' => [], 'errores' => ['La fila contiene caracteres que no son UTF-8 válido.']];
        }

        $crudo = array_combine($encabezados, $campos);

        $fila = [];
        foreach (array_keys($definicion['columnas']) as $columna) {
            $valor = trim((string) ($crudo[$columna] ?? ''));
            $fila[$columna] = $valor === '' ? null : $valor;
        }

        $errores = [];
        $reglas = [];
        foreach ($definicion['columnas'] as $columna => $meta) {
            $reglas[$columna] = $meta['reglas'];
        }

        $departamento = $fila['departamento'] ?? null;
        $municipio = $fila['municipio'] ?? null;

        // Departamento contra el catálogo oficial (mismo criterio que el CRUD).
        if ($departamento !== null
            && array_key_exists('departamento', $definicion['columnas'])
            && ! Colombia::existeDepartamento($departamento)) {
            $errores[] = "El departamento «{$departamento}» no existe en el catálogo oficial (DIVIPOLA).";
        }

        // El municipio debe pertenecer al departamento de la misma fila.
        if ($municipio !== null && array_key_exists('municipio', $definicion['columnas'])) {
            if ($departamento === null) {
                if ($definicion['municipio_requerido']) {
                    // Default documentado de la columna (igual que la BD).
                    $departamento = 'Huila';
                    $fila['departamento'] = 'Huila';
                } else {
                    $errores[] = 'Si indica un municipio debe indicar también el departamento.';
                }
            }

            if ($departamento !== null) {
                $reglas['municipio'] = array_merge(
                    explode('|', $definicion['columnas']['municipio']['reglas']),
                    [new MunicipioDeDepartamento($departamento)]
                );
            }
        }

        $validador = Validator::make($fila, $reglas);

        // validated() lanza ValidationException si el validador falló; solo se
        // llama cuando no hay errores, para devolver el motivo por fila.
        if ($validador->fails()) {
            return [
                'datos' => [],
                'errores' => array_merge($errores, $validador->errors()->all()),
            ];
        }

        return ['datos' => $validador->validated(), 'errores' => $errores];
    }

    /** @return array<int, string> Encabezados en minúsculas y sin espacios. */
    private function parsearEncabezado(string $linea, string $separador): array
    {
        return array_map(
            fn ($celda) => mb_strtolower(trim((string) $celda), 'UTF-8'),
            str_getcsv($linea, $separador, '"', '\\')
        );
    }

    /**
     * Elige el separador que produce más columnas en la cabecera.
     * Se admiten coma, punto y coma y tabulador.
     */
    private function detectarSeparador(string $linea): string
    {
        $mejor = ',';
        $maximo = 0;

        foreach ([',', ';', "\t"] as $separador) {
            $columnas = count(str_getcsv($linea, $separador, '"', '\\'));
            if ($columnas > $maximo) {
                $maximo = $columnas;
                $mejor = $separador;
            }
        }

        if ($maximo < 2) {
            throw new ImportacionCsvException(
                'No se reconoció el separador del archivo (se admiten coma, punto y coma y tabulador).'
            );
        }

        return $mejor;
    }

    /**
     * La cabecera debe ser única, estar compuesta solo por columnas conocidas
     * del módulo (lista permitida) e incluir todas las requeridas.
     *
     * @param array<int, string> $encabezados
     * @param array<string, mixed> $definicion
     */
    private function validarEncabezados(array $encabezados, array $definicion): void
    {
        $encabezados = array_values(array_filter($encabezados, fn ($c) => $c !== ''));

        if (count($encabezados) < 2) {
            throw new ImportacionCsvException('La cabecera del archivo debe tener al menos dos columnas.');
        }

        $repetidas = array_unique(array_diff_assoc($encabezados, array_unique($encabezados)));
        if ($repetidas !== []) {
            throw new ImportacionCsvException('La cabecera repite columnas: '.implode(', ', $repetidas).'.');
        }

        $permitidas = array_keys($definicion['columnas']);
        $desconocidas = array_diff($encabezados, $permitidas);
        if ($desconocidas !== []) {
            throw new ImportacionCsvException(
                'La cabecera contiene columnas desconocidas: '.implode(', ', $desconocidas)
                .'. Columnas válidas para '.$definicion['label'].': '.implode(', ', $permitidas).'.'
            );
        }

        $faltantes = [];
        foreach ($definicion['columnas'] as $columna => $meta) {
            if ($meta['requerido'] && ! in_array($columna, $encabezados, true)) {
                $faltantes[] = $columna;
            }
        }
        if ($faltantes !== []) {
            throw new ImportacionCsvException('Faltan columnas requeridas en la cabecera: '.implode(', ', $faltantes).'.');
        }
    }

    /** Primera línea no vacía del archivo (sin BOM) o null si está vacío. */
    private function siguienteLineaNoVacia($manejo): ?string
    {
        while (($linea = fgets($manejo)) !== false) {
            if (trim($linea) !== '') {
                return $linea;
            }
        }

        return null;
    }

    /** @param array<int, mixed> $campos */
    private function filaVacia(array $campos): bool
    {
        foreach ($campos as $campo) {
            if (trim((string) ($campo ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Cuenta la fila como rechazada y adjunta el motivo (con tope de detalle,
     * el resto solo se contabiliza).
     *
     * @param array<string, mixed> $resumen
     */
    private function rechazar(array &$resumen, int $fila, string $motivo): void
    {
        $resumen['rechazadas']++;

        $tope = (int) config('importacion.max_errores_detalle', 50);
        if (count($resumen['errores']) < $tope) {
            $resumen['errores'][] = ['fila' => $fila, 'mensaje' => $motivo];
        }
    }

    /**
     * Clave compuesta de duplicado en minúsculas (nunca solo el nombre).
     *
     * @param array<string, mixed> $datos
     * @param array<int, string> $columnas
     */
    private function claveDuplicados(array $datos, array $columnas): string
    {
        $partes = [];
        foreach ($columnas as $columna) {
            $partes[] = mb_strtolower(trim((string) ($datos[$columna] ?? '')), 'UTF-8');
        }

        return implode('|', $partes);
    }

    /**
     * ¿Existe ya un registro con la misma clave compuesta en la BD?
     * Comparación exacta por columna (los nombres provienen de la lista
     * permitida de config, nunca del archivo).
     *
     * @param array<string, mixed> $definicion
     * @param array<string, mixed> $datos
     */
    private function existeEnBase(array $definicion, array $datos): bool
    {
        $consulta = $definicion['modelo']::query();

        foreach ($definicion['clave_duplicados'] as $columna) {
            $valor = $datos[$columna] ?? null;
            if ($valor === null) {
                continue;
            }
            $consulta->where($columna, (string) $valor);
        }

        return $consulta->exists();
    }

    /**
     * Confirma un lote de filas válidas en una transacción. Si el lote falla,
     * se revierte completo y se reintenta fila por fila para aislar el fallo:
     * una fila solo se cuenta como importada tras confirmarse su escritura.
     *
     * @param array<string, mixed> $definicion
     * @param array<int, array{fila:int, datos:array<string, mixed>}> $filas
     * @param array<string, mixed> $resumen
     */
    private function persistirLote(array $definicion, array $filas, array &$resumen): void
    {
        $modelo = $definicion['modelo'];

        try {
            DB::transaction(function () use ($modelo, $filas) {
                foreach ($filas as $fila) {
                    $modelo::create($fila['datos']);
                }
            });

            $resumen['importadas'] += count($filas);

            return;
        } catch (\Throwable $e) {
            // El lote completo quedó revertido; se reintenta fila por fila.
            Log::warning('importacion_csv_fallo_lote', [
                'modulo' => $definicion['label'],
                'filas' => count($filas),
                'error' => $e->getMessage(),
            ]);
        }

        foreach ($filas as $fila) {
            try {
                DB::transaction(fn () => $modelo::create($fila['datos']));
                $resumen['importadas']++;
            } catch (\Throwable $e) {
                Log::error('importacion_csv_fallo_fila', [
                    'modulo' => $definicion['label'],
                    'fila' => $fila['fila'],
                    'error' => $e->getMessage(),
                ]);
                $this->rechazar($resumen, $fila['fila'], 'No fue posible guardar la fila en la base de datos.');
            }
        }
    }

    /**
     * Registro de la operación para trazabilidad administrativa.
     *
     * PUNTO DE INTEGRACIÓN DE AUDITORÍA (Etapa D): hoy la operación se registra
     * en el log de la aplicación; cuando exista la tabla central de auditoría,
     * este evento debe persistirse allí con los mismos campos (usuario, módulo,
     * fecha y contadores). No se crea una segunda tabla de auditoría para la
     * importación y no se registran datos personales adicionales.
     *
     * @param array<string, mixed> $resumen
     */
    private function registrarOperacion(string $modulo, ?int $usuarioId, array $resumen): void
    {
        Log::info('importacion_csv', [
            'user_id' => $usuarioId,
            'modulo' => $modulo,
            'leidas' => $resumen['leidas'],
            'importadas' => $resumen['importadas'],
            'rechazadas' => $resumen['rechazadas'],
            'duplicados' => $resumen['duplicados'],
            'resultado' => $resumen['resultado'],
        ]);

        // Auditoría central (Etapa D): misma información en la tabla única,
        // sin datos personales adicionales. Nunca interrumpe la importación.
        app(AuditoriaService::class)->registrar('importacion.csv', 'importacion', null, [
            'user_id' => $usuarioId,
            'modulo' => $modulo,
            'leidas' => $resumen['leidas'],
            'importadas' => $resumen['importadas'],
            'rechazadas' => $resumen['rechazadas'],
            'duplicados' => $resumen['duplicados'],
            'resultado' => $resumen['resultado'],
        ]);
    }
}

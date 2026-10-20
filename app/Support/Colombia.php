<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Acceso de solo lectura al catálogo geográfico nacional (DIVIPOLA).
 *
 * Los datos provienen de config/colombia.php, generado desde el CSV oficial
 * del DANE mediante scripts/build-colombia.php. No inventa información:
 * refleja fielmente el dataset oficial.
 */
class Colombia
{
    /**
     * Caché del mapa por petición/proceso: el catálogo es de solo lectura
     * y la importación CSV consulta la combinación dep+municipio por fila.
     *
     * @var array<string, array<int, string>>|null
     */
    private static ?array $mapaCache = null;

    /**
     * Mapa 'Departamento' => ['Municipio', ...] construido desde las claves
     * "Departamento|Municipio" del catálogo oficial. Ordenado alfabéticamente.
     *
     * @return array<string, array<int, string>>
     */
    public static function mapa(): array
    {
        if (self::$mapaCache !== null) {
            return self::$mapaCache;
        }

        $municipios = config('colombia.municipios', []);
        $mapa = [];

        foreach (array_keys($municipios) as $clave) {
            if (! str_contains($clave, '|')) {
                continue;
            }

            [$dep, $mun] = explode('|', $clave, 2);
            $mapa[$dep][] = $mun;
        }

        foreach ($mapa as $dep => $muns) {
            sort($mapa[$dep], SORT_NATURAL | SORT_FLAG_CASE);
        }

        ksort($mapa, SORT_NATURAL | SORT_FLAG_CASE);

        return self::$mapaCache = $mapa;
    }

    /** @return array<int, string> Lista de departamentos ordenada. */
    public static function departamentos(): array
    {
        return array_keys(self::mapa());
    }

    /** @return array<int, string> Municipios de un departamento (vacío si no existe). */
    public static function municipios(string $departamento): array
    {
        return self::mapa()[$departamento] ?? [];
    }

    /** ¿Existe la combinación departamento + municipio? */
    public static function existe(string $departamento, string $municipio): bool
    {
        return in_array($municipio, self::municipios($departamento), true);
    }

    /** ¿Existe el departamento? */
    public static function existeDepartamento(string $departamento): bool
    {
        return array_key_exists($departamento, self::mapa());
    }

    /**
     * Regiones naturales: 'Región' => [departamentos...].
     *
     * @return array<string, array<int, string>>
     */
    public static function regiones(): array
    {
        return config('destinos.regiones', []);
    }

    /** @return array<int, string> Nombres de regiones en orden de agrupación. */
    public static function nombresRegiones(): array
    {
        return array_keys(self::regiones());
    }

    /**
     * Región natural a la que pertenece un departamento.
     * Null si el departamento no está catalogado.
     */
    public static function regionDe(string $departamento): ?string
    {
        foreach (self::regiones() as $region => $deps) {
            if (in_array($departamento, $deps, true)) {
                return $region;
            }
        }

        return null;
    }

    /** @return array<int, string> Departamentos de una región (vacío si no existe). */
    public static function departamentosDeRegion(string $region): array
    {
        return self::regiones()[$region] ?? [];
    }

    /**
     * Metadatos de presentación (icono/descripción) de una región.
     *
     * @return array{icono?:string, descripcion?:string}
     */
    public static function infoRegion(string $region): array
    {
        return config('destinos.info')[$region] ?? [];
    }

    /** ¿Existe la región? */
    public static function existeRegion(string $region): bool
    {
        return array_key_exists($region, self::regiones());
    }

    /**
     * Slug URL estable de un departamento (sin tildes, signos ni mayúsculas).
     * Se usa como parámetro de /destinos/{departamento}.
     */
    public static function slug(string $departamento): string
    {
        return Str::slug($departamento);
    }

    /**
     * Departamento canónico del catálogo a partir de su slug.
     * Normaliza la entrada (tildes/mayúsculas/puntos) y devuelve null si
     * el slug no corresponde a ningún departamento.
     */
    public static function departamentoPorSlug(string $slug): ?string
    {
        $buscado = self::slug($slug);

        if ($buscado === '') {
            return null;
        }

        foreach (self::departamentos() as $departamento) {
            if (self::slug($departamento) === $buscado) {
                return $departamento;
            }
        }

        return null;
    }

    /**
     * Códigos DIVIPOLA oficiales de un municipio.
     *
     * @return array{cod_dpto:string, cod_mpio:string}|null
     */
    public static function codigos(string $departamento, string $municipio): ?array
    {
        return config('colombia.municipios')[$departamento . '|' . $municipio] ?? null;
    }
}

<?php

namespace App\Http\Controllers;

use App\Concerns\ManagesImages;
use App\Models\Agencia;
use App\Models\Departamento;
use App\Models\Guia;
use App\Models\Hotel;
use App\Models\Lugar;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Transporte;
use App\Support\Colombia;

/**
 * Destinos nacionales: navegación por las 6 regiones naturales de Colombia
 * y el detalle de cada uno de los 33 departamentos del catálogo DIVIPOLA.
 *
 * No crea datos nuevos: lee config/destinos.php (mapa regional) y
 * config/colombia.php (catálogo geográfico), y reutiliza el scope
 * HasUbicacion::ubicacion() de los 7 módulos turísticos.
 */
class DestinoController extends Controller
{
    // Reutiliza obtenerFavoritosIds() (mismo helper que usan los 7
    // controladores de módulos) sin duplicar la consulta de favoritos.
    use ManagesImages;

    /**
     * Listado de destinos agrupado por región natural, con el conteo real
     * de servicios por departamento (una consulta agregada por módulo).
     */
    public function index()
    {
        $conteos = $this->conteosPorDepartamento();

        $regiones = [];

        foreach (Colombia::regiones() as $nombre => $departamentos) {
            $info = Colombia::infoRegion($nombre);

            $regiones[] = [
                'nombre' => $nombre,
                'slug' => Colombia::slug($nombre),
                'icono' => $info['icono'] ?? '📍',
                'descripcion' => $info['descripcion'] ?? null,
                'departamentos' => array_map(fn (string $dep): array => [
                    'nombre' => $dep,
                    'slug' => Colombia::slug($dep),
                    'servicios' => $conteos[$dep] ?? 0,
                ], $departamentos),
            ];
        }

        return view('destinos.index', compact('regiones'));
    }

    /**
     * Detalle de un departamento (recibe el slug de su nombre).
     * 404 si el slug no corresponde a ningún departamento del catálogo.
     */
    public function show(string $departamento)
    {
        $nombre = Colombia::departamentoPorSlug($departamento);

        abort_unless($nombre !== null, 404);

        $region = Colombia::regionDe($nombre);
        $infoRegion = $region !== null ? Colombia::infoRegion($region) : [];
        $municipios = Colombia::municipios($nombre);

        $servicios = [];
        $favoritos = [];

        foreach ($this->modelos() as $tipo => $modelo) {
            $consulta = $modelo::query()
                ->withAvg('resenas', 'calificacion')
                ->withCount('resenas');

            if ($tipo === 'agencia') {
                $consulta->withCount('tours');
            }

            if ($tipo === 'tour') {
                $consulta->with('paquetes');
            }

            // Misma fórmula que los índices de módulo. guias no tiene
            // columna municipio (usa zonas): se filtra solo por departamento.
            $servicios[$tipo] = $consulta
                ->ubicacion($nombre, null)
                ->orderBy('nombre')
                ->get();

            $favoritos[$tipo] = $this->obtenerFavoritosIds(auth()->id(), $tipo);
        }

        $totalServicios = 0;
        foreach ($servicios as $coleccion) {
            $totalServicios += $coleccion->count();
        }

        return view('destinos.show', [
            'departamento' => $nombre,
            'region' => $region,
            'regionSlug' => $region !== null ? Colombia::slug($region) : null,
            'infoRegion' => $infoRegion,
            'municipios' => $municipios,
            'servicios' => $servicios,
            'favoritos' => $favoritos,
            'totalServicios' => $totalServicios,
        ]);
    }

    /**
     * Módulos turísticos por tipo de favorito/reseña (morphMap).
     *
     * @return array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    private function modelos(): array
    {
        return [
            'lugar' => Lugar::class,
            'hotel' => Hotel::class,
            'restaurante' => Restaurante::class,
            'tour' => Tour::class,
            'guia' => Guia::class,
            'agencia' => Agencia::class,
            'transporte' => Transporte::class,
        ];
    }

    /**
     * Total de servicios reales por departamento (solo departamentos del
     * catálogo). Usa `withCount` sobre las 7 relaciones `hasMany` de
     * `Departamento` para contar servicios vinculados por `departamento_id`.
     *
     * @return array<string, int>
     */
    private function conteosPorDepartamento(): array
    {
        $conteos = array_fill_keys(Colombia::departamentos(), 0);

        $departamentos = Departamento::withCount([
            'lugares',
            'hoteles',
            'restaurantes',
            'tours',
            'agencias',
            'guias',
            'transportes',
        ])->get();

        foreach ($departamentos as $depto) {
            $nombre = $depto->nombre;
            if (! array_key_exists($nombre, $conteos)) {
                continue;
            }

            $conteos[$nombre] = (int) $depto->lugares_count
                + (int) $depto->hoteles_count
                + (int) $depto->restaurantes_count
                + (int) $depto->tours_count
                + (int) $depto->agencias_count
                + (int) $depto->guias_count
                + (int) $depto->transportes_count;
        }

        return $conteos;
    }
}

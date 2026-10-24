<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Hotel;
use App\Models\Lugar;
use Database\Seeders\DepartamentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DepartamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_es_idempotente_deja_exactamente_33_filas(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);
        $this->assertCount(33, DB::table('departamentos')->get());

        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);
        $this->assertCount(33, DB::table('departamentos')->get());
    }

    public function test_codigos_y_slugs_son_unicos(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $codigos = DB::table('departamentos')->pluck('cod_dpto');
        $slugs = DB::table('departamentos')->pluck('slug');

        $this->assertCount(33, $codigos->unique());
        $this->assertCount(33, $slugs->unique());
    }

    public function test_relaciones_has_many_devuelven_registros_vinculados(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $huila = Departamento::where('nombre', 'Huila')->first();
        $this->assertNotNull($huila);
        $this->assertSame('41', $huila->cod_dpto);

        Lugar::factory()->create([
            'nombre' => 'Desierto de la Tatacoa',
            'departamento' => 'Huila',
            'municipio' => 'Villavieja',
        ]);
        Lugar::factory()->create([
            'nombre' => 'San Agustín',
            'departamento' => 'Huila',
            'municipio' => 'San Agustín',
        ]);

        $this->assertCount(2, $huila->fresh()->lugares);
        $this->assertSame('Desierto de la Tatacoa', $huila->lugares()->first()->nombre);
        $this->assertSame('41', $huila->lugares()->first()->departamento_id);
    }

    public function test_backfill_asigna_huila_solo_where_departamento_es_huila_y_id_null(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $codHuila = DB::table('departamentos')
            ->where('nombre', 'Huila')
            ->value('cod_dpto');

        Lugar::factory()->create([
            'nombre' => 'Lugar Huila Sin Id',
            'departamento' => 'Huila',
            'municipio' => 'Neiva',
        ]);

        Lugar::factory()->create([
            'nombre' => 'Lugar Antioquia Sin Id',
            'departamento' => 'Antioquia',
            'municipio' => 'Medellín',
        ]);

        $this->assertSame($codHuila, DB::table('lugares')
            ->where('nombre', 'Lugar Huila Sin Id')
            ->value('departamento_id'));

        $this->assertSame('05', DB::table('lugares')
            ->where('nombre', 'Lugar Antioquia Sin Id')
            ->value('departamento_id'));
    }

    public function test_backfill_tolerar_que_no_exista_fila_de_huila(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        // Usar un departamento que no existe en el catálogo (config) para probar
        // que el hook deja null y reporta sin lanzar excepción.
        Lugar::factory()->create([
            'nombre' => 'Lugar Sin Departamento',
            'departamento' => 'Atlantis',
            'municipio' => 'Neiva',
        ]);

        $this->assertNull(DB::table('lugares')
            ->where('nombre', 'Lugar Sin Departamento')
            ->value('departamento_id'));
    }

    public function test_catalogo_33_departamentos_resuelven_a_codigo_valido(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        // Usar los mismos nombres que usa el hook (config colombia.municipios).
        $nombresConfig = collect(array_keys(config('colombia.municipios')))
            ->map(fn ($k) => explode('|', $k, 2)[0])
            ->unique()
            ->values();

        $this->assertCount(33, $nombresConfig);

        foreach ($nombresConfig as $nombre) {
            $hotel = Hotel::create([
                'nombre' => 'Hotel test ' . $nombre,
                'descripcion' => 'Descripción suficientemente larga.',
                'departamento' => $nombre,
                'municipio' => 'Capital',
                'precio_noche' => 100000,
                'estrellas' => 3,
            ]);

            $this->assertNotNull(
                $hotel->departamento_id,
                "departamento_id es null para '$nombre'"
            );

            $this->assertTrue(
                DB::table('departamentos')->where('cod_dpto', $hotel->departamento_id)->exists(),
                "código '{$hotel->departamento_id}' no existe en departamentos para '$nombre'"
            );
        }
    }

    public function test_guardar_otro_campo_repara_departamento_id_nulo(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $lugar = Lugar::factory()->create([
            'nombre' => 'Lugar para reparar',
            'departamento' => 'Huila',
            'municipio' => 'Neiva',
        ]);

        // Simular registro viejo sin departamento_id (bypass hook).
        DB::table('lugares')->where('id', $lugar->id)->update(['departamento_id' => null]);
        $lugar->refresh();
        $this->assertNull($lugar->departamento_id);

        // Guardar otro campo (no departamento): el hook debe reparar departamento_id.
        $lugar->nombre = 'Lugar renombrado';
        $lugar->save();

        $this->assertSame('41', $lugar->fresh()->departamento_id);
    }

    public function test_hotel_asigna_departamento_id_desde_nombre(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $hotel = Hotel::create([
            'nombre' => 'Hotel prueba Antioquia',
            'descripcion' => 'Descripción suficientemente larga para el hotel.',
            'departamento' => 'Antioquia',
            'municipio' => 'Medellín',
            'precio_noche' => 100000,
            'estrellas' => 4,
        ]);

        $this->assertSame('05', $hotel->departamento_id);
    }

    public function test_actualizar_departamento_actualiza_departamento_id(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $lugar = Lugar::factory()->create([
            'nombre' => 'Lugar para actualizar',
            'departamento' => 'Huila',
            'municipio' => 'Neiva',
        ]);

        $this->assertSame('41', $lugar->fresh()->departamento_id);

        $lugar->update(['departamento' => 'Antioquia']);

        $this->assertSame('05', $lugar->fresh()->departamento_id);
    }

    public function test_codigos_seeder_iguales_a_codigos_catalogo(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        // Códigos del seeder (tabla departamentos).
        $codigosSeeder = DB::table('departamentos')->pluck('cod_dpto')->sort()->values()->toArray();

        // Códigos del catálogo (config colombia.municipios, mismo origen que el hook).
        $codigosCatalogo = collect(array_values(config('colombia.municipios')))
            ->pluck('cod_dpto')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $this->assertCount(33, $codigosSeeder);
        $this->assertCount(33, $codigosCatalogo);

        $soloEnSeeder = array_values(array_diff($codigosSeeder, $codigosCatalogo));
        $soloEnCatalogo = array_values(array_diff($codigosCatalogo, $codigosSeeder));

        if (! empty($soloEnSeeder) || ! empty($soloEnCatalogo)) {
            $mensaje = "Diferencias entre códigos del seeder y del catálogo:\n";
            if (! empty($soloEnSeeder)) {
                $mensaje .= "Solo en seeder: " . implode(', ', $soloEnSeeder) . "\n";
            }
            if (! empty($soloEnCatalogo)) {
                $mensaje .= "Solo en catálogo: " . implode(', ', $soloEnCatalogo) . "\n";
            }
            $this->fail($mensaje);
        }

        $this->assertSame($codigosCatalogo, $codigosSeeder);
    }

    public function test_departamento_id_por_mass_assignment_se_ignora(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $hotel = Hotel::create([
            'nombre' => 'Hotel mass-assignment',
            'descripcion' => 'Descripción suficientemente larga.',
            'departamento' => 'Antioquia',
            'municipio' => 'Medellín',
            'precio_noche' => 100000,
            'estrellas' => 3,
            'departamento_id' => '99',
        ]);

        $this->assertSame('05', $hotel->fresh()->departamento_id);
    }
}
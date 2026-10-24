<?php

namespace Tests\Feature;

use App\Models\Departamento;
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

        // Segunda corrida: no debe duplicar.
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
            'departamento_id' => $huila->cod_dpto,
            'municipio' => 'Villavieja',
        ]);
        Lugar::factory()->create([
            'nombre' => 'San Agustín',
            'departamento' => 'Huila',
            'departamento_id' => $huila->cod_dpto,
            'municipio' => 'San Agustín',
        ]);

        $this->assertCount(2, $huila->fresh()->lugares);
        $this->assertSame('Desierto de la Tatacoa', $huila->lugares()->first()->nombre);
    }

    public function test_backfill_asigna_huila_solo_where_departamento_es_huila_y_id_null(): void
    {
        $this->artisan('db:seed', ['--class' => DepartamentoSeeder::class]);

        $codHuila = DB::table('departamentos')
            ->where('nombre', 'Huila')
            ->value('cod_dpto');

        // Lugar con departamento = 'Huila' y departamento_id NULL -> debe recibir '41'
        Lugar::factory()->create([
            'nombre' => 'Lugar Huila Sin Id',
            'departamento' => 'Huila',
            'departamento_id' => null,
            'municipio' => 'Neiva',
        ]);

        // Lugar con departamento = 'Antioquia' y departamento_id NULL -> no debe recibir '41'
        Lugar::factory()->create([
            'nombre' => 'Lugar Antioquia Sin Id',
            'departamento' => 'Antioquia',
            'departamento_id' => null,
            'municipio' => 'Medellín',
        ]);

        // Lugar con departamento = 'Huila' pero departamento_id YA asignado -> no debe tocarse
        Lugar::factory()->create([
            'nombre' => 'Lugar Huila Con Id',
            'departamento' => 'Huila',
            'departamento_id' => '05', // Antioquia (intencionalmente distinto)
            'municipio' => 'Pitalito',
        ]);

        // Ejecutar la misma lógica de backfill de la migración
        foreach (['lugares'] as $tabla) {
            DB::table($tabla)
                ->whereNull('departamento_id')
                ->where('departamento', 'Huila')
                ->update(['departamento_id' => $codHuila]);
        }

        $this->assertSame($codHuila, DB::table('lugares')
            ->where('nombre', 'Lugar Huila Sin Id')
            ->value('departamento_id'));

        $this->assertNull(DB::table('lugares')
            ->where('nombre', 'Lugar Antioquia Sin Id')
            ->value('departamento_id'));

        $this->assertSame('05', DB::table('lugares')
            ->where('nombre', 'Lugar Huila Con Id')
            ->value('departamento_id'));
    }

    public function test_backfill_tolerar_que_no_exista_fila_de_huila(): void
    {
        // Si no existe la fila de Huila, $codDptoHuila es null y no se actualiza nada.
        $codDptoHuila = DB::table('departamentos')
            ->where('nombre', 'Huila')
            ->value('cod_dpto');

        $this->assertNull($codDptoHuila);

        Lugar::factory()->create([
            'nombre' => 'Lugar Sin Huila',
            'departamento' => 'Huila',
            'departamento_id' => null,
            'municipio' => 'Neiva',
        ]);

        // Sin fila de Huila, el backfill no asigna nada (evita código huérfano).
        if ($codDptoHuila) {
            DB::table('lugares')
                ->whereNull('departamento_id')
                ->where('departamento', 'Huila')
                ->update(['departamento_id' => $codDptoHuila]);
        }

        $this->assertNull(DB::table('lugares')
            ->where('nombre', 'Lugar Sin Huila')
            ->value('departamento_id'));
    }
}

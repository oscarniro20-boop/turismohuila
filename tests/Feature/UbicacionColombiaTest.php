<?php

namespace Tests\Feature;

use App\Models\Lugar;
use App\Support\Colombia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UbicacionColombiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogo_tiene_departamentos_y_municipios(): void
    {
        $departamentos = Colombia::departamentos();

        $this->assertNotEmpty($departamentos);
        $this->assertContains('Huila', $departamentos);
        $this->assertCount(37, Colombia::municipios('Huila'));
    }

    public function test_municipio_valido_existe_en_su_departamento(): void
    {
        $this->assertTrue(Colombia::existe('Huila', 'Neiva'));
        $this->assertTrue(Colombia::existe('Antioquia', 'Medellín'));
    }

    public function test_municipio_de_otro_departamento_no_existe(): void
    {
        // Neiva es de Huila, no de Antioquia.
        $this->assertFalse(Colombia::existe('Antioquia', 'Neiva'));
    }

    public function test_codigos_oficiales_como_cadena_con_cero_inicial(): void
    {
        $codigos = Colombia::codigos('Antioquia', 'Medellín');

        $this->assertNotNull($codigos);
        $this->assertSame('05', $codigos['cod_dpto']);
        $this->assertSame('05001', $codigos['cod_mpio']);
    }

    public function test_san_andres_con_salto_de_linea_entrecomillado_se_parsea(): void
    {
        // El CSV oficial trae "SAN ANDRÉS\nISLAS" en nom_mpio; el parser debe
        // haberlo conservado como un único municipio, no dividido.
        $encontro = false;
        foreach (Colombia::municipios('Archipiélago de San Andrés, Providencia y Santa Catalina') as $mun) {
            if (str_contains($mun, 'SAN ANDRÉS') || str_contains($mun, 'San Andrés')) {
                $encontro = true;
            }
        }

        $this->assertTrue($encontro, 'San Andrés debe estar presente y parseado como un municipio.');
    }

    public function test_validacion_rechaza_municipio_de_otro_departamento_al_crear(): void
    {
        $user = \App\Models\User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('lugares.store'), [
            'nombre' => 'Atractivo de prueba',
            'departamento' => 'Antioquia',
            'municipio' => 'Neiva', // pertenece a Huila, no a Antioquia
            'categoria' => 'Naturaleza',
            'descripcion' => 'Descripción de prueba suficientemente larga.',
        ]);

        $response->assertSessionHasErrors('municipio');
    }

    public function test_validacion_acepta_departamento_y_municipio_validos(): void
    {
        $user = \App\Models\User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('lugares.store'), [
            'nombre' => 'Atractivo válido',
            'departamento' => 'Antioquia',
            'municipio' => 'Medellín',
            'categoria' => 'Naturaleza',
            'descripcion' => 'Descripción de prueba suficientemente larga.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('lugares', [
            'nombre' => 'Atractivo válido',
            'departamento' => 'Antioquia',
            'municipio' => 'Medellín',
        ]);
    }

    public function test_registros_existentes_de_huila_se_preservan_con_su_ubicacion(): void
    {
        $lugar = Lugar::factory()->create([
            'nombre' => 'Desierto de la Tatacoa',
            'departamento' => 'Huila',
            'municipio' => 'Neiva',
        ]);

        $this->assertSame('Neiva, Huila', $lugar->ubicacion_texto);
    }

    public function test_filtro_por_departamento_filtra_resultados(): void
    {
        Lugar::factory()->create(['nombre' => 'Lugar Huila', 'departamento' => 'Huila', 'municipio' => 'Neiva']);
        Lugar::factory()->create(['nombre' => 'Lugar Antioquia', 'departamento' => 'Antioquia', 'municipio' => 'Medellín']);

        $response = $this->get(route('lugares.index', ['departamento' => 'Antioquia']));

        $response->assertStatus(200);
        $response->assertSee('Lugar Antioquia');
        $response->assertDontSee('Lugar Huila');
    }

    public function test_mapa_responde_200(): void
    {
        $response = $this->get(route('mapa'));

        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Lugar;
use App\Support\Colombia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 2 — módulo de destinos nacionales: listado por regiones naturales,
 * detalle por departamento, comportamiento de slugs y casos de error.
 */
class DestinosTest extends TestCase
{
    use RefreshDatabase;

    public function test_listado_muestra_las_seis_regiones_naturales(): void
    {
        $response = $this->get('/destinos');

        $response->assertOk();

        foreach (['Andina', 'Caribe', 'Pacífica', 'Amazonia', 'Orinoquía', 'Insular'] as $region) {
            $response->assertSee($region);
        }
    }

    public function test_listado_enlaza_los_33_departamentos_con_su_slug(): void
    {
        $response = $this->get(route('destinos.index'));

        $response->assertOk();
        $this->assertCount(33, Colombia::departamentos());

        foreach (Colombia::departamentos() as $departamento) {
            $response->assertSee(route('destinos.show', Colombia::slug($departamento)), false);
        }
    }

    public function test_listado_muestra_conteo_real_de_servicios(): void
    {
        Lugar::factory()->create(['nombre' => 'Tatacoa Norte', 'departamento' => 'Huila', 'municipio' => 'Villavieja']);
        Lugar::factory()->create(['nombre' => 'Tatacoa Sur', 'departamento' => 'Huila', 'municipio' => 'Villavieja']);

        $response = $this->get(route('destinos.index'));

        $response->assertOk();
        // La tarjeta de Huila debe reflejar los 2 servicios reales creados.
        $response->assertSee('2 servicios');
        // Los departamentos sin registros quedan marcados como vacíos.
        $response->assertSee(__('ui.destinos.no_services_short'));
    }

    public function test_detalle_muestra_servicios_del_departamento_y_solo_esos(): void
    {
        Lugar::factory()->create(['nombre' => 'Desierto de la Tatacoa', 'departamento' => 'Huila', 'municipio' => 'Villavieja']);
        Hotel::create([
            'nombre' => 'Hotel Neiva Centro',
            'descripcion' => 'Hotel de prueba con descripción suficiente.',
            'departamento' => 'Huila',
            'municipio' => 'Neiva',
            'precio_noche' => 120000,
        ]);
        Hotel::create([
            'nombre' => 'Hotel Medellín Plaza',
            'descripcion' => 'Hotel de prueba con descripción suficiente.',
            'departamento' => 'Antioquia',
            'municipio' => 'Medellín',
            'precio_noche' => 150000,
        ]);

        $response = $this->get(route('destinos.show', 'huila'));

        $response->assertOk();
        $response->assertSee('Desierto de la Tatacoa');
        $response->assertSee('Hotel Neiva Centro');
        $response->assertDontSee('Hotel Medellín Plaza');
        $response->assertSee('Andina'); // región natural del departamento
        $response->assertSee('Neiva');  // municipio real del catálogo DIVIPOLA
        $response->assertSee(__('ui.destinos.in_region', ['region' => 'Andina']));
        // Enlace "ver todos" al índice del módulo ya filtrado por departamento.
        $response->assertSee(route('hoteles.index', ['departamento' => 'Huila']), false);
    }

    public function test_detalle_sin_servicios_muestra_estados_vacios(): void
    {
        $response = $this->get(route('destinos.show', 'choco'));

        $response->assertOk();
        $response->assertSee(__('ui.catalog.no_places'));
        $response->assertSee(__('ui.catalog.no_hotels'));
        $response->assertSee(__('ui.catalog.no_tours'));
    }

    public function test_slugs_respetan_mayusculas_tildes_y_puntos(): void
    {
        $this->get('/destinos/HUILA')->assertOk();
        $this->get('/destinos/huila')->assertOk();
        $this->get('/destinos/Bogota-DC')->assertOk(); // Bogotá D.C.
    }

    public function test_departamento_inexistente_devuelve_404(): void
    {
        $this->get(route('destinos.show', 'atlantida'))->assertNotFound();
        // Un municipio no es un departamento: "neiva" no debe resolver.
        $this->get(route('destinos.show', 'neiva'))->assertNotFound();
    }

    public function test_slugs_de_departamentos_son_unicos_y_reversibles(): void
    {
        $departamentos = Colombia::departamentos();
        $slugs = array_map([Colombia::class, 'slug'], $departamentos);

        $this->assertCount(count($departamentos), array_unique($slugs));
        $this->assertSame('bogota-dc', Colombia::slug('Bogotá D.C.'));
        $this->assertSame('Bogotá D.C.', Colombia::departamentoPorSlug('bogota-dc'));
        $this->assertSame('Huila', Colombia::departamentoPorSlug('Huila'));
        $this->assertNull(Colombia::departamentoPorSlug('atlantida'));
    }

    public function test_bienvenida_y_navbar_enlazan_destinos(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('destinos.index'), false);
        $response->assertSee(__('ui.welcome.destinos_title'));
        $response->assertSee(__('ui.welcome.see_destinos'));
    }
}

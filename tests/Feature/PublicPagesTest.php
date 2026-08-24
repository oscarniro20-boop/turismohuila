<?php

namespace Tests\Feature;

use App\Models\Lugar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Huila');
    }

    public function test_lugares_index_page_loads(): void
    {
        Lugar::factory()->create(['nombre' => 'Desierto de la Tatacoa']);

        $response = $this->get(route('lugares.index'));

        $response->assertStatus(200);
        $response->assertSee('Desierto de la Tatacoa');
    }

    public function test_lugar_show_page_loads(): void
    {
        $lugar = Lugar::factory()->create();

        $response = $this->get(route('lugares.show', $lugar));

        $response->assertStatus(200);
    }

    public function test_nosotros_page_loads(): void
    {
        $response = $this->get(route('nosotros'));

        $response->assertStatus(200);
    }

    public function test_contacto_page_loads(): void
    {
        $response = $this->get(route('contacto'));

        $response->assertStatus(200);
    }

    public function test_mapa_page_loads(): void
    {
        $response = $this->get(route('mapa'));

        $response->assertStatus(200);
    }

    /**
     * "/" debe usar la vista 'bienvenida' (fusionada) y, como invitado,
     * mostrar su contenido distintivo y el banner de captación "Crear cuenta".
     */
    public function test_home_page_shows_bienvenida_content_for_guest(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Tres maneras de vivir el Huila');
        $response->assertSee('Galería de experiencias');
        $response->assertSee('¿Listo para descubrir el Huila?');
        $response->assertSee('Crear cuenta');
    }

    /**
     * Autenticado: el banner @guest de invitados NO debe renderizarse.
     */
    public function test_home_page_hides_guest_banner_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Crear cuenta');
        $response->assertDontSee('¿Listo para descubrir el Huila?');
    }
}
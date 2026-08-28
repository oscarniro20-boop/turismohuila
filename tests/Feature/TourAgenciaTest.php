<?php

namespace Tests\Feature;

use App\Models\Agencia;
use App\Models\Tour;
use App\Models\TourPaquete;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourAgenciaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['rol' => 'admin']);
    }

    private function tour(array $overrides = []): Tour
    {
        return Tour::create(array_merge([
            'nombre' => 'Tour Rio Magdalena',
            'descripcion' => 'Recorrido por el río',
            'municipio' => 'Neiva',
            'precio' => 150000,
            'duracion_dias' => 2,
            'incluye' => 'Transporte',
            'cupo_maximo' => 10,
        ], $overrides));
    }

    private function agencia(array $overrides = []): Agencia
    {
        return Agencia::create(array_merge([
            'nombre' => 'Agencia Neiva Tours',
            'municipio' => 'Neiva',
        ], $overrides));
    }

    public function test_crear_tour_con_agencia_escribe_en_tour_paquetes(): void
    {
        $agencia = $this->agencia();

        $this->actingAs($this->admin())
            ->post(route('tours.store'), [
                'nombre' => 'Tour San Agustin',
                'descripcion' => 'Ruta arqueológica',
                'municipio' => 'San Agustín',
                'precio' => 200000,
                'duracion_dias' => 3,
                'incluye' => 'Guía',
                'cupo_maximo' => 8,
                'agencias' => [$agencia->id],
                'paquetes' => [$agencia->id => ['precio' => 200000, 'duracion_dias' => 3, 'cupo_maximo' => 8]],
            ])
            ->assertRedirect(route('tours.index'));

        $tour = Tour::where('nombre', 'Tour San Agustin')->firstOrFail();
        $this->assertDatabaseHas('tour_paquetes', ['tour_id' => $tour->id, 'agencia_id' => $agencia->id]);

        // Los flujos de lectura deben venir de tour_paquetes (única fuente)
        $this->assertSame([$tour->id], $agencia->tours->pluck('id')->all());
        $this->assertCount(1, $agencia->paquetes);
        $this->assertSame([$agencia->id], $tour->agencias->pluck('id')->all());
    }

    public function test_editar_tour_actualiza_relacion_agencia_en_tour_paquetes(): void
    {
        $tour = $this->tour();
        $agencia1 = $this->agencia(['nombre' => 'Agencia 1']);
        $agencia2 = $this->agencia(['nombre' => 'Agencia 2']);
        TourPaquete::create(['tour_id' => $tour->id, 'agencia_id' => $agencia1->id, 'precio' => 100]);

        $this->actingAs($this->admin())
            ->put(route('tours.update', $tour), [
                'nombre' => $tour->nombre,
                'descripcion' => $tour->descripcion,
                'municipio' => $tour->municipio,
                'precio' => $tour->precio,
                'duracion_dias' => $tour->duracion_dias,
                'incluye' => $tour->incluye,
                'cupo_maximo' => $tour->cupo_maximo,
                'agencias' => [$agencia2->id],
                'paquetes' => [$agencia2->id => ['precio' => 200, 'duracion_dias' => 3, 'cupo_maximo' => 6]],
            ])
            ->assertRedirect(route('tours.index'));

        // La agencia 1 fue eliminada de la relación; la 2 agregada
        $this->assertDatabaseMissing('tour_paquetes', ['tour_id' => $tour->id, 'agencia_id' => $agencia1->id]);
        $this->assertDatabaseHas('tour_paquetes', ['tour_id' => $tour->id, 'agencia_id' => $agencia2->id]);
        $this->assertSame([$agencia2->id], $tour->agencias->pluck('id')->all());
    }

    public function test_mostrar_tour_carga_paquetes_y_agencias_desde_tour_paquetes(): void
    {
        $tour = $this->tour();
        $agencia = $this->agencia();
        TourPaquete::create(['tour_id' => $tour->id, 'agencia_id' => $agencia->id, 'precio' => 120]);

        $response = $this->get(route('tours.show', $tour))->assertOk();

        $response->assertSee($tour->nombre);
        $this->assertDatabaseHas('tour_paquetes', ['tour_id' => $tour->id, 'agencia_id' => $agencia->id]);
    }

    public function test_listar_agencia_cuenta_tours_desde_tour_paquetes(): void
    {
        $agencia = $this->agencia();
        $tour = $this->tour();
        TourPaquete::create(['tour_id' => $tour->id, 'agencia_id' => $agencia->id, 'precio' => 90]);

        $this->assertSame(1, Agencia::withCount('tours')->find($agencia->id)->tours_count);
        $this->assertSame(1, $agencia->tours->count());
    }

    public function test_obtener_agencias_relacionadas_desde_tour_paquetes(): void
    {
        $tour = $this->tour();
        $agencia = $this->agencia();
        TourPaquete::create(['tour_id' => $tour->id, 'agencia_id' => $agencia->id, 'precio' => 80]);

        // El endpoint de reseñas que resuelve "servicios relacionados" usa las relaciones.
        $this->actingAs(User::factory()->create())
            ->get(route('resenas.nombre-servicio', ['tipo' => 'tour', 'id' => $tour->id]))
            ->assertOk()
            ->assertJson(['nombre' => $tour->nombre, 'relacionados' => [$agencia->nombre]]);
    }
}

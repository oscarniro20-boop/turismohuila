<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Cobertura CRUD de los módulos administrativos:
 * Lugares, Hoteles, Restaurantes, Tours, Guías, Agencias y Transportes.
 *
 * Comprueba acceso público (index/show), protección de escritura
 * (visitante -> login, turista -> 403) y ciclo completo del admin
 * (store/update/destroy) sin archivos de imagen (nullable).
 */
class CrudModulosTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['rol' => 'admin']);
    }

    private function turista(): User
    {
        return User::factory()->create(['rol' => 'turista']);
    }

    public static function modulos(): array
    {
        return [
            'lugares'      => ['lugares', 'lugar'],
            'hoteles'      => ['hoteles', 'hotel'],
            'restaurantes' => ['restaurantes', 'restaurante'],
            'tours'        => ['tours', 'tour'],
            'guias'        => ['guias', 'guia'],
            'agencias'     => ['agencias', 'agencia'],
            'transportes'  => ['transportes', 'transporte'],
        ];
    }

    #[DataProvider('modulos')]
    public function test_index_publico_es_accesible(string $plural, string $singular = ''): void
    {
        $this->get(route($plural . '.index'))->assertOk();
    }

    #[DataProvider('modulos')]
    public function test_visitante_no_puede_acceder_al_formulario_de_creacion(string $plural, string $singular = ''): void
    {
        $this->get(route($plural . '.create'))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('modulos')]
    public function test_turista_no_puede_crear(string $plural, string $singular = ''): void
    {
        $this->actingAs($this->turista())
            ->post(route($plural . '.store'), [])
            ->assertForbidden();
    }

    #[DataProvider('modulos')]
    public function test_admin_puede_crear(string $plural, string $singular): void
    {
        $datos = $this->datosValidos($plural);

        $this->actingAs($this->admin())
            ->post(route($plural . '.store'), $datos)
            ->assertRedirect();

        $this->assertDatabaseHas($this->tabla($singular), [
            'nombre' => $datos['nombre'],
        ]);
    }

    #[DataProvider('modulos')]
    public function test_admin_puede_editar_y_actualizar(string $plural, string $singular = ''): void
    {
        $singular = $this->singular($plural);
        $admin = $this->admin();
        $registro = $this->crear($singular, $admin);

        $datos = $this->datosValidos($plural);
        $datos['nombre'] = 'Actualizado ' . uniqid();

        $this->actingAs($admin)
            ->put(route($plural . '.update', $registro), $datos)
            ->assertRedirect();

        $this->assertDatabaseHas($this->tabla($singular), [
            'id'     => $registro->id,
            'nombre' => $datos['nombre'],
        ]);
    }

    #[DataProvider('modulos')]
    public function test_turista_no_puede_eliminar(string $plural, string $singular = ''): void
    {
        $registro = $this->crear($this->singular($plural), $this->admin());

        $this->actingAs($this->turista())
            ->delete(route($plural . '.destroy', $registro))
            ->assertForbidden();
    }

    #[DataProvider('modulos')]
    public function test_admin_puede_eliminar(string $plural, string $singular = ''): void
    {
        $singular = $this->singular($plural);
        $admin = $this->admin();
        $registro = $this->crear($singular, $admin);

        $this->actingAs($admin)
            ->delete(route($plural . '.destroy', $registro))
            ->assertRedirect();

        $this->assertDatabaseMissing($this->tabla($singular), ['id' => $registro->id]);
    }

    #[DataProvider('modulos')]
    public function test_validacion_rechaza_datos_incompletos_para_admin(string $plural, string $singular = ''): void
    {
        $this->actingAs($this->admin())
            ->post(route($plural . '.store'), [])
            ->assertSessionHasErrors();
    }

    // ------------------------------------------------------------------

    private function singular(string $plural): string
    {
        return [
            'lugares'      => 'lugar',
            'hoteles'      => 'hotel',
            'restaurantes' => 'restaurante',
            'tours'        => 'tour',
            'guias'        => 'guia',
            'agencias'     => 'agencia',
            'transportes'  => 'transporte',
        ][$plural];
    }

    private function tabla(string $singular): string
    {
        return [
            'lugar'       => 'lugares',
            'hotel'       => 'hoteles',
            'restaurante' => 'restaurantes',
            'tour'        => 'tours',
            'guia'        => 'guias',
            'agencia'     => 'agencias',
            'transporte'  => 'transportes',
        ][$singular];
    }

    /**
     * Crea un registro real. Lugares tiene factory; los demás módulos se
     * crean vía HTTP (store) para respetar el flujo real del controlador.
     */
    private function crear(string $singular, User $autor)
    {
        if ($singular === 'lugar') {
            return \App\Models\Lugar::factory()->create(['user_id' => $autor->id]);
        }

        $plural = $this->tabla($singular);
        $datos = $this->datosValidos($plural);

        $this->actingAs($autor)->post(route($plural . '.store'), $datos);

        $clase = 'App\\Models\\' . ucfirst($singular);

        return $clase::where('nombre', $datos['nombre'])->firstOrFail();
    }

    /**
     * Datos mínimos válidos según las validaciones reales de cada
     * controlador. Sin 'imagen' (nullable) para no depender de Cloudinary.
     */
    private function datosValidos(string $plural): array
    {
        $base = [
            'nombre'      => 'Prueba ' . uniqid(),
            'descripcion' => 'Descripción de prueba suficientemente larga.',
            'municipio'   => 'Neiva',
        ];

        return match ($plural) {
            'lugares' => $base + [
                'departamento' => 'Huila',
                'categoria'    => 'Naturaleza',
                'latitud'      => 2.9273,
                'longitud'     => -75.2819,
            ],
            'hoteles' => $base + [
                'precio_noche' => 150000,
                'estrellas'    => 4,
                'telefono'     => '2781234567',
                'direccion'    => 'Calle 1 #2-3',
            ],
            'restaurantes' => $base + [
                'tipo_cocina'     => 'Tradicional',
                'precio_promedio' => 45000,
                'telefono'        => '2781234568',
                'direccion'       => 'Calle 4 #5-6',
            ],
            'tours' => $base + [
                'precio'        => 200000,
                'duracion_dias' => 2,
                'incluye'       => 'Transporte, guía y almuerzo',
                'cupo_maximo'   => 15,
            ],
            'guias' => [
                'nombre' => 'Prueba ' . uniqid(),
            ],
            'agencias' => [
                'nombre' => 'Prueba ' . uniqid(),
            ],
            'transportes' => $base + [
                'tipo' => 'Bus',
            ],
        };
    }
}

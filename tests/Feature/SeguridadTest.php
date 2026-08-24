<?php

namespace Tests\Feature;

use App\Models\Favorito;
use App\Models\Lugar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_normal_no_puede_acceder_a_rutas_admin(): void
    {
        $usuario = User::factory()->create(['rol' => 'turista']);

        $this->actingAs($usuario)
            ->get(route('lugares.create'))
            ->assertForbidden();

        $this->actingAs($usuario)
            ->post(route('lugares.store'), ['nombre' => 'x'])
            ->assertForbidden();
    }

    public function test_visitante_no_puede_crear_resenas_ni_ver_admin_resenas(): void
    {
        $this->get(route('resenas.create', ['tipo' => 'lugar', 'referencia_id' => 1]))
            ->assertRedirect(route('login'));

        $this->get(route('resenas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_favorito_rechaza_recurso_inexistente_y_tipo_invalido(): void
    {
        $usuario = User::factory()->create();

        // ID que no existe en ninguna tabla referenciable
        $this->actingAs($usuario)
            ->postJson(route('favoritos.toggle'), ['tipo' => 'lugar', 'referencia_id' => 999999])
            ->assertNotFound();

        $this->assertSame(0, Favorito::count());

        // Tipo fuera de la lista permitida
        $this->actingAs($usuario)
            ->postJson(route('favoritos.toggle'), ['tipo' => 'users', 'referencia_id' => 1])
            ->assertStatus(422); // validacion rechaza tipo no permitido

        $this->assertSame(0, Favorito::count());
    }

    public function test_favorito_usa_siempre_el_usuario_autenticado(): void
    {
        $propietario = User::factory()->create();
        $otro = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $this->actingAs($propietario)
            ->postJson(route('favoritos.toggle'), [
                'tipo' => 'lugar',
                'referencia_id' => $lugar->id,
                'user_id' => $otro->id, // intento de suplantación
            ])
            ->assertOk()
            ->assertJson(['activo' => true]);

        $fav = Favorito::sole();
        $this->assertSame($propietario->id, $fav->user_id);
        $this->assertNotSame($otro->id, $fav->user_id);
    }

    public function test_toggle_de_favorito_es_un_alternador_sin_duplicados(): void
    {
        $usuario = User::factory()->create();
        $lugar = Lugar::factory()->create();

        foreach ([true, false, true] as $esperado) {
            $this->actingAs($usuario)
                ->postJson(route('favoritos.toggle'), ['tipo' => 'lugar', 'referencia_id' => $lugar->id])
                ->assertOk()
                ->assertJson(['activo' => $esperado]);
        }

        $this->assertSame(1, Favorito::where('user_id', $usuario->id)->count());
    }

    public function test_usuario_no_puede_escalar_rol_en_su_perfil(): void
    {
        $usuario = User::factory()->create(['rol' => 'turista']);

        $this->actingAs($usuario)
            ->patch(route('profile.update'), [
                'name' => 'Nombre Nuevo',
                'email' => $usuario->email,
                'rol' => 'admin', // intento de escalamiento
            ]);

        $this->assertSame('turista', $usuario->fresh()->rol);
    }

    public function test_resena_no_puede_cambiar_user_id_tipo_ni_referencia_al_editar(): void
    {
        $autor = User::factory()->create();
        $otro = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $resena = \App\Models\Resena::create([
            'user_id' => $autor->id,
            'tipo' => 'lugar',
            'referencia_id' => $lugar->id,
            'calificacion' => 4,
            'comentario' => 'Original',
        ]);

        $this->actingAs($autor)
            ->put(route('resenas.update', $resena), [
                'calificacion' => 5,
                'comentario' => 'Editado',
                'user_id' => $otro->id,
                'tipo' => 'hotel',
                'referencia_id' => 12345,
            ]);

        $resena->refresh();
        $this->assertSame($autor->id, $resena->user_id);
        $this->assertSame('lugar', $resena->tipo);
        $this->assertSame($lugar->id, $resena->referencia_id);
        $this->assertSame('Editado', $resena->comentario);
    }

    public function test_usuario_no_puede_borrar_resena_ajena(): void
    {
        $autor = User::factory()->create();
        $intruso = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $resena = \App\Models\Resena::create([
            'user_id' => $autor->id,
            'tipo' => 'lugar',
            'referencia_id' => $lugar->id,
            'calificacion' => 3,
            'comentario' => 'De otro',
        ]);

        $this->actingAs($intruso)
            ->delete(route('resenas.destroy', $resena))
            ->assertForbidden();

        $this->assertDatabaseHas('resenas', ['id' => $resena->id]);
    }

    public function test_respuestas_incluyen_cabeceras_de_seguridad(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_logout_requiere_csrf(): void
    {
        // Laravel desactiva CSRF automaticamente en tests (DisableCsrfValidation),
        // asi que no se puede esperar un 419 aqui. En su lugar verificamos que:
        // 1) el default del framework para el grupo 'web' incluye ValidateCsrfToken, y
        // 2) bootstrap/app.php no excluye ese middleware del grupo web.
        $gruposDefault = (new \Illuminate\Foundation\Configuration\Middleware)->getMiddlewareGroups();

        $csrfEnDefaults = collect($gruposDefault['web'] ?? [])->contains(
            fn ($m) => str_contains((string) $m, 'ValidateCsrfToken')
                || str_contains((string) $m, 'PreventRequestForgery'),
        );

        $bootstrap = (string) file_get_contents(base_path('bootstrap/app.php'));
        $excluidoEnBootstrap = str_contains($bootstrap, 'validateCsrfTokens')
            || (str_contains($bootstrap, 'csrf') && str_contains($bootstrap, 'remove'));

        $this->assertTrue($csrfEnDefaults, 'El framework debe incluir CSRF en el grupo web.');
        $this->assertFalse($excluidoEnBootstrap, 'No se debe excluir el middleware CSRF.');
    }
}

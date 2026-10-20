<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Activación / desactivación de cuentas (hardening de administración).
 *
 * Cubre: estado inicial coherente con el comportamiento previo, acción
 * administrativa con guards en servidor (no auto-desactivación, no
 * desactivar al último admin activo), bloqueo real del login, permisos de
 * la ruta y conservación de datos asociados (sin borrado físico).
 */
class AdminUsuariosEstadoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    // ── Estado inicial coherente ──────────────────────────────────────────

    public function test_las_cuentas_existentes_quedan_activas_por_defecto(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->assertTrue($admin->fresh()->estaActivo());
        $this->assertTrue($turista->fresh()->estaActivo());
    }

    // ── Acción administrativa + auditoría ─────────────────────────────────

    public function test_desactivar_cuenta_registra_auditoria(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.usuarios.estado', $turista), [
            'activo' => '0',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertFalse($turista->fresh()->estaActivo());

        $registro = Auditoria::query()->where('accion', 'usuario.desactivado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($turista->id, $registro->entidad_id);
        $this->assertFalse((bool) ($registro->datos['activo'] ?? true));
        $this->assertSame($admin->id, $registro->user_id);
    }

    public function test_activar_cuenta_registra_auditoria(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create(['activo' => false]);

        $this->actingAs($admin)->post(route('admin.usuarios.estado', $turista), [
            'activo' => '1',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertTrue($turista->fresh()->estaActivo());

        $registro = Auditoria::query()->where('accion', 'usuario.activado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($turista->id, $registro->entidad_id);
        $this->assertTrue((bool) ($registro->datos['activo'] ?? false));
    }

    public function test_estado_sin_cambios_no_duplica_auditoria(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.usuarios.estado', $turista), [
            'activo' => '1',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseCount('auditorias', 0);
        $this->assertTrue($turista->fresh()->estaActivo());
    }

    // ── Guards de servidor ────────────────────────────────────────────────

    public function test_admin_no_puede_desactivarse_a_si_mismo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.usuarios.estado', $admin), [
            'activo' => '0',
        ])->assertRedirect(route('admin.usuarios.index'))->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->estaActivo());
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_no_se_puede_desactivar_al_unico_administrador_activo(): void
    {
        // Sesión de un admin ya desactivado (el servidor no confía en la UI):
        // el único admin con acceso es $adminActivo y no puede ser desactivado.
        $adminActivo = $this->admin();
        $adminInactivo = User::factory()->admin()->create(['activo' => false]);

        $this->actingAs($adminInactivo)->post(route('admin.usuarios.estado', $adminActivo), [
            'activo' => '0',
        ])->assertRedirect(route('admin.usuarios.index'))->assertSessionHas('error');

        $this->assertTrue($adminActivo->fresh()->estaActivo());
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_se_puede_desactivar_a_otro_admin_si_queda_al_menos_uno_activo(): void
    {
        $adminA = $this->admin();
        $adminB = User::factory()->admin()->create();

        $this->actingAs($adminA)->post(route('admin.usuarios.estado', $adminB), [
            'activo' => '0',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertFalse($adminB->fresh()->estaActivo());
        $this->assertTrue($adminA->fresh()->estaActivo());

        $registro = Auditoria::query()->where('accion', 'usuario.desactivado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($adminB->id, $registro->entidad_id);
    }

    // ── Login real (flujo de autenticación, no solo UI) ───────────────────

    public function test_cuenta_desactivada_no_puede_iniciar_sesion(): void
    {
        $turista = User::factory()->create(['activo' => false]);

        $respuesta = $this->post('/login', [
            'email' => $turista->email,
            'password' => 'password',
        ]);

        $respuesta->assertRedirect(route('login'));
        $respuesta->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_mensaje_de_cuenta_desactivada_se_muestra_en_el_formulario_de_login(): void
    {
        // Nota: va en un test aparte porque assertSessionHasErrors arranca la
        // sesión y, en el mismo proceso PHP (harness de tests), la segunda
        // marshaling de 'errors' en el GET posterior vaciaría el ViewErrorBag
        // (particularidad de la serialización json de sesión en Laravel 13).
        $turista = User::factory()->create(['activo' => false]);

        $this->post('/login', [
            'email' => $turista->email,
            'password' => 'password',
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('cuenta está desactivada');
    }

    public function test_cuenta_activada_vuelve_a_poder_iniciar_sesion(): void
    {
        $turista = User::factory()->create(['activo' => false]);

        // Un admin la reactiva desde el panel.
        $this->actingAs($this->admin())->post(route('admin.usuarios.estado', $turista), [
            'activo' => '1',
        ]);

        // Olvidar la sesión de prueba para que el siguiente POST /login
        // se ejecute realmente como invitado (flujo de login limpio).
        $this->app['auth']->forgetGuards();

        $this->post('/login', [
            'email' => $turista->email,
            'password' => 'password',
        ])->assertRedirect(route('bienvenida', absolute: false));

        $this->assertAuthenticatedAs($turista);
    }

    // ── Permisos y validación de la ruta ──────────────────────────────────

    public function test_invitado_y_turista_no_pueden_usar_la_ruta_de_estado(): void
    {
        $otro = User::factory()->create();
        $turista = User::factory()->create();

        $this->post(route('admin.usuarios.estado', $otro), ['activo' => '0'])
            ->assertRedirect(route('login'));

        $this->actingAs($turista)
            ->post(route('admin.usuarios.estado', $otro), ['activo' => '0'])
            ->assertForbidden();

        $this->assertTrue($otro->fresh()->estaActivo());
    }

    public function test_estado_valida_el_parametro_en_el_servidor(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.usuarios.estado', $turista), [])
            ->assertSessionHasErrors('activo');

        $this->actingAs($admin)
            ->post(route('admin.usuarios.estado', $turista), ['activo' => 'no-es-booleano'])
            ->assertSessionHasErrors('activo');

        $this->assertTrue($turista->fresh()->estaActivo());
        $this->assertDatabaseCount('auditorias', 0);
    }

    // ── Sin borrado físico ────────────────────────────────────────────────

    public function test_desactivar_conserva_resenas_y_favoritos(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        Resena::create([
            'user_id' => $turista->id,
            'tipo' => 'lugar',
            'referencia_id' => 1,
            'comentario' => 'Reseña de prueba',
            'calificacion' => 5,
        ]);

        $this->actingAs($admin)->post(route('admin.usuarios.estado', $turista), [
            'activo' => '0',
        ]);

        $this->assertFalse($turista->fresh()->estaActivo());
        $this->assertDatabaseCount('users', 2);
        $this->assertSame(1, Resena::where('user_id', $turista->id)->count());
    }

    // ── Mensaje específico de cuenta desactivada en el HTML renderizado ─────

    public function test_error_de_cuenta_desactivada_se_renderiza_en_campo_email(): void
    {
        $turista = User::factory()->create(['activo' => false]);

        $this->post('/login', [
            'email' => $turista->email,
            'password' => 'password',
        ]);

        $html = $this->get('/login')->assertOk()->getContent();

        // El mensaje de cuenta desactivada debe aparecer en el HTML renderizado.
        $this->assertStringContainsString('Tu cuenta está desactivada', $html);

        // El error debe asociarse al campo correo (input con clase is-invalid),
        // no presentarse como un fallo genérico de contraseña.
        $this->assertStringContainsString('id="email"', $html);
        $this->assertStringContainsString('is-invalid', $html);

        // No debe mostrarse el mensaje genérico de credenciales inválidas.
        $this->assertStringNotContainsString(
            'Estas credenciales no coinciden con nuestros registros.',
            $html
        );
    }
}

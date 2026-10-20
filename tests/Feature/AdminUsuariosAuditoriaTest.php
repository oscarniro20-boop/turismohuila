<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Favorito;
use App\Models\Resena;
use App\Models\User;
use App\Services\AuditoriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Fase 5 (gestión de usuarios por admin) + Fase 6 (auditoría central).
 *
 * Guards clave: lista cerrada de roles, no auto-eliminación, no eliminar
 * al último admin, y registro best-effort de cada operación.
 */
class AdminUsuariosAuditoriaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    // ── Acceso ───────────────────────────────────────────────────────────

    public function test_invitado_es_redirigido_a_login(): void
    {
        $this->get('/admin/usuarios')->assertRedirect(route('login'));
        $this->get('/admin/auditorias')->assertRedirect(route('login'));
    }

    public function test_turista_recibe_403_en_rutas_de_gestion(): void
    {
        $turista = User::factory()->create();
        $otro = User::factory()->create();

        $this->actingAs($turista)->get(route('admin.usuarios.index'))->assertForbidden();
        $this->actingAs($turista)->post(route('admin.usuarios.store'))->assertForbidden();
        $this->actingAs($turista)->put(route('admin.usuarios.update', $otro))->assertForbidden();
        $this->actingAs($turista)->delete(route('admin.usuarios.destroy', $otro))->assertForbidden();
        $this->actingAs($turista)->get(route('admin.auditorias.index'))->assertForbidden();
    }

    // ── Listado y búsqueda ───────────────────────────────────────────────

    public function test_index_lista_usuarios_y_filtra_por_nombre_o_correo(): void
    {
        $admin = $this->admin();
        User::factory()->create(['name' => 'Maria Castano', 'email' => 'maria@example.com']);
        User::factory()->create(['name' => 'Pedro Gomez', 'email' => 'pedro@example.com']);

        $this->actingAs($admin)->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertSee('Maria Castano')
            ->assertSee('Pedro Gomez');

        $this->actingAs($admin)->get(route('admin.usuarios.index', ['q' => 'castano']))
            ->assertOk()
            ->assertSee('Maria Castano')
            ->assertDontSee('Pedro Gomez');

        $this->actingAs($admin)->get(route('admin.usuarios.index', ['q' => 'pedro@']))
            ->assertOk()
            ->assertSee('pedro@example.com')
            ->assertDontSee('maria@example.com');
    }

    // ── Crear usuario ────────────────────────────────────────────────────

    public function test_store_crea_usuario_con_rol_y_registra_auditoria(): void
    {
        $admin = $this->admin();

        $respuesta = $this->actingAs($admin)->post(route('admin.usuarios.store'), [
            'name' => 'Nuevo Turista',
            'email' => 'nuevo@example.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'rol' => 'turista',
        ]);

        $respuesta->assertRedirect(route('admin.usuarios.index'));

        $usuario = User::where('email', 'nuevo@example.com')->first();
        $this->assertNotNull($usuario);
        $this->assertSame('turista', $usuario->rol);
        $this->assertTrue(Hash::check('secreto123', $usuario->password));

        $registro = Auditoria::query()->where('accion', 'usuario.creado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($admin->id, $registro->user_id);
        $this->assertSame('usuario', $registro->entidad);
        $this->assertSame($usuario->id, $registro->entidad_id);
        $this->assertSame('nuevo@example.com', $registro->datos['email']);
        $this->assertSame('turista', $registro->datos['rol']);
    }

    public function test_store_rechaza_rol_fuera_de_la_lista_cerrada(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.usuarios.store'), [
            'name' => 'Intruso',
            'email' => 'intruso@example.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'rol' => 'superadmin',
        ])->assertSessionHasErrors('rol');

        $this->assertDatabaseMissing('users', ['email' => 'intruso@example.com']);
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_store_rechaza_email_duplicado(): void
    {
        $admin = $this->admin();
        User::factory()->create(['email' => 'ocupado@example.com']);

        $this->actingAs($admin)->post(route('admin.usuarios.store'), [
            'name' => 'Repetido',
            'email' => 'ocupado@example.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'rol' => 'turista',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 2); // admin + existente
    }

    // ── Actualizar ────────────────────────────────────────────────────────

    public function test_update_cambia_rol_y_registra_el_cambio_en_auditoria(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.usuarios.update', $turista), [
            'name' => $turista->name,
            'email' => $turista->email,
            'rol' => 'admin',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertSame('admin', $turista->fresh()->rol);

        $registro = Auditoria::query()->where('accion', 'usuario.actualizado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($turista->id, $registro->entidad_id);
        $this->assertSame('turista', $registro->datos['campos']['rol']['antes']);
        $this->assertSame('admin', $registro->datos['campos']['rol']['después']);
    }

    public function test_update_sin_cambios_no_registra_auditoria(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.usuarios.update', $turista), [
            'name' => $turista->name,
            'email' => $turista->email,
            'rol' => $turista->rol,
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_update_con_password_reemplaza_la_contraseña(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.usuarios.update', $turista), [
            'name' => $turista->name,
            'email' => $turista->email,
            'rol' => 'turista',
            'password' => 'nuevaClave123',
            'password_confirmation' => 'nuevaClave123',
        ]);

        $this->assertTrue(Hash::check('nuevaClave123', $turista->fresh()->password));
    }

    public function test_update_valida_rol_y_email(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();
        User::factory()->create(['email' => 'ocupado@example.com']);

        $this->actingAs($admin)->put(route('admin.usuarios.update', $turista), [
            'name' => $turista->name,
            'email' => $turista->email,
            'rol' => 'superadmin',
        ])->assertSessionHasErrors('rol');

        $this->actingAs($admin)->put(route('admin.usuarios.update', $turista), [
            'name' => $turista->name,
            'email' => 'ocupado@example.com',
            'rol' => 'turista',
        ])->assertSessionHasErrors('email');
    }

    // ── Eliminar (guards) ─────────────────────────────────────────────────

    public function test_destroy_elimina_al_usuario_y_su_contenido_cascada(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        Resena::create([
            'user_id' => $turista->id,
            'tipo' => 'lugar',
            'referencia_id' => 1,
            'comentario' => 'Lugar bonito',
            'calificacion' => 4,
        ]);
        Favorito::create([
            'user_id' => $turista->id,
            'tipo' => 'lugar',
            'referencia_id' => 1,
        ]);
        $turistaId = $turista->id;
        $turistaNombre = $turista->name;

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $turista))
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseMissing('users', ['id' => $turistaId]);
        $this->assertDatabaseCount('resenas', 0);
        $this->assertDatabaseCount('favoritos', 0);

        $registro = Auditoria::query()->where('accion', 'usuario.eliminado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($turistaId, $registro->entidad_id);
        $this->assertSame($turistaNombre, $registro->datos['name']);
        $this->assertSame(1, $registro->datos['resenas_eliminadas']);
    }

    public function test_no_se_puede_eliminar_la_propia_cuenta(): void
    {
        $admin = $this->admin();
        $otroAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $admin))
            ->assertRedirect(route('admin.usuarios.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_admin_unico_no_puede_eliminar_su_cuenta(): void
    {
        // El guard "no eliminar al último admin" actúa como red de seguridad;
        // con un solo admin, cualquier intento de borrado de su cuenta (único
        // admin) queda bloqueado observándoselo por auto-eliminación primero.
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $admin))
            ->assertRedirect(route('admin.usuarios.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_se_puede_eliminar_a_un_admin_si_queda_al_menos_otro(): void
    {
        $adminA = $this->admin();
        $adminB = User::factory()->admin()->create();

        $this->actingAs($adminA)->delete(route('admin.usuarios.destroy', $adminB))
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseMissing('users', ['id' => $adminB->id]);
        $this->assertDatabaseHas('users', ['id' => $adminA->id]);

        $registro = Auditoria::query()->where('accion', 'usuario.eliminado')->latest('id')->first();
        $this->assertSame($adminB->id, $registro->entidad_id);
    }
    // ── Auditoría central (página y servicio) ─────────────────────────────

    public function test_auditoria_index_muestra_registros_y_permite_filtrar(): void
    {
        $admin = $this->admin();

        Auditoria::create([
            'user_id' => $admin->id,
            'accion' => 'usuario.creado',
            'entidad' => 'usuario',
            'entidad_id' => 5,
            'datos' => ['email' => 'x@y.co'],
        ]);
        Auditoria::create([
            'accion' => 'importacion.csv',
            'entidad' => 'importacion',
            'datos' => ['modulo' => 'lugares'],
        ]);

        $this->actingAs($admin)->get(route('admin.auditorias.index'))
            ->assertOk()
            ->assertSee('usuario.creado')
            ->assertSee('importacion.csv')
            ->assertSee($admin->name)
            ->assertSee('x@y.co');

        // Filtro: al ver solo importaciones, el detalle del registro de
        // usuario no aparece (los <option> del filtro no contienen datos json).
        $this->actingAs($admin)->get(route('admin.auditorias.index', ['accion' => 'importacion.csv']))
            ->assertOk()
            ->assertSee('importacion.csv')
            ->assertDontSee('x@y.co');
    }

    public function test_eliminar_usuario_conserva_su_historial_de_auditoria(): void
    {
        $admin = $this->admin();
        $turista = User::factory()->create();

        Auditoria::create([
            'user_id' => $turista->id,
            'accion' => 'algo.realizado',
            'entidad' => 'lugar',
            'entidad_id' => 1,
        ]);

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $turista));

        // FK nullOnDelete: el rastro sobrevive al usuario borrado.
        $registro = Auditoria::query()->where('accion', 'algo.realizado')->first();
        $this->assertNotNull($registro);
        $this->assertNull($registro->user_id);
    }

    public function test_servicio_auditoria_es_best_effort_y_no_rompe_la_operacion(): void
    {
        Log::spy();

        // Simula un fallo de persistencia de la auditoría.
        Auditoria::creating(function (): void {
            throw new \RuntimeException('fallo simulado de auditoría');
        });

        // No debe lanzar excepción: la operación auditada continúa igual.
        app(AuditoriaService::class)
            ->registrar('prueba.fallo', 'prueba', 1, ['x' => 1]);

        Log::shouldHaveReceived('warning')->withArgs(
            fn ($mensaje, $contexto) => $mensaje === 'auditoria_no_registrada'
                && ($contexto['accion'] ?? null) === 'prueba.fallo'
        );

        $this->assertDatabaseCount('auditorias', 0);
        $this->addToAssertionCount(1); // no lanzar excepción ya es la aserción
    }

    public function test_las_auditorias_no_tienen_rutas_de_escritura(): void
    {
        // La interfaz ordinaria no puede modificar ni eliminar registros.
        $this->assertFalse(Route::has('admin.auditorias.store'));
        $this->assertFalse(Route::has('admin.auditorias.update'));
        $this->assertFalse(Route::has('admin.auditorias.destroy'));

        // La única vía es de lectura y está protegida por auth + rol:admin.
        $ruta = Route::getRoutes()->getByName('admin.auditorias.index');
        $this->assertNotNull($ruta);
        $this->assertSame(['GET', 'HEAD'], $ruta->methods());

        $middlewares = $ruta->gatherMiddleware();
        $this->assertContains('auth', $middlewares);
        $this->assertContains('rol:admin', $middlewares);
    }

    // ── Controles de privilegios de rol (hardening, servidor + pruebas) ────

    public function test_admin_no_puede_cambiar_su_propio_rol(): void
    {
        $adminA = $this->admin();
        User::factory()->admin()->create(); // hay otro admin: el sistema queda cubierto

        $this->actingAs($adminA)->put(route('admin.usuarios.update', $adminA), [
            'name' => $adminA->name,
            'email' => $adminA->email,
            'rol' => 'turista',
        ])->assertRedirect(route('admin.usuarios.index'))->assertSessionHas('error');

        $this->assertSame('admin', $adminA->fresh()->rol);
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_unico_admin_no_puede_degradarse_a_si_mismo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.usuarios.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'rol' => 'turista',
        ])->assertRedirect(route('admin.usuarios.index'))->assertSessionHas('error');

        // Reglas cruzadas: no se degrada a sí mismo y el sistema no pierde
        // su único administrador activo.
        $this->assertSame('admin', $admin->fresh()->rol);
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_se_puede_degradar_a_otro_admin_si_queda_al_menos_uno_activo(): void
    {
        $adminA = $this->admin();
        $adminB = User::factory()->admin()->create();

        $this->actingAs($adminA)->put(route('admin.usuarios.update', $adminB), [
            'name' => $adminB->name,
            'email' => $adminB->email,
            'rol' => 'turista',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect(route('admin.usuarios.index'));

        $this->assertSame('turista', $adminB->fresh()->rol);
        $this->assertSame('admin', $adminA->fresh()->rol);
        $this->assertTrue(Hash::check('secreto123', $adminB->fresh()->password));

        $registro = Auditoria::query()->where('accion', 'usuario.actualizado')->latest('id')->first();
        $this->assertNotNull($registro);
        $this->assertSame($adminB->id, $registro->entidad_id);
        $this->assertSame('turista', $registro->datos['campos']['rol']['después'] ?? null);
        $this->assertSame('actualizado', $registro->datos['campos']['password'] ?? null);

        // La auditoría registra la acción pero nunca la contraseña ni su hash.
        $json = json_encode($registro->datos, JSON_UNESCAPED_UNICODE);
        $this->assertStringNotContainsString('secreto123', $json);
        $this->assertStringNotContainsString('$2y$', $json);
    }

    public function test_no_se_puede_degradar_al_unico_admin_activo_desde_otra_sesion(): void
    {
        // El servidor no confía en quién pide el cambio: una sesión de admin
        // ya desactivada no puede degradar al único admin con acceso.
        $adminActivo = $this->admin();
        $adminInactivo = User::factory()->admin()->create(['activo' => false]);

        $this->actingAs($adminInactivo)->put(route('admin.usuarios.update', $adminActivo), [
            'name' => $adminActivo->name,
            'email' => $adminActivo->email,
            'rol' => 'turista',
        ])->assertRedirect(route('admin.usuarios.index'))->assertSessionHas('error');

        $this->assertSame('admin', $adminActivo->fresh()->rol);
        $this->assertDatabaseCount('auditorias', 0);
    }

    public function test_turista_no_puede_cambiar_rol_de_otros_usuarios(): void
    {
        $turista = User::factory()->create();
        $otro = User::factory()->create();

        $this->actingAs($turista)
            ->put(route('admin.usuarios.update', $otro), [
                'name' => $otro->name,
                'email' => $otro->email,
                'rol' => 'admin',
            ])
            ->assertForbidden();

        $this->assertSame('turista', $otro->fresh()->rol);
    }
}
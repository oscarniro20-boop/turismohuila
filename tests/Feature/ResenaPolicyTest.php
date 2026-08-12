<?php

namespace Tests\Feature;

use App\Models\Lugar;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResenaPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_update_own_resena(): void
    {
        $user = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $resena = Resena::create([
            'user_id' => $user->id,
            'tipo' => 'lugar',
            'referencia_id' => $lugar->id,
            'comentario' => 'Excelente lugar',
            'calificacion' => 5,
        ]);

        $this->actingAs($user);

        $response = $this->patch(route('resenas.update', $resena), [
            'comentario' => 'Actualizado',
            'calificacion' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resenas', [
            'id' => $resena->id,
            'comentario' => 'Actualizado',
            'calificacion' => 4,
        ]);
    }

    public function test_other_user_cannot_update_resena(): void
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $resena = Resena::create([
            'user_id' => $author->id,
            'tipo' => 'lugar',
            'referencia_id' => $lugar->id,
            'comentario' => 'Excelente lugar',
            'calificacion' => 5,
        ]);

        $this->actingAs($otherUser);

        $response = $this->patch(route('resenas.update', $resena), [
            'comentario' => 'Intento de edición',
            'calificacion' => 1,
        ]);

        $response->assertForbidden();
    }

    public function test_author_can_delete_own_resena(): void
    {
        $user = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $resena = Resena::create([
            'user_id' => $user->id,
            'tipo' => 'lugar',
            'referencia_id' => $lugar->id,
            'comentario' => 'Excelente lugar',
            'calificacion' => 5,
        ]);

        $this->actingAs($user);

        $response = $this->delete(route('resenas.destroy', $resena));

        $response->assertRedirect();
        $this->assertDatabaseMissing('resenas', ['id' => $resena->id]);
    }

    public function test_admin_can_delete_any_resena(): void
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        $author = User::factory()->create();
        $lugar = Lugar::factory()->create();

        $resena = Resena::create([
            'user_id' => $author->id,
            'tipo' => 'lugar',
            'referencia_id' => $lugar->id,
            'comentario' => 'Excelente lugar',
            'calificacion' => 5,
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('resenas.destroy', $resena));

        $response->assertRedirect();
        $this->assertDatabaseMissing('resenas', ['id' => $resena->id]);
    }
}
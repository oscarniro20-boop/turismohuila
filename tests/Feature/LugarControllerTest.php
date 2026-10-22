<?php

namespace Tests\Feature;

use App\Models\Lugar;
use App\Models\LugarImagen;
use App\Models\User;
use App\Services\CloudinaryImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class LugarControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['rol' => 'admin']);
    }

    private function datosValidos(): array
    {
        return [
            'nombre'       => 'Prueba ' . uniqid(),
            'departamento' => 'Huila',
            'municipio'    => 'Neiva',
            'categoria'    => 'Naturaleza',
            'latitud'      => 2.9273,
            'longitud'     => -75.2819,
            'descripcion'  => 'Descripción de prueba suficientemente larga.',
        ];
    }

    public function test_store_ignora_user_id_enviado_en_request(): void
    {
        $admin = $this->admin();

        $datos = $this->datosValidos();
        $datos['user_id'] = 999; // Intento de mass-assignment

        $this->actingAs($admin)
            ->post(route('lugares.store'), $datos)
            ->assertRedirect();

        $this->assertDatabaseHas('lugares', [
            'nombre' => $datos['nombre'],
            'user_id' => $admin->id, // Debe ser el del admin autenticado, no 999
        ]);

        $lugar = Lugar::where('nombre', $datos['nombre'])->first();
        $this->assertNotSame(999, $lugar->user_id);
        $this->assertSame($admin->id, $lugar->user_id);
    }

    public function test_update_no_borra_galeria_por_defecto(): void
    {
        $admin = $this->admin();
        $lugar = Lugar::factory()->create(['user_id' => $admin->id]);

        // Imagen de galería existente antes del update
        LugarImagen::create([
            'lugar_id' => $lugar->id,
            'ruta' => 'https://res.cloudinary.com/test/image-galeria.jpg',
            'orden' => 1,
        ]);

        // Update sin eliminar_imagenes ni imagenes nuevas
        $this->actingAs($admin)
            ->put(route('lugares.update', $lugar), $this->datosValidos())
            ->assertRedirect();

        // La galería previa debe seguir intacta
        $this->assertDatabaseHas('lugar_imagenes', [
            'lugar_id' => $lugar->id,
            'ruta' => 'https://res.cloudinary.com/test/image-galeria.jpg',
            'orden' => 1,
        ]);
    }

    public function test_update_no_borra_imagen_de_otro_lugar(): void
    {
        $admin = $this->admin();
        $lugar = Lugar::factory()->create(['user_id' => $admin->id]);
        $otroLugar = Lugar::factory()->create(['user_id' => $admin->id]);

        // Imagen de galería de este lugar
        $imagenPropia = LugarImagen::create([
            'lugar_id' => $lugar->id,
            'ruta' => 'https://res.cloudinary.com/test/propia.jpg',
            'orden' => 1,
        ]);

        // Imagen de galería de OTRO lugar (intento de borrado ajeno)
        LugarImagen::create([
            'lugar_id' => $otroLugar->id,
            'ruta' => 'https://res.cloudinary.com/test/ajena.jpg',
            'orden' => 1,
        ]);

        // Update solicitando eliminar la imagen del otro lugar (ID ajeno)
        $this->actingAs($admin)
            ->put(route('lugares.update', $lugar), array_merge($this->datosValidos(), [
                'eliminar_imagenes' => [$otroLugar->imagenes->first()->id ?? 99999],
            ]))
            ->assertRedirect();

        // La imagen propia debe seguir existiendo
        $this->assertDatabaseHas('lugar_imagenes', [
            'id' => $imagenPropia->id,
            'ruta' => 'https://res.cloudinary.com/test/propia.jpg',
        ]);

        // La imagen del otro lugar debe seguir existiendo (no se pudo borrar)
        $this->assertDatabaseHas('lugar_imagenes', [
            'lugar_id' => $otroLugar->id,
            'ruta' => 'https://res.cloudinary.com/test/ajena.jpg',
        ]);
    }

    public function test_update_rollback_galeria_intacta_y_huerfano_borrado(): void
    {
        $admin = $this->admin();
        $lugar = Lugar::factory()->create(['user_id' => $admin->id]);

        // Imagen previa de galería (debe sobrevivir al rollback)
        $previa = LugarImagen::create([
            'lugar_id' => $lugar->id,
            'ruta' => 'https://res.cloudinary.com/test/previa.jpg',
            'orden' => 1,
        ]);

        // Mock Cloudinary: subir retorna URL falsa, eliminarPorUrl se espía
        $cloudinary = Mockery::mock(CloudinaryImageService::class);
        $cloudinary->shouldReceive('subir')->andReturn('https://res.cloudinary.com/test/nueva.jpg');
        $cloudinary->shouldReceive('esUrlCloudinary')->andReturn(true);
        $cloudinary->shouldReceive('eliminarPorUrl')->andReturn(true);
        app()->instance(CloudinaryImageService::class, $cloudinary);

        // Forzar fallo dentro de la transacción al insertar en lugar_imagenes
        $disparado = false;
        DB::listen(function ($query) use (&$disparado) {
            if (! $disparado && str_contains($query->sql, 'lugar_imagenes') && str_contains($query->sql, 'insert')) {
                $disparado = true;
                throw new \RuntimeException('Fallo simulado en transacción');
            }
        });

        $this->actingAs($admin)
            ->put(route('lugares.update', $lugar), array_merge($this->datosValidos(), [
                'imagenes' => [UploadedFile::fake()->image('foto.jpg')],
            ]))
            ->assertStatus(500);

        // 1. La galería previa sigue intacta (rollback de la transacción)
        $this->assertDatabaseHas('lugar_imagenes', [
            'id' => $previa->id,
            'ruta' => 'https://res.cloudinary.com/test/previa.jpg',
            'orden' => 1,
        ]);

        // 2. No quedó registro huérfano de la nueva imagen en la BD
        $this->assertDatabaseMissing('lugar_imagenes', [
            'ruta' => 'https://res.cloudinary.com/test/nueva.jpg',
        ]);

        // 3. Se intentó borrar de Cloudinary la imagen huérfana recién subida
        $cloudinary->shouldHaveReceived('eliminarPorUrl');
    }
}
<?php

namespace Tests\Feature;

use App\Models\Agencia;
use App\Models\Auditoria;
use App\Models\Guia;
use App\Models\Hotel;
use App\Models\Lugar;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Transporte;
use App\Models\User;
use App\Services\ImportacionCsvService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Importación masiva CSV (Etapa C): acceso admin, plantillas, validación
 * por módulo, duplicados, límites y consistencia transaccional.
 */
class ImportacionCsvTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    /** Sube un CSV de prueba a la ruta de importación (como admin por defecto). */
    private function subir(string $categoria, string $csv, ?User $usuario = null): TestResponse
    {
        return $this->actingAs($usuario ?? $this->admin())->post(route('admin.importacion.procesar'), [
            'categoria' => $categoria,
            'archivo' => UploadedFile::fake()->createWithContent('datos.csv', $csv),
        ]);
    }

    // ── Acceso y rutas ───────────────────────────────────────────────────

    public function test_invitado_es_redirigido_a_login(): void
    {
        $this->get('/admin/importacion')->assertRedirect(route('login'));
    }

    public function test_usuario_no_admin_recibe_403(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get('/admin/importacion')->assertForbidden();
        $this->actingAs($usuario)->post(route('admin.importacion.procesar'))->assertForbidden();
    }

    public function test_admin_accede_al_panel_y_ve_las_siete_categorias(): void
    {
        $this->assertCount(7, ImportacionCsvService::categorias());

        $response = $this->actingAs($this->admin())->get(route('admin.importacion.index'));

        $response->assertOk()->assertSee('Importación masiva CSV');

        // La vista enlaza la plantilla de la categoría seleccionada (por
        // defecto lugares) y muestra las siete etiquetas en el selector.
        $response->assertSee(route('admin.importacion.plantilla', 'lugares'), false);

        foreach (ImportacionCsvService::categorias() as $etiqueta) {
            $response->assertSee($etiqueta, false);
        }
    }

    // ── Plantillas ───────────────────────────────────────────────────────

    public function test_las_siete_plantillas_se_descargan_con_su_cabecera_real(): void
    {
        $admin = $this->admin();

        foreach (ImportacionCsvService::categorias() as $clave => $etiqueta) {
            $columnas = array_keys(config("importacion.modulos.$clave.columnas"));

            $response = $this->actingAs($admin)->get(route('admin.importacion.plantilla', $clave));
            $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

            $csv = $response->getContent();
            $this->assertStringStartsWith("\xEF\xBB\xBF", $csv, "La plantilla de {$etiqueta} debe llevar BOM UTF-8.");

            $lineas = explode("\n", preg_replace('/^\xEF\xBB\xBF/', '', $csv));
            $cabecera = str_getcsv(rtrim(array_shift($lineas), "\r"), ';', '"', '\\');

            $this->assertSame($columnas, $cabecera, "Cabecera de {$etiqueta} distinta de las columnas reales.");
            $this->assertNotSame('', trim($lineas[0] ?? ''), "La plantilla de {$etiqueta} debe incluir una fila de ejemplo.");
        }
    }

    public function test_plantilla_de_categoria_invalida_devuelve_404(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.importacion.plantilla', 'no-existe'))
            ->assertNotFound();
    }

    public function test_categoria_invalida_en_el_formulario_es_rechazada_por_validacion(): void
    {
        $this->actingAs($this->admin())->post(route('admin.importacion.procesar'), [
            'categoria' => 'no-existe',
            'archivo' => UploadedFile::fake()->createWithContent('datos.csv', "a,b\n1,2\n"),
        ])->assertSessionHasErrors('categoria');
    }

    // ── Importación válida por módulo ────────────────────────────────────

    public function test_importa_lugares_con_utf8_tildes_y_autor(): void
    {
        $admin = $this->admin();
        $csv = "nombre,departamento,municipio,categoria,descripcion,latitud,longitud\n"
            . "\"Cañón del Río Magdalena\",Huila,Pitalito,Naturaleza,\"Cañón natural, al sur.\",1.8537,-76.0507\n";

        $this->subir('lugares', $csv, $admin)
            ->assertRedirect(route('admin.importacion.index', ['categoria' => 'lugares']))
            ->assertSessionHas('importacion_resumen.leidas', 1)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('lugares', [
            'nombre' => 'Cañón del Río Magdalena',
            'departamento' => 'Huila',
            'municipio' => 'Pitalito',
            'categoria' => 'Naturaleza',
            'user_id' => $admin->id,
        ]);
    }

    public function test_importa_hoteles_valido(): void
    {
        $csv = "nombre;departamento;municipio;descripcion;precio_noche;estrellas;direccion;telefono\n"
            . "Hotel Terrón Blanco;Huila;Neiva;Hotel céntrico con vista al río.;180000;4;Cra 5 # 12-34;8876543\n";

        $this->subir('hoteles', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('hoteles', [
            'nombre' => 'Hotel Terrón Blanco',
            'municipio' => 'Neiva',
            'estrellas' => 4,
            'precio_noche' => 180000,
        ]);
    }

    public function test_importa_restaurantes_valido(): void
    {
        $csv = "nombre,departamento,municipio,descripcion,tipo_cocina,precio_promedio,direccion\n"
            . "La Cosecha,Huila,Neiva,\"Cocina huilense de autor.\",Típica,35000,\"Cra 7 # 15-20\"\n";

        $this->subir('restaurantes', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('restaurantes', [
            'nombre' => 'La Cosecha',
            'tipo_cocina' => 'Típica',
            'precio_promedio' => 35000,
        ]);
    }

    public function test_importa_tours_valido(): void
    {
        $csv = "nombre;departamento;municipio;descripcion;precio;duracion_dias;incluye;cupo_maximo;telefono_contacto\n"
            . "Tour Tatacoa;Huila;Villavieja;Recorrido guiado por el desierto.;80000;1;Transporte y guía.;15;8879999\n";

        $this->subir('tours', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('tours', [
            'nombre' => 'Tour Tatacoa',
            'municipio' => 'Villavieja',
            'cupo_maximo' => 15,
            'duracion_dias' => 1,
        ]);
    }

    public function test_importa_guias_sin_columna_municipio_y_con_zonas(): void
    {
        $csv = "nombre,departamento,bio,idiomas,experiencia_anios,zonas,whatsapp,email,activo\n"
            . "María Pérez,Huila,\"Guía certificada.\",Español Inglés,10,\"Neiva, Pitalito, San Agustín\",3001234567,guia@example.com,1\n";

        $this->subir('guias', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('guias', [
            'nombre' => 'María Pérez',
            'departamento' => 'Huila',
            'zonas' => 'Neiva, Pitalito, San Agustín',
            'activo' => 1,
        ]);
    }

    public function test_importa_agencias_valido(): void
    {
        $csv = "nombre;departamento;municipio;telefono_contacto;whatsapp;email\n"
            . "Agencia Andina;Huila;Neiva;8876000;3009876543;contacto@example.com\n";

        $this->subir('agencias', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('agencias', [
            'nombre' => 'Agencia Andina',
            'municipio' => 'Neiva',
            'whatsapp' => '3009876543',
        ]);
    }

    public function test_importa_transportes_valido(): void
    {
        $csv = "nombre,departamento,municipio,descripcion,tipo,precio,capacidad,telefono\n"
            . "Rutas del Sur,Huila,Pitalito,Servicio intermunicipal.,Bus,12000,40,8875555\n";

        $this->subir('transportes', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('transportes', [
            'nombre' => 'Rutas del Sur',
            'tipo' => 'Bus',
            'capacidad' => 40,
        ]);
    }

    public function test_admite_separador_tabulado(): void
    {
        $csv = "nombre\tdescripcion\ttipo_cocina\tprecio_promedio\tdepartamento\tmunicipio\n"
            . "El Rincón\tCocina casera\tTípica\t25000\tHuila\tNeiva\n";

        $this->subir('restaurantes', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 0);

        $this->assertDatabaseHas('restaurantes', ['nombre' => 'El Rincón']);
    }

    // ── Archivos con estructura inválida (rechazo total) ─────────────────

    public function test_archivo_vacio_es_rechazado(): void
    {
        $respuesta = $this->subir('lugares', "\n \n");

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('vacío', session('errors')->first('archivo'));
    }

    public function test_archivo_solo_con_cabecera_es_rechazado(): void
    {
        $cabecera = implode(',', array_keys(config('importacion.modulos.lugares.columnas')));

        $respuesta = $this->subir('lugares', $cabecera."\n");

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('no contiene filas de datos', session('errors')->first('archivo'));
    }

    public function test_cabecera_con_columna_desconocida_es_rechazada(): void
    {
        $csv = "nombre,municipio,precio_base\nX,Neiva,100\n";
        $respuesta = $this->subir('lugares', $csv);

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('desconocidas', session('errors')->first('archivo'));
        $this->assertStringContainsString('precio_base', session('errors')->first('archivo'));
        $this->assertDatabaseCount('lugares', 0);
    }

    public function test_cabecera_con_columnas_duplicadas_es_rechazada(): void
    {
        $csv = "nombre,nombre,municipio\nA,B,Neiva\n";
        $respuesta = $this->subir('lugares', $csv);

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('repite columnas', session('errors')->first('archivo'));
    }

    public function test_cabecera_sin_columna_requerida_es_rechazada(): void
    {
        $csv = "nombre,departamento,descripcion\nX,Huila,Texto\n";
        $respuesta = $this->subir('lugares', $csv);

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('columnas requeridas', session('errors')->first('archivo'));
    }

    public function test_cabecera_no_utf8_es_rechazada(): void
    {
        $csv = "nomb\xC3\x28a,municipio\nX,Neiva\n";
        $respuesta = $this->subir('lugares', $csv);

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('UTF-8', session('errors')->first('archivo'));
    }

    // ── Filas inválidas (rechazo por fila, sin frenar el archivo) ─────────

    public function test_precio_no_numerico_rechaza_solo_esa_fila(): void
    {
        $csv = "nombre,departamento,municipio,descripcion,tipo_cocina,precio_promedio\n"
            . "Buen Resto,Huila,Neiva,Comida buena.,Típica,mil pesos\n"
            . "Otro Resto,Huila,Neiva,Otra opción.,Típica,20000\n";

        $this->subir('restaurantes', $csv)
            ->assertSessionHas('importacion_resumen.leidas', 2)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 1);

        $this->assertDatabaseHas('restaurantes', ['nombre' => 'Otro Resto']);
        $this->assertDatabaseMissing('restaurantes', ['nombre' => 'Buen Resto']);
    }

    public function test_departamento_inexistente_rechaza_la_fila(): void
    {
        $csv = "nombre,departamento,municipio,categoria,descripcion\n"
            . "Lugar Raro,Atlantida,Neiva,Naturaleza,Texto\n";

        $this->subir('lugares', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 0)
            ->assertSessionHas('importacion_resumen.rechazadas', 1);

        $this->assertDatabaseCount('lugares', 0);
    }

    public function test_municipio_que_no_pertenece_al_departamento_rechaza_la_fila(): void
    {
        $csv = "nombre,departamento,municipio,categoria,descripcion\n"
            . "Lugar Cruzado,Bogotá,Pitalito,Naturaleza,Texto\n";

        $this->subir('lugares', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 0)
            ->assertSessionHas('importacion_resumen.rechazadas', 1);

        $this->assertDatabaseCount('lugares', 0);
    }

    public function test_fila_no_utf8_se_rechaza_sin_importar(): void
    {
        $csv = "nombre,municipio,categoria,descripcion\n\xC3\x28mal,Neiva,Naturaleza,Texto\n";

        $this->subir('lugares', $csv)
            ->assertSessionHas('importacion_resumen.importadas', 0)
            ->assertSessionHas('importacion_resumen.rechazadas', 1);

        $this->assertDatabaseCount('lugares', 0);
    }

    // ── Límites del archivo ──────────────────────────────────────────────

    public function test_supera_el_maximo_de_filas_y_no_importa(): void
    {
        config(['importacion.max_filas' => 2]);

        $csv = "nombre,municipio,categoria,descripcion\n"
            . "A,Neiva,Naturaleza,T1\nB,Neiva,Naturaleza,T2\nC,Neiva,Naturaleza,T3\n";

        $respuesta = $this->subir('lugares', $csv);

        $respuesta->assertSessionHasErrors('archivo');
        $this->assertStringContainsString('máximo permitido', session('errors')->first('archivo'));
        $this->assertDatabaseCount('lugares', 0);
    }

    public function test_supera_el_peso_maximo_y_no_importa(): void
    {
        config(['importacion.max_peso_kb' => 1]);

        $csv = "nombre,municipio,categoria,descripcion\n"
            . 'A,Neiva,Naturaleza,'.str_repeat('x', 3000)."\n";

        $this->subir('lugares', $csv)->assertSessionHasErrors('archivo');
        $this->assertDatabaseCount('lugares', 0);
    }

    // ── Duplicados y consistencia transaccional ───────────────────────────

    public function test_duplicado_dentro_del_archivo_no_se_importa(): void
    {
        $csv = "nombre,departamento,municipio,categoria,descripcion\n"
            . "Mismo Lugar,Huila,Neiva,Naturaleza,Texto\n"
            . "Mismo Lugar,Huila,Neiva,Naturaleza,Texto\n";

        $this->subir('lugares', $csv)
            ->assertSessionHas('importacion_resumen.leidas', 2)
            ->assertSessionHas('importacion_resumen.importadas', 1)
            ->assertSessionHas('importacion_resumen.rechazadas', 1)
            ->assertSessionHas('importacion_resumen.duplicados', 1);

        $this->assertDatabaseCount('lugares', 1);
    }

    public function test_duplicado_existente_en_bd_no_se_duplica_ni_se_modifica(): void
    {
        $lugar = Lugar::create([
            'nombre' => 'Lugar Existente',
            'departamento' => 'Huila',
            'municipio' => 'Neiva',
            'categoria' => 'Naturaleza',
            'descripcion' => 'Descripción original',
        ]);

        $csv = "nombre,departamento,municipio,categoria,descripcion\n"
            . "Lugar Existente,Huila,Neiva,Naturaleza,Descripción modificada por CSV\n";

        $this->subir('lugares', $csv)
            ->assertSessionHas('importacion_resumen.leidas', 1)
            ->assertSessionHas('importacion_resumen.importadas', 0)
            ->assertSessionHas('importacion_resumen.rechazadas', 1)
            ->assertSessionHas('importacion_resumen.duplicados', 1);

        // La importación solo inserta: el registro existente queda intacto.
        $this->assertDatabaseCount('lugares', 1);
        $this->assertDatabaseHas('lugares', [
            'id' => $lugar->id,
            'descripcion' => 'Descripción original',
        ]);
    }

    public function test_fallo_de_escritura_no_duplica_ni_sobrecuenta(): void
    {
        $simularFallo = true;
        Hotel::creating(function (Hotel $hotel) use (&$simularFallo) {
            if ($simularFallo && $hotel->nombre === 'Hotel Que Falla') {
                throw new \RuntimeException('Fallo de escritura simulado');
            }
        });

        try {
            $csv = "nombre,departamento,municipio,descripcion,precio_noche,estrellas\n"
                . "Hotel Que Falla,Huila,Neiva,No se guarda.,100000,3\n"
                . "Hotel Correcto,Huila,Neiva,Sí se guarda.,120000,4\n";

            // El lote falla completo y se reintenta fila por fila: la fila
            // caída se cuenta como rechazada y la buena se confirma sola.
            $this->subir('hoteles', $csv)
                ->assertSessionHas('importacion_resumen.leidas', 2)
                ->assertSessionHas('importacion_resumen.importadas', 1)
                ->assertSessionHas('importacion_resumen.rechazadas', 1);

            $this->assertDatabaseCount('hoteles', 1);
            $this->assertDatabaseMissing('hoteles', ['nombre' => 'Hotel Que Falla']);
            $this->assertDatabaseHas('hoteles', ['nombre' => 'Hotel Correcto']);
        } finally {
            $simularFallo = false;
        }
    }

    public function test_operacion_se_registra_en_el_log_de_auditoria(): void
    {
        Log::spy();
        $admin = $this->admin();

        $csv = "nombre,municipio,categoria,descripcion\nRegistro Log,Neiva,Naturaleza,Texto\n";
        $this->subir('lugares', $csv, $admin);

        // Log de aplicación (se mantiene por compatibilidad con alertas).
        Log::shouldHaveReceived('info')->withArgs(
            fn ($mensaje, $contexto) => $mensaje === 'importacion_csv'
                && ($contexto['modulo'] ?? null) === 'lugares'
                && ($contexto['user_id'] ?? null) === $admin->id
                && ($contexto['importadas'] ?? null) === 1
                && ($contexto['resultado'] ?? null) === 'completa'
        );

        // Auditoría central (Fase 6): la misma operación queda registrada en
        // la tabla única con el usuario, la entidad y el resumen estructurado.
        $registro = Auditoria::query()
            ->where('accion', 'importacion.csv')
            ->latest('id')
            ->first();

        $this->assertNotNull($registro);
        $this->assertSame($admin->id, $registro->user_id);
        $this->assertSame('importacion', $registro->entidad);
        $this->assertSame('lugares', $registro->datos['modulo']);
        $this->assertSame(1, $registro->datos['importadas']);
        $this->assertSame('completa', $registro->datos['resultado']);
    }
}

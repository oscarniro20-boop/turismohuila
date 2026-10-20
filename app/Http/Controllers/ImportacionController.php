<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportacionCsvException;
use App\Services\ImportacionCsvService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Panel administrativo de importación masiva CSV.
 *
 * Rutas protegidas por auth + rol:admin (definidas en routes/web.php dentro
 * del grupo de administración). Solo admite la lista de categorías declarada
 * en config/importacion.php.
 */
class ImportacionController extends Controller
{
    public function __construct(private ImportacionCsvService $importador) {}

    public function index(Request $request)
    {
        $categorias = ImportacionCsvService::categorias();

        $categoria = (string) $request->query('categoria');
        if (! isset($categorias[$categoria])) {
            $categoria = array_key_first($categorias);
        }

        $definicion = config("importacion.modulos.$categoria");

        return view('admin.importacion', compact('categorias', 'categoria', 'definicion'));
    }

    public function plantilla(string $categoria)
    {
        if (! $this->importador->esCategoriaValida($categoria)) {
            abort(404);
        }

        return response($this->importador->plantilla($categoria), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla-'.$categoria.'.csv"',
        ]);
    }

    public function procesar(Request $request)
    {
        $datos = $request->validate([
            'categoria' => ['required', Rule::in(array_keys(ImportacionCsvService::categorias()))],
            'archivo' => ['required', 'file', 'mimes:csv,txt', 'max:'.(int) config('importacion.max_peso_kb')],
        ]);

        try {
            $resumen = $this->importador->importar(
                $request->file('archivo')->getRealPath(),
                $datos['categoria'],
                $request->user()?->id
            );
        } catch (ImportacionCsvException $e) {
            // Error fatal del archivo: nada que revertir (la escritura no inició
            // o el límite se evaluó antes de persistir).
            return back()->withInput()->withErrors(['archivo' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.importacion.index', ['categoria' => $datos['categoria']])
            ->with('importacion_resumen', $resumen);
    }
}

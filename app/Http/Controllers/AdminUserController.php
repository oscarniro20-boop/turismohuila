<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gestión administrativa de usuarios (Fase 5).
 *
 * Todas las rutas están bajo auth + rol:admin. 'rol' no es mass-assignable:
 * se asigna solo con asignación directa/forceCreate, nunca desde datos crudos
 * del request.
 */
class AdminUserController extends Controller
{
    /**
     * Cuentas con rol admin y acceso activo: la protección contra dejar el
     * sistema "sin administración" se mide siempre sobre este conjunto.
     */
    private function adminsActivos(): int
    {
        return User::query()->where('rol', 'admin')->where('activo', true)->count();
    }

    /** Listado paginado con búsqueda por nombre o correo. */
    public function index(Request $request): View
    {
        $busqueda = trim((string) $request->query('q', ''));

        $usuarios = User::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                // Escapar comodines LIKE (higiene de consulta).
                $like = addcslashes($busqueda, '%_');

                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', "%{$like}%")
                        ->orWhere('email', 'like', "%{$like}%");
                });
            })
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.usuarios', [
            'usuarios' => $usuarios,
            'busqueda' => $busqueda,
        ]);
    }

    /** Crea un usuario con el rol elegido (validado contra lista cerrada). */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', 'string', Rule::in(['admin', 'turista'])],
        ]);

        // 'rol' no es fillable: se asigna de forma explícita y deliberada.
        $usuario = User::forceCreate([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => $datos['rol'],
        ]);

        app(AuditoriaService::class)->registrar('usuario.creado', 'usuario', $usuario->id, [
            'name' => $usuario->name,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', "Usuario «{$usuario->name}» creado.");
    }

    /** Actualiza nombre, correo, rol y (opcionalmente) la contraseña. */
    public function update(Request $request, User $user): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'rol' => ['required', 'string', Rule::in(['admin', 'turista'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // ── Guard 1: el sistema nunca puede quedar sin administrador activo. ─
        // Si quitarle el rol al objetivo dejaría cero admins activos, se rechaza.
        if (
            $user->esAdmin()
            && $user->estaActivo()
            && $datos['rol'] !== 'admin'
            && $this->adminsActivos() <= 1
        ) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No se puede quitar el rol al último administrador activo del sitio.');
        }

        // ── Guard 2: nadie eleva o degrada su propio rol desde el panel. ─────
        // Protege la sesión actual: un admin no puede degradarse (ni "mejorarse"
        // enviando el mismo valor alternativo) cambiando su propia fila.
        if ($user->id === $request->user()->id && $datos['rol'] !== $user->rol) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes cambiar tu propio rol desde el panel de usuarios.');
        }

        $cambios = [];
        foreach (['name', 'email', 'rol'] as $campo) {
            if ((string) $user->{$campo} !== (string) $datos[$campo]) {
                $cambios[$campo] = ['antes' => $user->{$campo}, 'después' => $datos[$campo]];
            }
        }

        $user->name = $datos['name'];
        $user->email = $datos['email'];
        $user->rol = $datos['rol'];

        $password = $datos['password'] ?? null;
        if ($password !== null && $password !== '') {
            $user->password = Hash::make($password);
            $cambios['password'] = 'actualizado';
        }

        $user->save();

        if ($cambios !== []) {
            app(AuditoriaService::class)->registrar('usuario.actualizado', 'usuario', $user->id, [
                'campos' => $cambios,
            ]);
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', "Usuario «{$user->name}» actualizado.");
    }

    /** Elimina un usuario (reseñas y favoritos caen por FK cascade). */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta desde el panel.');
        }

        if ($user->esAdmin() && $user->estaActivo() && $this->adminsActivos() <= 1) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No se puede eliminar al último administrador activo del sitio.');
        }

        $datos = [
            'name' => $user->name,
            'email' => $user->email,
            'rol' => $user->rol,
            'resenas_eliminadas' => $user->resenas()->count(),
        ];
        $id = $user->id;

        $user->delete();

        app(AuditoriaService::class)->registrar('usuario.eliminado', 'usuario', $id, $datos);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario eliminado.');
    }

    /**
     * Activa o desactiva una cuenta (sin borrado físico: reseñas, favoritos
     * y demás datos asociados permanecen intactos).
     *
     * Valor explícito en el request (no toggle a ciegas) para que la acción
     * sea idempotente y verificable en servidor.
     */
    public function estado(Request $request, User $user): RedirectResponse
    {
        $datos = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $nuevoEstado = (bool) $datos['activo'];

        // ── Guard 1: un administrador no se desactiva a sí mismo. ────────────
        if ($user->id === $request->user()->id && ! $nuevoEstado) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No puedes desactivar tu propia cuenta desde el panel.');
        }

        // ── Guard 2: nunca desactivar al último administrador activo. ────────
        if (
            ! $nuevoEstado
            && $user->esAdmin()
            && $user->estaActivo()
            && $this->adminsActivos() <= 1
        ) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'No se puede desactivar al último administrador activo del sitio.');
        }

        // Sin cambios: no se escribe ni se audita.
        if ($user->estaActivo() === $nuevoEstado) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('status', "Cuenta de «{$user->name}» sin cambios.");
        }

        // 'activo' no es mass-assignable: asignación directa y deliberada.
        $user->activo = $nuevoEstado;
        $user->save();

        app(AuditoriaService::class)->registrar(
            $nuevoEstado ? 'usuario.activado' : 'usuario.desactivado',
            'usuario',
            $user->id,
            [
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'activo' => $nuevoEstado,
            ]
        );

        return redirect()
            ->route('admin.usuarios.index')
            ->with(
                'status',
                $nuevoEstado
                    ? "Cuenta de «{$user->name}» activada."
                    : "Cuenta de «{$user->name}» desactivada."
            );
    }
}
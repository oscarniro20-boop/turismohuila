<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $resenas = \App\Models\Resena::with('servicio')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $favoritos = \App\Models\Favorito::with('servicio')
            ->where('user_id', $user->id)
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'favoritosCount' => \App\Models\Favorito::where('user_id', $user->id)->count(),
            'resenas' => $resenas,
            'resenasCount' => $resenas->count(),
            'calificacionPromedio' => $resenas->count() > 0 ? round($resenas->avg('calificacion'), 1) : 0,
            'favoritos' => $favoritos,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // Manejar foto de portada
        if ($request->hasFile('foto_portada')) {
            $archivo = $request->file('foto_portada');
            $nombre = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('portadas'), $nombre);
            $datos['foto_portada'] = 'portadas/' . $nombre;
        }

        $request->user()->fill($datos);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Limpiar foto de portada del usuario
        \App\Utilities\FotoPortada::limpiar($user);

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

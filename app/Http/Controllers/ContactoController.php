<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function mostrar()
    {
        return view('contacto');
    }

    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email',
            'asunto' => 'required|string|max:150',
            'mensaje' => 'required|string|max:2000',
        ]);

        Mail::raw(
            "Nombre: {$datos['nombre']}\nEmail: {$datos['email']}\n\n{$datos['mensaje']}",
            function ($message) use ($datos) {
                $message->to(config('mail.from.address'))
                    ->subject($datos['asunto'])
                    ->replyTo($datos['email'], $datos['nombre']);
            }
        );

        return back()->with('exito', 'Mensaje enviado. Te responderemos pronto.');
    }
}
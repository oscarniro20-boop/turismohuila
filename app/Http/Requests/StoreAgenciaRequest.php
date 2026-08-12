<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->esAdmin();
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:agencias,nombre',
            'municipio' => 'nullable|string|max:255',
            'telefono_contacto' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tours' => 'nullable|array',
            'tours.*' => 'integer|exists:tours,id',
            'paquetes.*.precio' => 'nullable|numeric|min:0',
            'paquetes.*.duracion_dias' => 'nullable|integer|min:1',
            'paquetes.*.cupo_maximo' => 'nullable|integer|min:1',
            'paquetes.*.descripcion' => 'nullable|string',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ];
    }
}
<?php

namespace App\Http\Requests;

use App\Rules\MunicipioDeDepartamento;
use Illuminate\Foundation\Http\FormRequest;

class StoreLugarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre'       => 'required|string|max:255',
            'departamento' => 'required|string|in:'.implode(',', \App\Support\Colombia::departamentos()),
            'municipio'    => ['required', 'string', 'max:255', new MunicipioDeDepartamento],
            'categoria'    => 'required|string|max:100',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
            'descripcion'  => 'required|string',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'imagenes'     => 'nullable|array|max:10',
            'imagenes.*'   => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}
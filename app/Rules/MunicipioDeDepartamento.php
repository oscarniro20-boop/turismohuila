<?php

namespace App\Rules;

use App\Support\Colombia;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida que el municipio pertenezca al departamento enviado en el mismo request.
 * Usa el catálogo oficial DIVIPOLA. No reasigna por aproximación.
 */
class MunicipioDeDepartamento implements ValidationRule
{
    public function __construct(private ?string $departamento = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $departamento = $this->departamento
            ?? request()->input('departamento')
            ?? request()->input(str_replace('municipio', 'departamento', $attribute));

        if (! is_string($departamento) || $departamento === '') {
            $fail('Debe indicar un departamento válido.');

            return;
        }

        if (! Colombia::existe($departamento, (string) $value)) {
            $fail('El municipio no pertenece al departamento seleccionado.');
        }
    }
}

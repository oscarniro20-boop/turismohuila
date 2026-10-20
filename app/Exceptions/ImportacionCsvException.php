<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Error fatal de un archivo de importación CSV (estructura, cabecera,
 * codificación o límites). Es controlada por ImportacionController para
 * mostrar un mensaje claro al administrador sin persistir ningún registro.
 */
class ImportacionCsvException extends RuntimeException
{
}

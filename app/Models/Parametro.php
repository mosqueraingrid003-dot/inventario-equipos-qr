<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametros';

    protected $fillable = ['clave', 'valor', 'descripcion'];

    // Guarda los valores leídos para no consultar la base de datos varias veces
    private static array $memoria = [];

    /** Devuelve el valor numérico de un parámetro (o el valor por defecto). */
    public static function numero(string $clave, int $porDefecto): int
    {
        if (! array_key_exists($clave, self::$memoria)) {
            $valor = self::where('clave', $clave)->value('valor');
            self::$memoria[$clave] = $valor !== null ? (int) $valor : $porDefecto;
        }

        return self::$memoria[$clave];
    }

    public static function olvidar(): void
    {
        self::$memoria = [];
    }
}

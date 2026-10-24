<?php

namespace app\libs\slugger;

final class Slugger
{
    public static function generateSlug(string $texto, int $maxLength = 120): string
    {
        // 1. Pasar a minúscula y quitar acentos/ñ
        $texto = mb_strtolower(trim($texto), 'UTF-8');
        $texto = self::quitarAcentos($texto);

        // 2. Reemplazar todo lo que no sea a-z0-9 por guión
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);

        // 3. Sacar guiones repetidos y de los bordes
        $texto = trim(preg_replace('/-+/', '-', $texto), '-');

        return mb_substr($texto, 0, $maxLength);
    }

    private static function quitarAcentos(string $texto): string
    {
        $mapa = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
            'ü' => 'ñ',
        ];

        return strtr($texto, $mapa);
    }
}
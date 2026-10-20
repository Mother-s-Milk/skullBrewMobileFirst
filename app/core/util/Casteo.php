<?php

namespace app\core\util;

use InvalidArgumentException;
use DateTimeImmutable;

final class Casteo
{
    public static function entero(mixed $valor, string $campo): ?int
    {
        if (is_string($valor)) {
            $valor = trim($valor);
        }
        if ($valor === null || $valor === "") {
            return null;
        }
        $entero = filter_var($valor, FILTER_VALIDATE_INT);
        if ($entero === false) {
            throw new InvalidArgumentException("El campo '$campo' debe ser un número entero.");
        }
        return $entero;
    }

    public static function decimal(mixed $valor, string $campo): ?float
    {
        if (is_string($valor)) {
            $valor = str_replace(",", ".", trim($valor));
        }
        if ($valor === null || $valor === "") {
            return null;
        }
        if (!is_numeric($valor)) {
            throw new InvalidArgumentException("El campo '$campo' debe ser un número decimal.");
        }
        return (float) $valor;
    }

    public static function booleano(mixed $valor, string $campo): ?bool
    {
        if ($valor === null || $valor === "") {
            return null;
        }
        $bool = filter_var($valor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($bool === null) {
            throw new InvalidArgumentException("El campo '$campo' debe ser verdadero o falso.");
        }
        return $bool;
    }

    public static function parseFecha(mixed $valor): ?DateTimeImmutable
    {
        if ($valor === null || $valor === "") {
            return null;
        }
        return $valor instanceof DateTimeImmutable ? $valor : new DateTimeImmutable($valor);
    }
}

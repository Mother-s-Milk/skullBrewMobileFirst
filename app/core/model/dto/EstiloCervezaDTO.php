<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use InvalidArgumentException;

final class EstiloCervezaDTO implements InterfaceDTO
{

    private ?int $id;
    private string $nombre;
    private ?string $descripcion;

    public function __construct(array $data = [])
    {
        $this->setId($data["id"] ?? null);
        $this->setNombre($data["nombre"] ?? "");
        $this->setDescripcion($data["descripcion"] ?? null);
    }

    /***********/
    /* GETTERS */
    /***********/
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    /***********/
    /* SETTERS */
    /***********/
    public function setId(?int $id): void
    {
        // null = categoría todavía no guardada en la base
        $this->id = ($id !== null && $id > 0) ? $id : null;
    }

    public function setNombre(string $nombre): void
    {
        $nombre = trim($nombre);
        if (mb_strlen($nombre) > 100) {
            throw new InvalidArgumentException("El nombre no puede superar los 100 caracteres.");
        }
        $this->nombre = $nombre;
    }

    public function setDescripcion(?string $descripcion): void
    {
        $descripcion = $descripcion === null ? "" : trim($descripcion);
        if (mb_strlen($descripcion) > 500) {
            throw new InvalidArgumentException("La descripción no puede superar los 500 caracteres.");
        }
        // Vacío se guarda como NULL en la base
        $this->descripcion = $descripcion === "" ? null : $descripcion;
    }

    /*********************/
    /* INTERFACE METHODS */
    /*********************/
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "nombre" => $this->getNombre(),
            "descripcion" => $this->getDescripcion()
        ];
    }
}

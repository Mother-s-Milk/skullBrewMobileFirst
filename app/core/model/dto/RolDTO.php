<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use app\core\util\Casteo;

use InvalidArgumentException;

final class RolDTO implements InterfaceDTO
{

    private ?int $id;
    private string $nombre;
    private ?string $descripcion;

    public function __construct(array $data = [])
    {
        $this->setId(Casteo::entero($data["id"] ?? null, "id"));
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
        // null = rol todavía no guardado en la base
        $this->id = ($id !== null && $id > 0) ? $id : null;
    }

    public function setNombre(string $nombre): void
    {
        $nombre = trim($nombre);
        if (mb_strlen($nombre) > 50) {
            throw new InvalidArgumentException("El nombre no puede superar los 50 caracteres.");
        }
        $this->nombre = $nombre;
    }

    public function setDescripcion(?string $descripcion): void
    {
        $descripcion = $descripcion === null ? "" : trim($descripcion);
        if (mb_strlen($descripcion) > 255) {
            throw new InvalidArgumentException("La descripción no puede superar los 255 caracteres.");
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

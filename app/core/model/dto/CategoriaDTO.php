<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use DateTimeImmutable;
use InvalidArgumentException;

final class CategoriaDTO implements InterfaceDTO
{

    private ?int $id;
    private ?int $parent_id;
    private string $nombre;
    private string $slug;
    private ?string $descripcion;
    private int $orden;
    private bool $activa;
    private ?DateTimeImmutable $created_at;
    private ?DateTimeImmutable $updated_at;

    public function __construct(array $data = [])
    {
        $this->setId($data["id"] ?? null);
        $this->setParentId($data["parent_id"] ?? null);
        $this->setNombre($data["nombre"] ?? "");
        $this->setSlug($data["slug"] ?? "");
        $this->setDescripcion($data["descripcion"] ?? null);
        $this->setOrden((int) ($data["orden"] ?? 0));
        $this->setActiva((bool) ($data["activa"] ?? true));

        // Los completa MySQL: solo se cargan cuando el DAO lee de la base
        $this->created_at = $this->parseFecha($data["created_at"] ?? null);
        $this->updated_at = $this->parseFecha($data["updated_at"] ?? null);
    }

    /***********/
    /* GETTERS */
    /***********/
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getOrden(): int
    {
        return $this->orden;
    }

    public function getActiva(): bool
    {
        return $this->activa;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /***********/
    /* SETTERS */
    /***********/
    public function setId(?int $id): void
    {
        // null = categoría todavía no guardada en la base
        $this->id = ($id !== null && $id > 0) ? $id : null;
    }

    public function setParentId(?int $parent_id): void
    {
        // null = categoría raíz (sin padre)
        $this->parent_id = ($parent_id !== null && $parent_id > 0) ? $parent_id : null;
    }

    public function setNombre(string $nombre): void
    {
        $nombre = trim($nombre);
        if (mb_strlen($nombre) > 100) {
            throw new InvalidArgumentException("El nombre no puede superar los 100 caracteres.");
        }
        $this->nombre = $nombre;
    }

    public function setSlug(string $slug): void
    {
        $slug = trim($slug);
        if (mb_strlen($slug) > 120) {
            throw new InvalidArgumentException("El slug no puede superar los 120 caracteres.");
        }
        $this->slug = $slug;
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

    public function setOrden(int $orden): void
    {
        // La columna es SMALLINT UNSIGNED: 0 a 65535
        if ($orden < 0 || $orden > 65535) {
            throw new InvalidArgumentException("El orden debe estar entre 0 y 65535.");
        }
        $this->orden = $orden;
    }

    public function setActiva(bool $activa): void
    {
        $this->activa = $activa;
    }

    /*********************/
    /* INTERFACE METHODS */
    /*********************/
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "parent_id" => $this->getParentId(),
            "nombre" => $this->getNombre(),
            "slug" => $this->getSlug(),
            "descripcion" => $this->getDescripcion(),
            "orden" => $this->getOrden(),
            "activa" => $this->getActiva(),
            "created_at" => $this->getCreatedAt()?->format("d-m-Y H:i:s"),
            "updated_at" => $this->getUpdatedAt()?->format("d-m-Y H:i:s")
        ];
    }

    /***********/
    /* PRIVATE */
    /***********/
    private function parseFecha(mixed $valor): ?DateTimeImmutable
    {
        if ($valor === null || $valor === "") {
            return null;
        }
        return $valor instanceof DateTimeImmutable ? $valor : new DateTimeImmutable($valor);
    }
}

<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use app\core\util\Casteo;

use DateTimeImmutable;
use InvalidArgumentException;

final class ProductoDTO implements InterfaceDTO
{

    private ?int $id;
    private ?int $categoria_id;
    private string $nombre;
    private string $slug;
    private ?string $descripcion;
    private bool $es_alcoholico;
    private bool $destacado;
    private ?string $badge;
    private bool $disponible;
    private bool $activo;
    private ?DateTimeImmutable $created_at;
    private ?DateTimeImmutable $updated_at;

    public function __construct(array $data = [])
    {
        $this->setId(Casteo::entero($data["id"] ?? null, "id"));
        $this->setCategoriaId(Casteo::entero($data["categoria_id"] ?? null, "categoria_id"));
        $this->setNombre($data["nombre"] ?? "");
        $this->setSlug($data["slug"] ?? "");
        $this->setDescripcion($data["descripcion"] ?? null);
        $this->setEsAlcoholico(Casteo::booleano($data["es_alcoholico"] ?? null, "es_alcoholico") ?? false);
        $this->setDestacado(Casteo::booleano($data["destacado"] ?? null, "destacado") ?? false);
        $this->setBadge($data["badge"] ?? null);
        $this->setDisponible(Casteo::booleano($data["disponible"] ?? null, "disponible") ?? true);
        $this->setActivo(Casteo::booleano($data["activo"] ?? null, "activo") ?? true);

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

    public function getCategoriaId(): ?int
    {
        return $this->categoria_id;
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

    public function getEsAlcoholico(): bool
    {
        return $this->es_alcoholico;
    }

    public function getDestacado(): bool
    {
        return $this->destacado;
    }

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function getDisponible(): bool
    {
        return $this->disponible;
    }

    public function getActivo(): bool
    {
        return $this->activo;
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

    public function setCategoriaId(?int $categoria_id): void
    {
        $this->categoria_id = ($categoria_id !== null && $categoria_id > 0) ? $categoria_id : null;
    }

    public function setNombre(string $nombre): void
    {
        $nombre = trim($nombre);
        if (mb_strlen($nombre) > 150) {
            throw new InvalidArgumentException("El nombre no puede superar los 150 caracteres.");
        }
        $this->nombre = $nombre;
    }

    public function setSlug(string $slug): void
    {
        $slug = trim($slug);
        if (mb_strlen($slug) > 170) {
            throw new InvalidArgumentException("El slug no puede superar los 170 caracteres.");
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

    public function setEsAlcoholico(bool $es_alcoholico): void
    {
        $this->es_alcoholico = $es_alcoholico;
    }

    public function setDestacado(bool $destacado): void
    {
        $this->destacado = $destacado;
    }

    public function setBadge(?string $badge): void
    {
        $badge = $badge === null ? "" : trim($badge);
        if (mb_strlen($badge) > 30) {
            throw new InvalidArgumentException("La etiqueta no puede superar los 30 caracteres.");
        }
        // Vacío se guarda como NULL en la base
        $this->badge = $badge === "" ? null : $badge;
    }

    public function setDisponible(bool $disponible): void
    {
        $this->disponible = $disponible;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }

    /*********************/
    /* INTERFACE METHODS */
    /*********************/
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "categoria_id" => $this->getCategoriaId(),
            "nombre" => $this->getNombre(),
            "slug" => $this->getSlug(),
            "descripcion" => $this->getDescripcion(),
            "es_alcoholico" => $this->getEsAlcoholico(),
            "destacado" => $this->getDestacado(),
            "badge" => $this->getBadge(),
            "disponible" => $this->getDisponible(),
            "activo" => $this->getActivo(),
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

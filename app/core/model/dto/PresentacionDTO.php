<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use app\core\util\Casteo;

use DateTimeImmutable;
use InvalidArgumentException;

final class PresentacionDTO implements InterfaceDTO
{
    private ?int $id;
    private ?int $producto_id;
    private string $formato;
    private ?int $volumen_ml;
    private float $precio;
    private ?string $sku;
    private bool $disponible;
    private int $orden;
    private ?DateTimeImmutable $created_at;
    private ?DateTimeImmutable $updated_at;

    public function __construct(array $data = [])
    {
        $this->setId(Casteo::entero($data["id"] ?? null, "id"));
        $this->setProductoId(Casteo::entero($data["producto_id"] ?? null, "producto_id"));
        $this->setFormato($data["formato"] ?? "");
        $this->setVolumenMl(Casteo::entero($data["volumen_ml"] ?? null, "volumen_ml"));
        $this->setPrecio(Casteo::decimal($data["precio"] ?? null, "precio") ?? 0.0);
        $this->setSku($data["sku"] ?? null);
        $this->setDisponible(Casteo::booleano($data["disponible"] ?? true, "disponible"));
        $this->setOrden(Casteo::entero($data["orden"] ?? 0, "orden"));

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

    public function getProductoId(): ?int
    {
        return $this->producto_id;
    }

    public function getFormato(): string
    {
        return $this->formato;
    }

    public function getVolumenMl(): ?int
    {
        return $this->volumen_ml;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getDisponible(): bool
    {
        return $this->disponible;
    }

    public function getOrden(): int
    {
        return $this->orden;
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

    public function setProductoId(?int $producto_id): void
    {
        // null = todavía no se guardó el producto padre
        $this->producto_id = ($producto_id !== null && $producto_id > 0) ? $producto_id : null;
    }

    public function setFormato(string $formato): void
    {
        $formato = trim($formato);
        if (mb_strlen($formato) > 50) {
            throw new InvalidArgumentException("El nombre del formato no puede superar los 50 caracteres.");
        }
        $this->formato = $formato;
    }

    public function setVolumenMl(?int $volumen_ml): void
    {
        $this->volumen_ml = ($volumen_ml !== null && $volumen_ml > 0) ? $volumen_ml : null;
    }

    public function setPrecio(float $precio): void
    {
        if ($precio < 0 || $precio > 99999999.99) {
            throw new InvalidArgumentException("El precio debe estar entre 0 y 99999999.99.");
        }
        $this->precio = round($precio, 2);
    }

    public function setSku(?string $sku): void
    {
        $sku = $sku === null ? "" : trim($sku);
        if (mb_strlen($sku) > 50) {
            throw new InvalidArgumentException("El sku no puede superar los 50 caracteres.");
        }
        $this->sku = $sku === "" ? null : $sku;
    }

    public function setDisponible(bool $disponible): void
    {
        $this->disponible = $disponible;
    }

    public function setOrden(int $orden): void
    {
        // La columna es SMALLINT UNSIGNED: 0 a 65535
        if ($orden < 0 || $orden > 65535) {
            throw new InvalidArgumentException("El orden debe estar entre 0 y 65535.");
        }
        $this->orden = $orden;
    }

    /*********************/
    /* INTERFACE METHODS */
    /*********************/
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "producto_id" => $this->getProductoId(),
            "formato" => $this->getFormato(),
            "volumen_ml" => $this->getVolumenMl(),
            "precio" => $this->getPrecio(),
            "sku" => $this->getSku(),
            "disponible" => $this->getDisponible(),
            "orden" => $this->getOrden(),
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

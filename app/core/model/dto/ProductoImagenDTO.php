<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use app\core\util\Casteo;

use InvalidArgumentException;

final class ProductoImagenDTO implements InterfaceDTO
{

    private ?int $id;
    private ?int $producto_id;
    private string $url;
    private ?string $alt_text;
    private bool $es_principal;
    private ?int $orden;

    public function __construct(array $data = [])
    {
        $this->setId(Casteo::entero($data["id"] ?? null, "id"));
        $this->setProductoId(Casteo::entero($data["producto_id"] ?? null, "producto_id"));
        $this->setUrl($data["url"] ?? "");
        $this->setAltText($data["alt_text"] ?? null);
        $this->setEsPrincipal(Casteo::booleano($data["es_principal"] ?? null, "es_principal") ?? false);
        $this->setOrden(Casteo::entero($data["orden"] ?? null, "orden") ?? 0);
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAltText(): ?string
    {
        return $this->alt_text;
    }

    public function getEsPrincipal(): bool
    {
        return $this->es_principal;
    }

    public function getOrden(): int
    {
        return $this->orden;
    }

    /***********/
    /* SETTERS */
    /***********/
    public function setId(?int $id): void
    {
        $this->id = ($id !== null && $id > 0) ? $id : null;
    }

    public function setProductoId(?int $producto_id): void
    {
        $this->producto_id = ($producto_id !== null && $producto_id > 0) ? $producto_id : null;
    }

    public function setUrl(string $url): void
    {
        $url = trim($url);
        if (mb_strlen($url) > 255) {
            throw new InvalidArgumentException("La url no puede superar los 255 caracteres.");
        }
        $this->url = $url;
    }

    public function setAltText(?string $alt_text): void
    {
        $alt_text = $alt_text === null ? "" : trim($alt_text);
        if (mb_strlen($alt_text) > 150) {
            throw new InvalidArgumentException("El texto alternativo no puede superar los 150 caracteres.");
        }
        $this->alt_text = $alt_text === "" ? null : $alt_text;
    }

    public function setEsPrincipal(bool $es_principal): void
    {
        $this->es_principal = $es_principal;
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
            "url" => $this->getUrl(),
            "alt_text" => $this->getAltText(),
            "es_principal" => $this->getEsPrincipal(),
            "orden" => $this->getOrden()
        ];
    }
}

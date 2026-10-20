<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;

use InvalidArgumentException;

final class CervezaDetalleDTO implements InterfaceDTO
{
    private ?int $producto_id;
    private ?int $estilo_id;
    private ?float $abv; //alcohol
    private ?int $ibu; //amargor
    private ?float $srm; //color
    private ?string $maltas;
    private ?string $lupulos;
    private ?string $notas_cata;
    private ?string $maridaje;

    public function __construct(array $data = [])
    {
        $this->setProductoId($data["producto_id"] ?? null);
        $this->setEstiloId($data["estilo_id"] ?? null);
        $this->setAbv($data["abv"] ?? null);
        $this->setIbu($data["ibu"] ?? null);
        $this->setSrm($data["srm"] ?? null);
        $this->setMaltas($data["maltas"] ?? null);
        $this->setLupulos($data["lupulos"] ?? null);
        $this->setNotasCata($data["notas_cata"] ?? null);
        $this->setMaridaje($data["maridaje"] ?? null);
    }

    /***********/
    /* GETTERS */
    /***********/
    public function getProductoId(): ?int
    {
        return $this->producto_id;
    }

    public function getEstiloId(): ?int
    {
        return $this->estilo_id;
    }

    public function getAbv(): ?float
    {
        return $this->abv;
    }

    public function getIbu(): ?int
    {
        return $this->ibu;
    }

    public function getSrm(): ?float
    {
        return $this->srm;
    }

    public function getMaltas(): ?string
    {
        return $this->maltas;
    }

    public function getLupulos(): ?string
    {
        return $this->lupulos;
    }

    public function getNotasCata(): ?string
    {
        return $this->notas_cata;
    }

    public function getMaridaje(): ?string
    {
        return $this->maridaje;
    }

    /***********/
    /* SETTERS */
    /***********/
    public function setProductoId(?int $producto_id): void
    {
        $this->producto_id = ($producto_id !== null && $producto_id > 0) ? $producto_id : null;
    }

    public function setEstiloId(?int $estilo_id): void
    {
        $this->estilo_id = ($estilo_id !== null && $estilo_id > 0) ? $estilo_id : null;
    }

    public function setAbv(?float $abv): void
    {
        if ($abv !== null) {
            $abv = round($abv, 2);

            if ($abv < 0 || $abv > 99.99) {
                throw new InvalidArgumentException("El ABV debe estar entre 0 y 99.99.");
            }
        }

        $this->abv = $abv;
    }

    public function setIbu(?int $ibu): void
    {
        if ($ibu !== null && ($ibu < 0 || $ibu > 65535)) {
            throw new InvalidArgumentException("El IBU debe estar entre 0 y 65535.");
        }

        $this->ibu = $ibu;
    }

    public function setSrm(?float $srm): void
    {
        if ($srm !== null) {
            $srm = round($srm, 1);

            if ($srm < 0 || $srm > 999.9) {
                throw new InvalidArgumentException("El SRM debe estar entre 0 y 999.9.");
            }
        }

        $this->srm = $srm;
    }

    public function setMaltas(?string $maltas): void
    {
        $maltas = $maltas === null ? "" : trim($maltas);

        if (mb_strlen($maltas) > 255) {
            throw new InvalidArgumentException("El detalle de las maltas no puede superar los 255 caracteres.");
        }

        $this->maltas = $maltas === "" ? null : $maltas;
    }

    public function setLupulos(?string $lupulos): void
    {
        $lupulos = $lupulos === null ? "" : trim($lupulos);

        if (mb_strlen($lupulos) > 255) {
            throw new InvalidArgumentException("El detalle de los lúpulos no puede superar los 255 caracteres.");
        }

        $this->lupulos = $lupulos === "" ? null : $lupulos;
    }

    public function setNotasCata(?string $notas_cata): void
    {
        $notas_cata = $notas_cata === null ? "" : trim($notas_cata);

        if (mb_strlen($notas_cata) > 500) {
            throw new InvalidArgumentException("El detalle de las notas de cata no puede superar los 500 caracteres.");
        }

        $this->notas_cata = $notas_cata === "" ? null : $notas_cata;
    }

    public function setMaridaje(?string $maridaje): void
    {
        $maridaje = $maridaje === null ? "" : trim($maridaje);

        if (mb_strlen($maridaje) > 255) {
            throw new InvalidArgumentException("El detalle de maridaje no puede superar los 255 caracteres.");
        }

        $this->maridaje = $maridaje === "" ? null : $maridaje;
    }

    /*********************/
    /* INTERFACE METHODS */
    /*********************/
    public function toArray(): array
    {
        return [
            "producto_id" => $this->getProductoId(),
            "estilo_id" => $this->getEstiloId(),
            "abv" => $this->getAbv(),
            "ibu" => $this->getIbu(),
            "srm" => $this->getSrm(),
            "maltas" => $this->getMaltas(),
            "lupulos" => $this->getLupulos(),
            "notas_cata" => $this->getNotasCata(),
            "maridaje" => $this->getMaridaje()
        ];
    }
}

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
        $this->setProductoId($this->aEntero($data["producto_id"] ?? null));
        $this->setEstiloId($this->aEntero($data["estilo_id"] ?? null));
        $this->setAbv($this->aDecimal($data["abv"] ?? null));
        $this->setIbu($this->aEntero($data["ibu"] ?? null));
        $this->setSrm($this->aDecimal($data["srm"] ?? null));
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
    public function setProductoId(?int $id): void
    {
        // null = todavía no se guardó el producto padre
        $this->producto_id = ($id !== null && $id > 0) ? $id : null;
    }

    public function setEstiloId(?int $estilo_id): void
    {
        $this->estilo_id = ($estilo_id !== null && $estilo_id > 0) ? $estilo_id : null;
    }

    public function setAbv(?float $abv): void
    {
        if ($abv !== null && ($abv < 0 || $abv > 99.99)) {
            throw new InvalidArgumentException("El alcohol debe estar entre 0 y 99.99.");
        }
        $this->abv = $abv !== null ? round($abv, 2) : null;
    }

    public function setIbu(?int $ibu): void
    {
        // La columna es SMALLINT UNSIGNED: 0 a 65535
        if ($ibu !== null && ($ibu < 0 || $ibu > 65535)) {
            throw new InvalidArgumentException("El IBU debe estar entre 0 y 65535.");
        }
        $this->ibu = $ibu;
    }

    public function setSrm(?float $srm): void
    {
        if ($srm !== null && ($srm < 0 || $srm > 999.9)) {
            throw new InvalidArgumentException("El SRM debe estar entre 0 y 999.9.");
        }
        $this->srm = $srm !== null ? round($srm, 1) : null;
    }

    public function setMaltas(?string $maltas): void
    {
        $maltas = $maltas === null ? "" : trim($maltas);
        if (mb_strlen($maltas) > 255) {
            throw new InvalidArgumentException("La descripción de maltas no puede superar los 255 caracteres.");
        }
        // Vacío se guarda como NULL en la base
        $this->maltas = $maltas === "" ? null : $maltas;
    }

    public function setLupulos(?string $lupulos): void
    {
        $lupulos = $lupulos === null ? "" : trim($lupulos);
        if (mb_strlen($lupulos) > 255) {
            throw new InvalidArgumentException("La descripción de lupulos no puede superar los 255 caracteres.");
        }
        // Vacío se guarda como NULL en la base
        $this->lupulos = $lupulos === "" ? null : $lupulos;
    }

    public function setNotasCata(?string $notas_cata): void
    {
        $notas_cata = $notas_cata === null ? "" : trim($notas_cata);
        if (mb_strlen($notas_cata) > 500) {
            throw new InvalidArgumentException("La descripción de notas cata no puede superar los 500 caracteres.");
        }
        // Vacío se guarda como NULL en la base
        $this->notas_cata = $notas_cata === "" ? null : $notas_cata;
    }

    public function setMaridaje(?string $maridaje): void
    {
        $maridaje = $maridaje === null ? "" : trim($maridaje);
        if (mb_strlen($maridaje) > 255) {
            throw new InvalidArgumentException("La descripción de maridaje no puede superar los 255 caracteres.");
        }
        // Vacío se guarda como NULL en la base
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

    /* PRIVATE */
    private function aEntero(mixed $valor): ?int
    {
        if (is_string($valor)) {
            $valor = trim($valor);
        }
        if ($valor === null || $valor === "") {
            return null;
        }
        $entero = filter_var($valor, FILTER_VALIDATE_INT);
        if ($entero === false) {
            throw new InvalidArgumentException("Se esperaba un número entero.");
        }
        return $entero;
    }

    private function aDecimal(mixed $valor): ?float
    {
        if (is_string($valor)) {
            $valor = str_replace(",", ".", trim($valor));
        }
        if ($valor === null || $valor === "") {
            return null;
        }
        if (!is_numeric($valor)) {
            throw new InvalidArgumentException("Se esperaba un número decimal.");
        }
        return (float) $valor;
    }
}

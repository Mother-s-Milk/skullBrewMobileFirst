<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\CervezaDetalleDTO;

final class CervezaDetalleDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'cervezas_detalle');
    }

    public function save(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        $this->validate($data);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (producto_id, estilo_id, abv, ibu, srm, maltas, lupulos, notas_cata, maridaje)
                VALUES
                    (:producto_id, :estilo_id, :abv, :ibu, :srm, :maltas, :lupulos, :notas_cata, :maridaje)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "producto_id" => $data["producto_id"],
                "estilo_id" => $data["estilo_id"],
                "abv" => $data["abv"],
                "ibu" => $data["ibu"],
                "srm" => $data["srm"],
                "maltas" => $data["maltas"],
                "lupulos" => $data["lupulos"],
                "notas_cata" => $data["notas_cata"],
                "maridaje" => $data["maridaje"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;

            if ($code === 1062) {
                throw new \InvalidArgumentException("El producto {$data["producto_id"]} ya tiene detalle de cerveza.");
            }
            if ($code === 1452) {
                throw new \InvalidArgumentException("El producto o el estilo indicado no existe.");
            }
            throw $e;
        }
    }

    public function load(int $producto_id): InterfaceDTO
    {
        if ($producto_id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        $sql = "
            SELECT
                dc.producto_id,
                dc.estilo_id,
                dc.abv,
                dc.ibu,
                dc.srm,
                dc.maltas,
                dc.lupulos,
                dc.notas_cata,
                dc.maridaje
            FROM 
                {$this->table} dc
            WHERE
                dc.producto_id = :producto_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["producto_id" => $producto_id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$producto_id} no encontrado.");
        }

        return new CervezaDetalleDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        $this->validate($data);

        $check = $this->conn->prepare("SELECT 1 FROM {$this->table} WHERE producto_id = :producto_id");
        $check->execute(["producto_id" => $data["producto_id"]]);

        if ($check->fetchColumn() === false) {
            throw new \InvalidArgumentException("El producto {$data["producto_id"]} no tiene detalle de cerveza.");
        }

        $sql = "
            UPDATE
                {$this->table}
            SET
                estilo_id = :estilo_id,
                abv = :abv,
                ibu = :ibu,
                srm = :srm,
                maltas = :maltas,
                lupulos = :lupulos,
                notas_cata = :notas_cata,
                maridaje = :maridaje
            WHERE
                producto_id = :producto_id
        ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "producto_id" => $data["producto_id"],
                "estilo_id" => $data["estilo_id"],
                "abv" => $data["abv"],
                "ibu" => $data["ibu"],
                "srm" => $data["srm"],
                "maltas" => $data["maltas"],
                "lupulos" => $data["lupulos"],
                "notas_cata" => $data["notas_cata"],
                "maridaje" => $data["maridaje"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;

            if ($code === 1452) {
                throw new \InvalidArgumentException("El estilo indicado no existe.");
            }
            throw $e;
        }
    }

    public function delete(int $producto_id): void
    {
        $sql = "
            DELETE FROM
                {$this->table}
            WHERE
                producto_id = :producto_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["producto_id" => $producto_id]);

        if ($stmt->rowCount() === 0) {
            throw new \Exception("No se encontró el elemento con ID {$producto_id}.");
        }
    }

    /* RETURN ALL ITEMS */
    public function listAll(): array
    {
        $sql = "
                SELECT
                    *
                FROM
                    {$this->table}
                ORDER BY
                    producto_id ASC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new CervezaDetalleDTO($row), $list);
    }

    /* PRIVATE */
    private function validate(array $data): void
    {
        if ($data["producto_id"] === null) {
            throw new \InvalidArgumentException("El producto_id es obligatorio.");
        }
    }
}

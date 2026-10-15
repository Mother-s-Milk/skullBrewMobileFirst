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
        $sql = "
                INSERT INTO
                    {$this->table}
                    (producto_id, estilo_id, abv, ibu, srm, maltas, lupulos, notas_cata, maridaje)
                VALUES
                    (:producto_id, :estilo_id, :abv, :ibu, :srm, :maltas, :lupulos, :notas_cata, :maridaje)
            ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

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
    }

    public function load(int $producto_id): InterfaceDTO
    {
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
        $data = $object->toArray();

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
    public function list(): array
    {
        $sql = "
                SELECT
                    *
                FROM
                    {$this->table}
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

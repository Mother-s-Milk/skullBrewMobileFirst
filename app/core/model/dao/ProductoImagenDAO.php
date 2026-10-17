<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\ProductoImagenDTO;

final class ProductoImagenDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'producto_imagenes');
    }

    public function save(InterfaceDTO $object): void
    {
        $sql = "
                INSERT INTO
                    {$this->table}
                    (producto_id, url, alt_text, es_principal, orden)
                VALUES
                    (:producto_id, :url, :alt_text, :es_principal, :orden)
            ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        $stmt->execute([
            "producto_id" => $data["producto_id"],
            "url" => $data["url"],
            "alt_text" => $data["alt_text"],
            "es_principal" => (int) $data["es_principal"],
            "orden" => $data["orden"]
        ]);

        $object->setId((int) $this->conn->lastInsertId());
    }

    public function load(int $id): InterfaceDTO
    {
        $sql = "
            SELECT
                pi.id,
                pi.producto_id,
                pi.url,
                pi.alt_text,
                pi.es_principal,
                pi.orden
            FROM 
                {$this->table} pi
            WHERE
                pi.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new ProductoImagenDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $sql = "
            UPDATE
                {$this->table}
            SET
                producto_id = :producto_id,
                url = :url,
                alt_text = :alt_text,
                es_principal = :es_princiapal,
                orden = :orden
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar una imagen sin ID.");
        }

        $stmt->execute([
            "id" => $data["id"],
            "producto_id" => $data["producto_id"],
            "url" => $data["url"],
            "alt_text" => $data["alt_text"],
            "es_principal" => (int) $data["es_principal"],
            "orden" => $data["orden"]
        ]);
    }

    public function delete(int $id): void
    {
        $sql = "
            DELETE FROM
                {$this->table}
            WHERE
                id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        if ($stmt->rowCount() === 0) {
            throw new \Exception("No se encontró el elemento con ID {$id}.");
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

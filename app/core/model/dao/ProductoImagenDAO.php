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
        $data = $object->toArray();
        $this->validate($data);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (producto_id, url, alt_text, es_principal, orden)
                VALUES
                    (:producto_id, :url, :alt_text, :es_principal, :orden)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "producto_id" => $data["producto_id"],
                "url" => $data["url"],
                "alt_text" => $data["alt_text"],
                "es_principal" => (int) $data["es_principal"],
                "orden" => $data["orden"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;
            if ($code === 1452) {
                throw new \InvalidArgumentException("El producto indicado no existe.");
            }
            throw $e;
        }

        $object->setId((int) $this->conn->lastInsertId());
    }

    public function load(int $id): InterfaceDTO
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

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
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar una imagen sin ID.");
        }

        $this->validate($data);

        $sql = "
            UPDATE
                {$this->table}
            SET
                producto_id = :producto_id,
                url = :url,
                alt_text = :alt_text,
                es_principal = :es_principal,
                orden = :orden
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "id" => $data["id"],
                "producto_id" => $data["producto_id"],
                "url" => $data["url"],
                "alt_text" => $data["alt_text"],
                "es_principal" => (int) $data["es_principal"],
                "orden" => $data["orden"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;

            if ($code === 1452) {
                throw new \InvalidArgumentException("El producto indicado no existe.");
            }
            throw $e;
        }
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
    public function listAll(): array
    {
        $sql = "
                SELECT
                    *
                FROM
                    {$this->table}
                ORDER BY
                    producto_id ASC, orden ASC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new ProductoImagenDTO($row), $list);
    }

    private function validate(array $data): void
    {
        if ($data["producto_id"] === null) {
            throw new \InvalidArgumentException("La imagen debe estar asignada a un producto.");
        }

        if ($data["url"] === "") {
            throw new \InvalidArgumentException("La url no puede estar vacía.");
        }
    }
}

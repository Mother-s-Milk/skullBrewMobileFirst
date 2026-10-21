<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\PresentacionDTO;

final class PresentacionDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'presentaciones');
    }

    public function save(InterfaceDTO $object): void
    {
        $data = $object->toArray();
        $this->validate($data);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (producto_id, formato, volumen_ml, precio, sku, disponible, orden)
                VALUES
                    (:producto_id, :formato, :volumen_ml, :precio, :sku, :disponible, :orden)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "producto_id" => $data["producto_id"],
                "formato" => $data["formato"],
                "volumen_ml" => $data["volumen_ml"],
                "precio" => $data["precio"],
                "sku" => $data["sku"],
                "disponible" => $data["disponible"],
                "orden" => $data["orden"]
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El sku '{$data["sku"]}' ya existe.");
            }
            if (($e->errorInfo[1] ?? null) === 1452) {
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
                p.id,
                p.producto_id,
                p.formato,
                p.volumen_ml,
                p.precio,
                p.sku,
                p.disponible,
                p.orden,
                p.created_at,
                p.updated_at
            FROM 
                {$this->table} p
            WHERE
                p.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new PresentacionDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar una presentación sin ID.");
        }

        if (!$this->exists($data["id"])) {
            throw new \InvalidArgumentException("La presentación con ID {$data["id"]} no existe.");
        }

        $this->validate($data);

        $sql = "
            UPDATE
                {$this->table}
            SET
                producto_id = :producto_id,
                formato = :formato,
                volumen_ml = :volumen_ml,
                precio = :precio,
                sku = :sku,
                disponible = :disponible,
                orden = :orden
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "id" => $data["id"],
                "producto_id" => $data["producto_id"],
                "formato" => $data["formato"],
                "volumen_ml" => $data["volumen_ml"],
                "precio" => $data["precio"],
                "sku" => $data["sku"],
                "disponible" => $data["disponible"],
                "orden" => $data["orden"]
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El sku '{$data["sku"]}' ya existe.");
            }
            if (($e->errorInfo[1] ?? null) === 1452) {
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

        return array_map(fn($row) => new PresentacionDTO($row), $list);
    }

    /* PRIVATE */
    private function validate(array $data): void
    {
        if ($data["producto_id"] === null) {
            throw new \InvalidArgumentException("La presentación debe estar asignada a un producto.");
        }

        if ($data["formato"] === "") {
            throw new \InvalidArgumentException("Debe asignar un nombre al formato.");
        }
    }

    private function exists(int $id): bool
    {
        $sql = "
            SELECT 1 FROM
                {$this->table}
            WHERE
                id = :id
            LIMIT
                1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        return $stmt->fetchColumn() !== false;
    }
}

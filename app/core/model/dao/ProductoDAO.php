<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\ProductoDTO;

final class ProductoDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'productos');
    }

    public function save(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        $this->validate($data);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (categoria_id, nombre, slug, descripcion, es_alcoholico, destacado, badge, disponible, activo)
                VALUES
                    (:categoria_id, :nombre, :slug, :descripcion, :es_alcoholico, :destacado, :badge, :disponible, :activo)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "categoria_id" => $data["categoria_id"],
                "nombre" => $data["nombre"],
                "slug" => $data["slug"],
                "descripcion" => $data["descripcion"],
                "es_alcoholico" => $data["es_alcoholico"],
                "destacado" => $data["destacado"],
                "badge" => $data["badge"],
                "disponible" => $data["disponible"],
                "activo" => $data["activo"]
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El slug '{$data["slug"]}' ya existe.");
            }
            if (($e->errorInfo[1] ?? null) === 1452) {
                throw new \InvalidArgumentException("La categoría indicada no existe.");
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
                p.categoria_id,
                p.nombre,
                p.slug,
                p.descripcion,
                p.es_alcoholico,
                p.destacado,
                p.badge,
                p.disponible,
                p.activo,
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

        return new ProductoDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar un producto sin ID.");
        }

        if (!$this->exists($data["id"])) {
            throw new \InvalidArgumentException("La presentación con ID {$data["id"]} no existe.");
        }
        
        $this->validate($data);

        $sql = "
            UPDATE
                {$this->table}
            SET
                categoria_id = :categoria_id,
                nombre = :nombre,
                slug = :slug,
                descripcion = :descripcion,
                es_alcoholico = :es_alcoholico,
                destacado = :destacado,
                badge = :badge,
                disponible = :disponible,
                activo = :activo
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "id" => $data["id"],
                "categoria_id" => $data["categoria_id"],
                "nombre" => $data["nombre"],
                "slug" => $data["slug"],
                "descripcion" => $data["descripcion"],
                "es_alcoholico" => (int) $data["es_alcoholico"],
                "destacado" => (int) $data["destacado"],
                "badge" => $data["badge"],
                "disponible" => (int) $data["disponible"],
                "activo" => (int) $data["activo"]
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El slug '{$data["slug"]}' ya existe.");
            }
            if (($e->errorInfo[1] ?? null) === 1452) {
                throw new \InvalidArgumentException("La categoría indicada no existe.");
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
                    nombre ASC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => new ProductoDTO($row), $list);
    }

    /* PRIVATE */
    private function validate(array $data): void
    {
        if ($data["categoria_id"] === null) {
            throw new \InvalidArgumentException("Un producto debe tener una categoría.");
        }

        if ($data["nombre"] === "") {
            throw new \InvalidArgumentException("El nombre no puede estar vacío.");
        }

        if ($data["slug"] === "") {
            throw new \InvalidArgumentException("El slug no puede estar vacío.");
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

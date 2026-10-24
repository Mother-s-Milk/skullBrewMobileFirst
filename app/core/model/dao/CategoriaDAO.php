<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\CategoriaDTO;

final class CategoriaDAO extends DAO implements InterfaceDAO
{
    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'categorias');
    }

    public function save(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        $this->validate($data);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (parent_id, nombre, slug, descripcion, orden, activa)
                VALUES
                    (:parent_id, :nombre, :slug, :descripcion, :orden, :activa)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "parent_id" => $data["parent_id"],
                "nombre" => $data["nombre"],
                "slug" => $data["slug"],
                "descripcion" => $data["descripcion"],
                "orden" => $data["orden"],
                "activa" => $data["activa"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;

            if ($code === 1062) {
                throw new \InvalidArgumentException("El slug '{$data["slug"]}' ya existe.");
            }
            if ($code === 1452) {
                throw new \InvalidArgumentException("La categoría padre no existe.");
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
                c.id,
                c.parent_id,
                c.nombre,
                c.slug,
                c.descripcion,
                c.orden,
                c.activa,
                c.created_at,
                c.updated_at
            FROM 
                {$this->table} c
            WHERE
                c.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new CategoriaDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar una categoría sin ID.");
        }

        $this->validate($data);

        if (!$this->exists($data["id"])) {
            throw new \InvalidArgumentException("La categoría con ID {$data["id"]} no existe.");
        }


        $sql = "
            UPDATE
                {$this->table}
            SET
                parent_id = :parent_id,
                nombre = :nombre,
                slug = :slug,
                descripcion = :descripcion,
                orden = :orden,
                activa = :activa
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "id" => $data["id"],
                "parent_id" => $data["parent_id"],
                "nombre" => $data["nombre"],
                "slug" => $data["slug"],
                "descripcion" => $data["descripcion"],
                "orden" => $data["orden"],
                "activa" => $data["activa"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;

            if ($code === 1062) {
                throw new \InvalidArgumentException("El slug '{$data["slug"]}' ya existe.");
            }
            if ($code === 1452) {
                throw new \InvalidArgumentException("La categoría padre no existe.");
            }

            throw $e;
        }
    }

    public function delete(int $id): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        // La FK de parent_id es SET NULL: sin este chequeo, las subcategorías quedarían como principales.
        $hijos = $this->countSubcategorias($id);
        if ($hijos > 0) {
            throw new \InvalidArgumentException(
                "La categoría tiene {$hijos} subcategoría(s). Eliminalas o movelas antes de borrarla."
            );
        }

        $productos = $this->countProductos($id);
        if ($productos > 0) {
            throw new \InvalidArgumentException(
                "La categoría tiene {$productos} producto(s). Pasalos a otra categoría antes de borrarla."
            );
        }


        $sql = "
            DELETE FROM
                {$this->table}
            WHERE
                id = :id
        ";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute(["id" => $id]);
        } catch (\PDOException $e) {
            // Respaldo ante una carrera: alguien cargó un producto después del conteo.
            if (($e->errorInfo[1] ?? null) === 1451) {
                throw new \InvalidArgumentException("La categoría tiene productos asociados.");
            }
            throw $e;
        }


        if ($stmt->rowCount() === 0) {
            throw new \Exception("No se encontró el elemento con ID {$id}.");
        }
    }

    /* RETURN ALL ITEMS */
    public function listAll(): array
    {
        return $this->fetchList();
    }

    /* RETURN ACTIVE ITEMS (selects y catálogo) */
    public function listActive(): array
    {
        return $this->fetchList("WHERE activa = 1");
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE slug = :slug";
        $params = ["slug" => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id <> :id";
            $params["id"] = $excludeId;
        }

        $stmt = $this->conn->prepare($sql . " LIMIT 1");
        $stmt->execute($params);

        return $stmt->fetchColumn() !== false;
    }

    public function countSubcategorias(int $id): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE parent_id = :id");
        $stmt->execute(["id" => $id]);

        return (int) $stmt->fetchColumn();
    }

    public function countProductos(int $id): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id");
        $stmt->execute(["id" => $id]);

        return (int) $stmt->fetchColumn();
    }

    /* PRIVATE */
    private function validate(array $data): void
    {
        if ($data["parent_id"] !== null && $data["parent_id"] === $data["id"]) {
            throw new \InvalidArgumentException("Una categoría no puede ser su propio padre.");
        }

        if ($data["nombre"] === "") {
            throw new \InvalidArgumentException("El nombre no puede estar vacío.");
        }

        // También rechaza el slug vacío
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $data["slug"])) {
            throw new \InvalidArgumentException("El slug solo puede contener minúsculas, números y guiones.");
        }
    }

    private function exists(int $id): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);

        return $stmt->fetchColumn() !== false;
    }

    /* $where es siempre texto fijo del propio DAO, nunca input externo. */
    private function fetchList(string $where = ""): array
    {
        $sql = "
            SELECT
                id, parent_id, nombre, slug, descripcion, orden, activa, created_at, updated_at
            FROM
                {$this->table}
            {$where}
            ORDER BY
                orden ASC, nombre ASC
        ";

        $stmt = $this->conn->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new CategoriaDTO($row), $rows);
    }
}

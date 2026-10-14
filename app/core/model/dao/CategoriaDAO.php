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
        $sql = "
                INSERT INTO
                    {$this->table}
                    (parent_id, nombre, slug, descripcion, orden, activa)
                VALUES
                    (:parent_id, :nombre, :slug, :descripcion, :orden, :activa)
            ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        try {
            $stmt->execute([
                "parent_id" => $data["parent_id"],
                "nombre" => $data["nombre"],
                "slug" => $data["slug"],
                "descripcion" => $data["descripcion"],
                "orden" => $data["orden"],
                "activa" => (int) $data["activa"]
            ]);
        } catch (\PDOException $e) {
            $this->lanzarErrorAmigable($e);
        }

        $object->setId((int) $this->conn->lastInsertId());
    }

    public function load(int $id): InterfaceDTO
    {
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
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar una categoría sin ID.");
        }

        if ($data["parent_id"] === $data["id"]) {
            throw new \InvalidArgumentException("Una categoría no puede ser su propio padre.");
        }

        try {
            $stmt->execute([
                "id" => $data["id"],
                "parent_id" => $data["parent_id"],
                "nombre" => $data["nombre"],
                "slug" => $data["slug"],
                "descripcion" => $data["descripcion"],
                "orden" => $data["orden"],
                "activa" => (int) $data["activa"]
            ]);
        } catch (\PDOException $e) {
            $this->lanzarErrorAmigable($e);
        }
    }

    public function delete(int $id): void
    {
        /* opcional: verificar si la categoría tiene subcategorías antes de eliminarla */
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE parent_id = :id"
        );
        $check->execute(["id" => $id]);
        if ((int) $check->fetchColumn() > 0) {
            throw new \InvalidArgumentException(
                "La categoría tiene subcategorías. Eliminalas o reasignalas antes de borrarla."
            );
        }
        /* fin */

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
            $this->lanzarErrorAmigable($e);
        }

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
                ORDER BY
                    orden ASC, nombre ASC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /* RETURNS LIST WITH FILTERS APPLIED, IF ANY EXIST */
    public function listPaginated(array $params): array
    {
        $currentPage  = max(1, (int) ($params['page'] ?? 1));
        $itemsPerPage = min(100, max(1, (int) ($params['limit'] ?? 15)));
        $filters = $params['filters'] ?? [];

        $offset = ($currentPage - 1) * $itemsPerPage;

        $where = [];
        $queryParams = [];

        $whereClause = '';
        if (!empty($where)) {
            $whereClause = 'WHERE ' . implode(' AND ', $where);
        }

        $countSql = "
                SELECT
                    COUNT(*)
                FROM
                    {$this->table} AS c
                {$whereClause}
            ";
        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute($queryParams);
        $totalCategorias = (int) $countStmt->fetchColumn();

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
                    {$this->table} AS c
                {$whereClause}
                ORDER BY
                    c.orden ASC, c.nombre ASC
                LIMIT :limit OFFSET :offset
            ";

        $stmt = $this->conn->prepare($sql);
        foreach ($queryParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $categorias = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'categorias' => $categorias,
            'totalCategorias' => $totalCategorias
        ];
    }

    /***********/
    /* PRIVATE */
    /***********/
    private function lanzarErrorAmigable(\PDOException $e): never  // never requiere PHP 8.1+ (o cambiar a void)
    {
        match ($e->errorInfo[1] ?? null) {
            1062 => throw new \InvalidArgumentException("Ya existe una categoría con ese slug."),
            1452 => throw new \InvalidArgumentException("La categoría padre no existe."),
            1451 => throw new \InvalidArgumentException("No se puede eliminar: la categoría tiene productos asociados. Desactivala en su lugar."),
            default => throw $e,
        };
    }
}

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
        $sql = "
                INSERT INTO
                    {$this->table}
                    (categoria_id, nombre, slug, descripcion, es_alcoholico, destacado, badge, disponible, activo)
                VALUES
                    (:categoria_id, :nombre, :slug, :descripcion, :es_alcoholico, :destacado, :badge, :disponible, :activo)
            ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        try {
            $stmt->execute([
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
            $this->lanzarErrorAmigable($e);
        }

        $object->setId((int) $this->conn->lastInsertId());
    }

    public function load(int $id): InterfaceDTO
    {
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
                p,disponible,
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
        $sql = "
            UPDATE
                {$this->table}
            SET
                categoria_id = :categoria_id,
                nombre = :nombre,
                slug = :slug,
                descripcion = :descripcion,
                es_alcoholico = :es_alcoholico,,
                destacado = :destacado,
                badge = :badge,
                disponible = :disponible,
                activo = :activo
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar un producto sin ID.");
        }

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
            $this->lanzarErrorAmigable($e);
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
                    {$this->table} AS p
                {$whereClause}
            ";
        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute($queryParams);
        $totalProductos = (int) $countStmt->fetchColumn();

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
                    {$this->table} AS p
                {$whereClause}
                ORDER BY
                    p.nombre ASC
                LIMIT :limit OFFSET :offset
            ";

        $stmt = $this->conn->prepare($sql);
        foreach ($queryParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'productos' => $productos,
            'totalProductos' => $totalProductos
        ];
    }

    /***********/
    /* PRIVATE */
    /***********/
    private function lanzarErrorAmigable(\PDOException $e): never  // never requiere PHP 8.1+ (o cambiar a void)
    {
        match ($e->errorInfo[1] ?? null) {
            1062 => throw new \InvalidArgumentException("Ya existe un producto con ese slug."),
            default => throw $e,
        };
    }
}

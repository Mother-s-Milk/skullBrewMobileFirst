<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\EstiloCervezaDTO;

final class EstiloCervezaDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'estilos_cerveza');
    }

    public function save(InterfaceDTO $object): void
    {
        $sql = "
                INSERT INTO
                    {$this->table}
                    (nombre, descripcion)
                VALUES
                    (:nombre, :descripcion)
            ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        try {
            $stmt->execute([
                "nombre" => $data["nombre"],
                "descripcion" => $data["descripcion"]
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
                ec.id,
                ec.nombre,
                ec.descripcion
            FROM 
                {$this->table} ec
            WHERE
                ec.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new EstiloCervezaDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $sql = "
            UPDATE
                {$this->table}
            SET
                nombre = :nombre,
                descripcion = :descripcion
            WHERE
                id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar un estilo sin ID.");
        }

        try {
            $stmt->execute([
                "id" => $data["id"],
                "nombre" => $data["nombre"],
                "descripcion" => $data["descripcion"]
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
                ORDER BY
                    nombre ASC
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
                    {$this->table} AS ec
                {$whereClause}
            ";
        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute($queryParams);
        $totalEstilosCerveza = (int) $countStmt->fetchColumn();

        $sql = "
                SELECT
                    ec.id,
                    ec.nombre,
                    ec.descripcion
                FROM
                    {$this->table} AS ec
                {$whereClause}
                ORDER BY
                    ec.nombre ASC
                LIMIT :limit OFFSET :offset
            ";

        $stmt = $this->conn->prepare($sql);
        foreach ($queryParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $estilosCerveza = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'estilosCerveza' => $estilosCerveza,
            'totalEstilosCerveza' => $totalEstilosCerveza
        ];
    }

    /***********/
    /* PRIVATE */
    /***********/
    private function lanzarErrorAmigable(\PDOException $e): never  // never requiere PHP 8.1+ (o cambiar a void)
    {
        match ($e->errorInfo[1] ?? null) {
            1451 => throw new \InvalidArgumentException("No se puede eliminar: la categoría tiene productos asociados. Desactivala en su lugar."),
            default => throw $e,
        };
    }
}

<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\RolDTO;

final class RolDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'roles');
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
                r.id,
                r.nombre,
                r.descripcion
            FROM 
                {$this->table} r
            WHERE
                r.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new RolDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $sql = "
            UPDATE
                {$this->table}
            SET
                nombre = :nombre,
                descripcion = :descripcion
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar un rol sin ID.");
        }

        try {
            $stmt->execute([
                "id" => $data["id"],
                "nombre" => $data["nombre"],
                "descripcion" => $data["descripcion"],
            ]);
        } catch (\PDOException $e) {
            $this->lanzarErrorAmigable($e);
        }
    }

    public function delete(int $id): void
    {
        /* opcional: verificar si el rol tiene usuarios antes de eliminarlo */
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE rol_id = :id"
        );
        $check->execute(["id" => $id]);
        if ((int) $check->fetchColumn() > 0) {
            throw new \InvalidArgumentException(
                "El rol tiene usuarios. Eliminalos o reasignalos antes de borrarlo."
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
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /***********/
    /* PRIVATE */
    /***********/
    private function lanzarErrorAmigable(\PDOException $e): never  // never requiere PHP 8.1+ (o cambiar a void)
    {
        match ($e->errorInfo[1] ?? null) {
            1451 => throw new \InvalidArgumentException("No se puede eliminar: el rol tiene productos usuarios."),
            default => throw $e,
        };
    }
}

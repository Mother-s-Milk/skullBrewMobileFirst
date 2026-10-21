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
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El slug '{$data["slug"]}' ya existe.");
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
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El slug '{$data["slug"]}' ya existe.");
            }
            throw $e;
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

        /* opcional: verificar si la categoría tiene productos antes de eliminarla */
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM productos WHERE categoria_id = :id"
        );
        $check->execute(["id" => $id]);
        if ((int) $check->fetchColumn() > 0) {
            throw new \InvalidArgumentException(
                "La categoría tiene productos asociados. Eliminalos o reasignalos antes de borrarla."
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
                    orden ASC, nombre ASC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new CategoriaDTO($row), $list);
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

        if ($data["slug"] === "") {
            throw new \InvalidArgumentException("El slug no puede estar vacío.");
        }
    }
}

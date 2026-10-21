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
        $data = $object->toArray();
        $this->validate($data);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (nombre, descripcion)
                VALUES
                    (:nombre, :descripcion)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "nombre" => $data["nombre"],
                "descripcion" => $data["descripcion"]
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El estilo '{$data["nombre"]}' ya existe.");
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
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar un estilo sin ID.");
        }

        $this->validate($data);

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

        try {
            $stmt->execute([
                "id" => $data["id"],
                "nombre" => $data["nombre"],
                "descripcion" => $data["descripcion"]
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El estilo '{$data["nombre"]}' ya existe.");
            }
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        /* opcional: verificar si el estilo tiene relación con cervezas detalles antes de eliminarlo */
        $check = $this->conn->prepare(
            "SELECT COUNT(*) FROM cervezas_detalle WHERE estilo_id = :id"
        );
        $check->execute(["id" => $id]);
        if ((int) $check->fetchColumn() > 0) {
            throw new \InvalidArgumentException(
                "El estilo tiene asociados detalles de cervezas. Eliminalos o reasignalos antes de borrarlo."
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
                    nombre ASC
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new EstiloCervezaDTO($row), $list);
    }

    /* PRIVATE */
    private function validate(array $data): void
    {
        if ($data["nombre"] === "") {
            throw new \InvalidArgumentException("El nombre no puede estar vacío.");
        }
    }
}

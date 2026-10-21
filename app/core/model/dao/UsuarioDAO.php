<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\UsuarioDTO;

final class UsuarioDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'usuarios');
    }

    public function save(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        if ($data["id"] !== null) {
            throw new \InvalidArgumentException("El usuario ya fue guardado.");
        }

        $this->validate($data);

        if ($data["rol_id"] === null) {
            throw new \InvalidArgumentException("Un usuario debe tener un rol asignado.");
        }
        $this->validatePassword($data["password_hash"]);

        $hash = password_hash($data["password_hash"], PASSWORD_DEFAULT);

        $sql = "
                INSERT INTO
                    {$this->table}
                    (rol_id, nombre, email, password_hash, activo)
                VALUES
                    (:rol_id, :nombre, :email, :password_hash, :activo)
            ";

        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "rol_id" => $data["rol_id"],
                "nombre" => $data["nombre"],
                "email" => $data["email"],
                "password_hash" => $hash,
                "activo" => $data["activo"]
            ]);
        } catch (\PDOException $e) {
            $code = $e->errorInfo[1] ?? null;

            if ($code === 1062) {
                throw new \InvalidArgumentException("El email '{$data["email"]}' ya se encuentra registrado.");
            }
            if ($code === 1452) {
                throw new \InvalidArgumentException("El rol indicado no existe.");
            }

            throw $e;
        }

        $object->setId((int) $this->conn->lastInsertId());
        $object->setPasswordHash($hash);
    }

    public function load(int $id): InterfaceDTO
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        $sql = "
            SELECT
                u.id,
                u.rol_id,
                u.nombre,
                u.email,
                u.password_hash,
                u.activo,
                u.ultimo_acceso,
                u.created_at,
                u.updated_at
            FROM 
                {$this->table} u
            WHERE
                u.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new UsuarioDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $data = $object->toArray();

        if ($data["id"] === null) {
            throw new \InvalidArgumentException("No se puede actualizar un usuario sin ID.");
        }

        $this->validate($data);

        $sql = "
            UPDATE
                {$this->table}
            SET
                nombre = :nombre,
                email = :email
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                "id" => $data["id"],
                "nombre" => $data["nombre"],
                "email" => $data["email"],
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \InvalidArgumentException("El email '{$data["email"]}' ya se encuentra registrado.");
            }
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

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

        return array_map(fn($row) => new UsuarioDTO($row), $list);
    }

    public function cambiarRol(int $id, int $rol_id): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        if ($rol_id <= 0) {
            throw new \InvalidArgumentException("Rol inválido.");
        }

        if (!$this->exists($id)) {
            throw new \InvalidArgumentException("El usuario con ID {$id} no existe.");
        }

        $stmt = $this->conn->prepare("
            UPDATE
                {$this->table}
            SET
                rol_id = :rol_id
            WHERE id = :id
        ");

        try {
            $stmt->execute([
                "id" => $id,
                "rol_id" => $rol_id
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1452) {
                throw new \InvalidArgumentException("El rol indicado no existe.");
            }
            throw $e;
        }
    }

    public function activar(int $id): void
    {
        $this->cambiarEstado($id, 1);
    }

    public function desactivar(int $id): void
    {
        $this->cambiarEstado($id, 0);
    }

    public function cambiarPassword(int $id, string $passwordActual, string $passwordNueva): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        $stmt = $this->conn->prepare("SELECT password_hash FROM {$this->table} WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $hashActual = $stmt->fetchColumn();

        if ($hashActual === false) {
            throw new \InvalidArgumentException("El usuario con ID {$id} no existe.");
        }

        if (!password_verify($passwordActual, $hashActual)) {
            throw new \InvalidArgumentException("La contraseña actual es incorrecta.");
        }

        $this->validatePassword($passwordNueva);

        $stmt = $this->conn->prepare("
            UPDATE
                {$this->table}
            SET
                password_hash = :password_hash
            WHERE id = :id
        ");

        $stmt->execute([
            "id" => $id,
            "password_hash" => password_hash($passwordNueva, PASSWORD_DEFAULT)
        ]);
    }

    /* PRIVATE */
    private function validate(array $data): void
    {
        if ($data["nombre"] === "") {
            throw new \InvalidArgumentException("El nombre no puede estar vacío.");
        }

        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("El email no es válido.");
        }
    }

    private function validatePassword(?string $password): void
    {
        if ($password === null) {
            throw new \InvalidArgumentException("La contraseña no puede estar vacía.");
        }

        // bcrypt trabaja con bytes, por eso strlen y no mb_strlen
        if (strlen($password) < 4 || strlen($password) > 72) {
            throw new \InvalidArgumentException("La contraseña debe tener entre 8 y 72 caracteres.");
        }
    }

    private function cambiarEstado(int $id, int $activo): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        if (!$this->exists($id)) {
            throw new \InvalidArgumentException("El usuario con ID {$id} no existe.");
        }

        $stmt = $this->conn->prepare("
            UPDATE
                {$this->table}
            SET
                activo = :activo
            WHERE id = :id
        ");

        $stmt->execute([
            "id" => $id,
            "activo" => $activo
        ]);
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

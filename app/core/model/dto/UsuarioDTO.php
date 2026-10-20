<?php

namespace app\core\model\dto;

use app\core\model\base\InterfaceDTO;
use app\core\util\Casteo;

use DateTimeImmutable;
use InvalidArgumentException;

final class UsuarioDTO implements InterfaceDTO
{

    private ?int $id;
    private ?int $rol_id;
    private string $nombre;
    private string $email;
    private ?string $password_hash;
    private bool $activo;
    private ?DateTimeImmutable $ultimo_acceso;
    private ?DateTimeImmutable $created_at;
    private ?DateTimeImmutable $updated_at;

    public function __construct(array $data = [])
    {
        $this->setId(Casteo::entero($data["id"] ?? null, "id"));
        $this->setRolId(Casteo::entero($data["rol_id"] ?? null, "rol_id"));
        $this->setNombre($data["nombre"] ?? "");
        $this->setEmail($data["email"] ?? "");
        $this->setPasswordHash($data["password_hash"] ?? null);
        $this->setActivo(Casteo::booleano($data["activo"] ?? null, "activo") ?? true);

        // Los completa MySQL: solo se cargan cuando el DAO lee de la base
        $this->ultimo_acceso = $this->parseFecha($data["ultimo_acceso"] ?? null);
        $this->created_at = $this->parseFecha($data["created_at"] ?? null);
        $this->updated_at = $this->parseFecha($data["updated_at"] ?? null);
    }

    /***********/
    /* GETTERS */
    /***********/
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRolId(): ?int
    {
        return $this->rol_id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }

    public function getUltimoAcceso(): ?DateTimeImmutable
    {
        return $this->ultimo_acceso;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /***********/
    /* SETTERS */
    /***********/
    public function setId(?int $id): void
    {
        // null = usuario todavía no guardada en la base
        $this->id = ($id !== null && $id > 0) ? $id : null;
    }

    public function setRolId(?int $rol_id): void
    {
        $this->rol_id = ($rol_id !== null && $rol_id > 0) ? $rol_id : null;
    }

    public function setNombre(string $nombre): void
    {
        $nombre = trim($nombre);
        if (mb_strlen($nombre) > 100) {
            throw new InvalidArgumentException("El nombre no puede superar los 100 caracteres.");
        }
        $this->nombre = $nombre;
    }

    public function setEmail(string $email): void
    {
        $email = trim($email);
        if (mb_strlen($email) > 150) {
            throw new InvalidArgumentException("El email no puede superar los 150 caracteres.");
        }
        $this->email = $email;
    }

    public function setPasswordHash(?string $password_hash): void
    {
        $password_hash = $password_hash === null ? "" : trim($password_hash);
        if (mb_strlen($password_hash) > 255) {
            throw new InvalidArgumentException("La contraseña no puede superar los 255 caracteres.");
        }
        $this->password_hash = $password_hash === "" ? null : $password_hash;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }

    /*********************/
    /* INTERFACE METHODS */
    /*********************/
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "rol_id" => $this->getRolId(),
            "nombre" => $this->getNombre(),
            "email" => $this->getEmail(),
            "activo" => $this->getActivo(),
            "ultimo_acceso" => $this->getUltimoAcceso()?->format("d-m-Y H:i:s"),
            "created_at" => $this->getCreatedAt()?->format("d-m-Y H:i:s"),
            "updated_at" => $this->getUpdatedAt()?->format("d-m-Y H:i:s")
        ];
    }

    /***********/
    /* PRIVATE */
    /***********/
    private function parseFecha(mixed $valor): ?DateTimeImmutable
    {
        if ($valor === null || $valor === "") {
            return null;
        }
        return $valor instanceof DateTimeImmutable ? $valor : new DateTimeImmutable($valor);
    }
}

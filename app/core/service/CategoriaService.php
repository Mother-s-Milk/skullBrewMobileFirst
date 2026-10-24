<?php

namespace app\core\service;

use app\core\service\base\InterfaceService;
use app\core\service\base\Service;

use app\core\model\dao\CategoriaDAO;
use app\core\model\dto\CategoriaDTO;

use app\libs\connection\Connection;

final class CategoriaService extends Service implements InterfaceService
{
    private CategoriaDAO $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new CategoriaDAO(Connection::get());
    }

    public function save(array $object): CategoriaDTO
    {
        $dto = new CategoriaDTO($object);

        $this->validarPadre(null, $dto->getParentId());

        if ($dto->getSlug() === "") {
            $dto->setSlug($this->generarSlug($dto->getNombre()));
        }


        $this->dao->save($dto);

        return $dto;
    }

    public function load(int $id): CategoriaDTO
    {
        return $this->dao->load($id);
    }

    public function update(array $data): void
    {
        $id = (int) ($data["id"] ?? 0);

        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        // Las fechas las maneja MySQL y vuelven en otro formato: no se mezclan.
        unset($data["created_at"], $data["updated_at"]);

        $actual = $this->dao->load($id)->toArray();
        unset($actual["created_at"], $actual["updated_at"]);

        $dto = new CategoriaDTO(array_merge($actual, $data));

        $this->validarPadre($dto->getId(), $dto->getParentId());

        // Si cambia el nombre, el slug anterior se conserva (las URLs no cambian solas).
        if ($dto->getSlug() === "") {
            $dto->setSlug($this->generarSlug($dto->getNombre(), $id));
        }

        $this->dao->update($dto);
    }

    public function delete(int $id): void
    {
        $this->dao->delete($id);
    }

    /* RETURN ALL ITEMS */
    public function listAll(): array
    {
        return $this->dao->listAll();
    }
}

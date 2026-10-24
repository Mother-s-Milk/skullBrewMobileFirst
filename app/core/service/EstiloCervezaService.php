<?php

namespace app\core\service;

use app\core\service\base\InterfaceService;
use app\core\service\base\Service;

use app\core\model\dao\EstiloCervezaDAO;
use app\core\model\dto\EstiloCervezaDTO;

use app\libs\connection\Connection;

final class EstiloCervezaService extends Service implements InterfaceService
{
    private EstiloCervezaDAO $dao;

    public function __construct()
    {
        parent::__construct();
        $this->dao = new EstiloCervezaDAO(Connection::get());
    }

    public function save(array $object): EstiloCervezaDTO
    {
        $dto = new EstiloCervezaDTO($object);
        $this->dao->save($dto);

        return $dto;
    }

    public function load(int $id): EstiloCervezaDTO
    {
        return $this->dao->load($id);
    }

    public function update(array $data): void
    {
        $id = (int) ($data["id"] ?? 0);

        if ($id <= 0) {
            throw new \InvalidArgumentException("ID inválido.");
        }

        $actual = $this->dao->load($id)->toArray();
        $this->dao->update(new EstiloCervezaDTO(array_merge($actual, $data)));
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

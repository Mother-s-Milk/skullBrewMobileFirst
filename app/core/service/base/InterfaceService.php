<?php

namespace app\core\service\base;

use app\core\model\base\InterfaceDTO;

interface InterfaceService
{
    public function save(array $object): InterfaceDTO;

    public function load(int $id): InterfaceDTO;

    public function update(array $object): void;

    public function delete(int $id): void;

    public function listAll(): array;
}

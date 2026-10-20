<?php

namespace app\core\model\base;

class DAO
{
    protected \PDO $conn;
    protected string $table;

    public function __construct(\PDO $conn, string $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }
}

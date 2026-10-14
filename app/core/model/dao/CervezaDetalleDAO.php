<?php

namespace app\core\model\dao;

use app\core\model\base\DAO;
use app\core\model\base\InterfaceDAO;
use app\core\model\base\InterfaceDTO;

use app\core\model\dto\CervezaDetalleDTO;

final class CervezaDetalleDAO extends DAO implements InterfaceDAO
{

    public function __construct(\PDO $conn)
    {
        parent::__construct($conn, 'cervezas_detalle');
    }

    public function save(InterfaceDTO $object): void
    {
        $sql = "
                INSERT INTO
                    {$this->table}
                    (producto_id, estilo_id, abv, ibu, srm, maltas, lupulos, notas_cata, maridaje)
                VALUES
                    (:producto_id, :estilo_id, :abv, :ibu, :srm, :maltas, :lupulos, :notas_cata, :maridaje)
            ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        $stmt->execute([
            "producto_id" => $data["producto_id"],
            "estilo_id" => $data["estilo_id"],
            "abv" => $data["abv"],
            "ibu" => $data["ibu"],
            "srm" => $data["srm"],
            "maltas" => $data["maltas"],
            "lupulos" => $data["lupulos"],
            "notas_cata" => $data["notas_cata"],
            "maridaje" => $data["maridaje"]
        ]);

        $object->setId((int) $this->conn->lastInsertId());
    }

    public function load(int $id): InterfaceDTO
    {
        $sql = "
            SELECT
                dc.producto_id,
                dc.estilo_id,
                dc.abv,
                dc.ibu,
                dc.srm,
                dc.maltas,
                dc.lupulos,
                dc.notas_cata,
                dc.maridaje
            FROM 
                {$this->table} dc
            WHERE
                dc.id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["id" => $id]);

        //prueba de id inexistente
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \Exception("Elemento con ID {$id} no encontrado.");
        }

        return new CervezaDetalleDTO($data);
    }

    public function update(InterfaceDTO $object): void
    {
        $sql = "
            UPDATE
                {$this->table}
            SET
                producto_id = :producto_id,
                estilo_id = :estilo_id,
                abv = :abv,
                ibu = :ibu,
                srm = :srm,
                maltas = :maltas,
                lupulos = :lupulos,
                notas_cata = :notas_cata,
                maridaje = :maridaje
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);
        $data = $object->toArray();

        $stmt->execute([
            "producto_id" => $data["producto_id"],
            "estilo_id" => $data["estilo_id"],
            "abv" => $data["abv"],
            "ibu" => $data["ibu"],
            "srm" => $data["srm"],
            "maltas" => $data["maltas"],
            "lupulos" => $data["lupulos"],
            "notas_cata" => $data["notas_cata"],
            "maridaje" => $data["maridaje"]
        ]);
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
        $stmt->execute(["id" => $id]);

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
                    orden ASC, nombre ASC
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
                    {$this->table} AS c
                {$whereClause}
            ";
        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute($queryParams);
        $totalCervezasDetalle = (int) $countStmt->fetchColumn();

        $sql = "
                SELECT
                    cd.producto_id,
                    cd.estilo_id,
                    cd.abv,
                    cd.ibu,
                    cd.srm,
                    cd.maltas,
                    cd.lupulos,
                    cd.notas_cata,
                    cd.maridaje
                FROM
                    {$this->table} AS cd
                {$whereClause}
                LIMIT :limit OFFSET :offset
            ";

        $stmt = $this->conn->prepare($sql);
        foreach ($queryParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $cervezasDetalle = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'cervezasDetalle' => $cervezasDetalle,
            'totalCervezasDetalle' => $totalCervezasDetalle
        ];
    }
}

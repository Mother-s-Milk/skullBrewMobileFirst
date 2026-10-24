<?php

require_once '../app/config/DBConfig.php';
require_once '../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\model\dao\CervezaDetalleDAO;
use app\core\model\dto\CervezaDetalleDTO;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    $cd1 = [
        "producto_id" => 1,
        "estilo_id" => 3,
        "abv" => 9.2,
        "ibu" => 12,
        "srm" => 18.2,
        "maltas" => "waska, lokura, fidel",
        "lupulos" => "ni puta idea",
        "notas_catas" => "ni idea",
        "maridaje" => "hamburguesas, pastas, carnes rojas"
    ];

    $dao = new CervezaDetalleDAO($conn);

    try {
        //$dao->save(new CervezaDetalleDTO($cd1));
        /*$dao->save(new CategoriaDTO($c2));
        $dao->save(new CategoriaDTO($c3));*/

        /*$loadDetail = $dao->load(1);
        echo '<h1>Cerveza detalle cargado</h1>';
        print_r($loadDetail->toArray());*/

        $dao->update(new CervezaDetalleDTO([]));
        $loadDetail = $dao->load(1);
        echo '<h1>Cerveza detalle actualizado</h1>';
        print_r($loadDetail->toArray());

        //$dao->delete(1);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    /*$objectList = $dao->listAll();
    echo '<h1>Lista de Cerveza Detalle</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }*/
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

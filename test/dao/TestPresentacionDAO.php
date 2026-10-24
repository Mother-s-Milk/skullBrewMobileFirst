<?php

require_once '../app/config/DBConfig.php';
require_once '../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\model\dao\PresentacionDAO;
use app\core\model\dto\PresentacionDTO;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    $p1 = [
        "producto_id" => 1,
        "formato" => "Lata",
        "volumen_ml" => 500,
        "precio" => 5400,
        "sku" => "quilmes-lata-500",
        "disponible" => 1,
        "orden" => 3
    ];

    $dao = new PresentacionDAO($conn);

    try {
        //$dao->save(new PresentacionDTO($p1));

        /*$loadPresentation = $dao->load(4);
        echo '<h1>Presentación cargada</h1>';
        print_r($loadPresentation->toArray());*/

        /*$dao->update(new PresentacionDTO([
            "id" => 4,
            "producto_id" => 1,
            "formato" => "Botella",
            "volumen_ml" => 750,
            "precio" => 5500,
            "sku" => "quilmes-botella-750",
            "disponible" => 0,
            "orden" => 4
        ]));*/
        /*$loadPresentation = $dao->load(7);
        echo '<h1>Presentación actualizada</h1>';
        print_r($loadPresentation->toArray());*/

        $dao->delete(4);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $dao->listAll();
    echo '<h1>Lista de Presentaciones</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

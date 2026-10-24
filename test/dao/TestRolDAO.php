<?php

require_once '../app/config/DBConfig.php';
require_once '../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\model\dao\RolDAO;
use app\core\model\dto\RolDTO;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    $r1 = [
        "nombre" => "Rol test",
        "descripcion" => "Rol de prueba"
    ];

    $r2 = [
        "nombre" => "Pedilo"
    ];

    $dao = new RolDAO($conn);

    try {
        //$dao->save(new RolDTO($r2));

        /*$loadRol = $dao->load(9);
        echo '<h1>Rol cargado</h1>';
        print_r($loadRol->toArray());*/

        /*$dao->update(new RolDTO([
            "id" => 9,
            "nombre" => "Mega pedilo"
        ]));
        $loadRol = $dao->load(4);
        echo '<h1>Rol actualizado</h1>';
        print_r($loadRol->toArray());*/

        $dao->delete(4);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $dao->listAll();
    echo '<h1>Lista de Roles</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

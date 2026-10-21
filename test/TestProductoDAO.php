<?php

require_once '../app/config/DBConfig.php';
require_once '../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\model\dao\ProductoDAO;
use app\core\model\dto\ProductoDTO;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    /*$p1 = [
        "categoria_id" => 3,
        "nombre" => "Gaseosa Coca Cola",
        "slug" => "coca-cola",
        "descripcion" => "Gaseosa sabor cola",
        "es_alcoholico" => 0,
        "destacado" => 1,
        "badge" => "Estrella",
        "disponible" => 0,
        "activo" => 1
    ];*/

    $dao = new ProductoDAO($conn);

    try {
        //$dao->save(new ProductoDTO($p1));

        /*$loadProduct = $dao->load(2);
        echo '<h1>Producto cargado</h1>';
        print_r($loadProduct->toArray());*/

        /*$dao->update(new ProductoDTO([
            "id" => 6,
            "categoria_id" => 5,
            "nombre" => "Waskiii",
            "descripcion" => "",
            "slug" => "waski"
        ]));
        $loadProduct = $dao->load(6);
        echo '<h1>Producto actualizado</h1>';
        print_r($loadProduct->toArray());*/

        $dao->delete(6);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $dao->listAll();
    echo '<h1>Lista de Productos</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

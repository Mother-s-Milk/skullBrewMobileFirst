<?php

require_once '../app/config/DBConfig.php';
require_once '../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\model\dao\CategoriaDAO;
use app\core\model\dto\CategoriaDTO;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');

    $c1 = [
        "parent_id" => 3,
        "nombre" => "Vinos Tintos",
        "slug" => "vinos-tintos",
        "descripcion" => "Vinos tintos de diferentes variedades y regiones, ideales para acompañar carnes y platos fuertes.",
        "orden" => 5
    ];

    $c2 = [
        "parent_id" => 3,
        "nombre" => "Vinos Blancos",
        "slug" => "vinos-blancos",
        "descripcion" => "Vinos blancos de diferentes variedades y regiones, ideales para acompañar pescados y mariscos.",
        "orden" => 6
    ];

    $dao = new CategoriaDAO($conn);

    try {
        //$dao->save(new CategoriaDTO($c1));
        //$dao->save(new CategoriaDTO($c2));

        /*$loadCategory = $dao->load(6);
        echo '<h1>Categoria cargada</h1>';
        print_r($loadCategory->toArray());*/

        /*$dao->update(new CategoriaDTO([
            "id" => 5,
            "parent_id" => 3,
            "nombre" => "Vinos Tintos",
            "slug" => "vinos-tintos",
            "descripcion" => "Vinos tintos de diferentes variedades y regiones, ideales para acompañar carnes y platos fuertes.",
            "orden" => 5,
            "activa" => true
        ]));
        $loadCategory = $dao->load(5);
        echo '<h1>Categoria cargada</h1>';
        print_r($loadCategory->toArray());*/

        //$dao->delete(5);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $dao->list();
    echo '<h1>Lista de Categorias</h1>';
    foreach ($objectList as $object) {
        print_r($object);
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

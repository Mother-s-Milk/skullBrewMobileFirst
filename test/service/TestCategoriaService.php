<?php

require_once '../../app/config/DBConfig.php';
require_once '../../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\service\CategoriaService;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    $c1 = [
        "parent_id" => null,
        "nombre" => "Aguas",
        "slug" => "aguas",
        "descripcion" => "",
        "orden" => 30
    ];

    $service = new CategoriaService();

    try {
        /*$savedCategory = $service->save($c1);
        echo '<h1>Categoria creada</h1>';
        print_r($savedCategory->toArray());*/

        /*$loadCategory = $service->load(19);
        echo '<h1>Categoria cargada</h1>';
        print_r($loadCategory->toArray());*/

        /*$service->update([
            "id" => 6,
            "parent_id" => 2,
            "nombre" => "Bolivianoooo",
            "slug" => "bolivianoooo",
            "descripcion" => "Bolivianooooo bolivianooo"
        ]);
        $loadCategory = $service->load(6);
        echo '<h1>Categoria actualizada</h1>';
        print_r($loadCategory->toArray());*/

        //$service->delete(10);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $service->listAll();
    echo '<h1>Lista de Categorias</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

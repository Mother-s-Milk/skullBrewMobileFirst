<?php

require_once '../../app/config/DBConfig.php';
require_once '../../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\service\EstiloCervezaService;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    $ec1 = [
        "nombre" => "EPA",
        "descripcion" => "Cervezas con un amargor moderado"
    ];

    $service = new EstiloCervezaService();

    try {
        /*$savedStyle = $service->save($ec1);
        echo '<h1>Estilo creado</h1>';
        print_r($savedStyle->toArray());*/

        /*$loadStyle = $service->load(3);
        echo '<h1>Estilo cargado</h1>';
        print_r($loadStyle->toArray());*/

        /*$service->update([
            "id" => 9,
            "nombre" => "waski",
            "descripcion" => "Cervezas para putos"
        ]);
        $loadStyle = $service->load(9);
        echo '<h1>Estilo actualizado</h1>';
        print_r($loadStyle->toArray());*/

        $service->delete(9);

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $service->listAll();
    echo '<h1>Lista de Estilos de cervea</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

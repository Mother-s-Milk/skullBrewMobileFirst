<?php

require_once '../app/config/DBConfig.php';
require_once '../app/vendor/autoload.php';

use app\libs\connection\Connection;

use app\core\model\dao\UsuarioDAO;
use app\core\model\dto\UsuarioDTO;

try {
    $conn = Connection::get();
    echo ('Conexion establecida');
    echo '<br>';

    $u1 = [
        "rol_id" => 1,
        "nombre" => "Franco Vera",
        "email" => "franco@gmail.com",
        "password_hash" => "1234",
        "activo" => 1
    ];

    $u2 = [
        "rol_id" => 2,
        "nombre" => "Fernando Díaz",
        "email" => "fernando@gmail.com",
        "password_hash" => "1234",
        "activo" => 0
    ];

    $u3 = [
        "rol_id" => 3,
        "nombre" => "Mariano Rodriguez",
        "email" => "mariano@gmail.com",
        "password_hash" => "1234",
        "activo" => 1
    ];

    $dao = new UsuarioDAO($conn);

    try {
        //$dao->save(new UsuarioDTO($u1));
        //$dao->save(new UsuarioDTO($u2));
        //$dao->save(new UsuarioDTO($u3));

        /*$loadUser = $dao->load(2);
        echo '<h1>Usuario cargado</h1>';
        print_r($loadUser->toArray());*/

        /*$dao->update(new UsuarioDTO([
            "id" => 1,
            "rol_id" => 1,
            "nombre" => "Franco Vera",
            "email" => "franco@gmail.com",
            "password_hash" => "1234",
            "activo" => 1
        ]));
        $loadUser = $dao->load(1);
        echo '<h1>Usuario actualizado</h1>';
        print_r($loadUser->toArray());*/

        //$dao->delete(1);

        //$dao->cambiarRol(3, 2);
        
        //$dao->desactivar(4);

        $dao->cambiarPassword(
            3,
            "1234",
            "4321"
        );

    } catch (Exception $ex) {
        print_r($ex->getMessage());
    }

    $objectList = $dao->listAll();
    echo '<h1>Lista de Usuarios</h1>';
    foreach ($objectList as $object) {
        print_r($object->toArray());
        echo '<br>';
    }
} catch (PDOException $ex) {
    echo '<p>Error de conexión' . $ex->getMessage() . '</p>';
}

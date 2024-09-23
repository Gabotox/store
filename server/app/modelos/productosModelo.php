<?php

require_once __DIR__ . "/../../config/conexion.php";


class productosModelo {

    public static function inicio($tabla){

        $stmt = conexion::conectar()->prepare("SELECT * FROM $tabla");
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }
}
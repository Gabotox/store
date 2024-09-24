<?php

require_once __DIR__ . "/../../config/conexion.php";


class productosModelo {

    public static function inicio($tabla){

        $stmt = conexion::conectar()->prepare("SELECT * FROM $tabla");
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }

    static public function registrar($tabla, $datos){
        $stmt = conexion::conectar()->prepare("INSERT INTO `productos`(
                nombre_producto, 
                precio_producto, 
                categoria_producto, 
                fecha_insercion_producto
                ) VALUES (
                :nombre,
                :precio,
                :categoria,
                :fecha_insercion
                )");

        $stmt->bindParam(":nombre", $datos["nombre"]);
        $stmt->bindParam(":precio", $datos["precio"]);
        $stmt->bindParam(":categoria", $datos["categoria"]);
        $stmt->bindParam(":fecha_insercion", $datos["fecha_insercion"]);

        if($stmt -> execute()) {
            return "ok";
        }
    }
    static public function verificarProductoExistente($tabla, $nombre)
    {
        $stmt = conexion::conectar()->prepare("SELECT nombre_producto FROM $tabla WHERE nombre_producto = :nombre");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->execute();

        // Si encuentra una fila con el nombre, devuelve true
        return $stmt->rowCount() > 0;
    }

    static public function mostrar($tabla, $id){
        $stmt = conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_producto = :id");
        $stmt -> bindParam(":id", $id);
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_CLASS);
    }
}
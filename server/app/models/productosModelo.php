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
                descripcion_producto,
                precio_producto, 
                stock_producto,
                imagen_producto,
                categoria_producto, 
                fecha_insercion_producto,
                estado_producto,
                descuento_producto
                ) VALUES (
                :nombre,
                :descripcion,
                :precio,
                :stock,
                :imagen,
                :categoria,
                :fecha_insercion,
                :estado,
                :descuento
                )");

        $stmt->bindParam(":nombre", $datos["nombre"]);
        $stmt->bindParam(":descripcion", $datos["descripcion"]);
        $stmt->bindParam(":precio", $datos["precio"]);
        $stmt->bindParam(":stock", $datos["stock"]);
        $stmt->bindParam(":imagen", $datos["imagen"]);
        $stmt->bindParam(":categoria", $datos["categoria"]);
        $stmt->bindParam(":fecha_insercion", $datos["fecha_insercion"]);
        $stmt->bindParam(":estado", $datos["estado"]);
        $stmt->bindParam(":descuento", $datos["descuento"]);

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

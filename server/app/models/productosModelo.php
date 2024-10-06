<?php

require_once __DIR__ . "/../../config/conexion.php";


class productosModelo
{

    public static function inicio($tabla, $cantidad, $desde)
    {
        if ($cantidad != null) {
            $stmt = conexion::conectar()->prepare("
                SELECT p.*, c.nombre_categoria AS nombre_categoria 
                FROM $tabla p 
                JOIN categorias c ON p.categoria_producto = c.id_categoria 
                LIMIT :desde, :cantidad
            ");
            $stmt->bindParam(':desde', $desde, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        } else {
            $stmt = conexion::conectar()->prepare("
                SELECT p.*, c.nombre_categoria AS nombre_categoria 
                FROM $tabla p 
                JOIN categorias c ON p.categoria_producto = c.id_categoria
            ");
        }


        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    static public function registrar($tabla, $datos)
    {
        try {
            // Preparar la consulta
            $stmt = conexion::conectar()->prepare("INSERT INTO $tabla (
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

            // Asignar los valores a los parámetros
            $stmt->bindParam(":nombre", $datos["nombre"]);
            $stmt->bindParam(":descripcion", $datos["descripcion"]);
            $stmt->bindParam(":precio", $datos["precio"]);
            $stmt->bindParam(":stock", $datos["stock"]);
            $stmt->bindParam(":imagen", $datos["imagen"]);
            $stmt->bindParam(":categoria", $datos["categoria"]);
            $stmt->bindParam(":fecha_insercion", $datos["fecha_insercion"]);
            $stmt->bindParam(":estado", $datos["estado"]);
            $stmt->bindParam(":descuento", $datos["descuento"]);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                return "ok"; // Retornar respuesta exitosa
            } else {
                // Si hay un error en la ejecución, lanzar una excepción
                throw new Exception("Error al insertar el producto: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y devolver el mensaje de error
            return ["error" => $e->getMessage()];
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

    static public function mostrar($tabla, $id)
    {
        $stmt = conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id_producto = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    static public function actualizar($tabla, $datos)
    {

        try {
            // Preparamos la consulta SQL
            $stmt = conexion::conectar()->prepare("UPDATE $tabla SET 
                `nombre_producto`= :nombre,
                `descripcion_producto`= :descripcion,
                `precio_producto`= :precio,
                `stock_producto`= :stock,           
                `imagen_producto`= :imagen,         
                `categoria_producto`= :categoria,
                `fecha_modificacion_producto`= :fecha,
                `estado_producto`= :estado,
                `descuento_producto`= :descuento
                WHERE id_producto = :id");

            // Asociamos los parámetros
            $stmt->bindParam(":id", $datos["id"]);
            $stmt->bindParam(":nombre", $datos["nombre"]);
            $stmt->bindParam(":descripcion", $datos["descripcion"]);
            $stmt->bindParam(":precio", $datos["precio"]);
            $stmt->bindParam(":stock", $datos["stock"]);
            $stmt->bindParam(":imagen", $datos["imagen"]);
            $stmt->bindParam(":categoria", $datos["categoria"]);
            $stmt->bindParam(":fecha", $datos["fechaUpdate"]);
            $stmt->bindParam(":estado", $datos["estado"]);
            $stmt->bindParam(":descuento", $datos["descuento"]);

            // Ejecutamos la consulta
            if ($stmt->execute()) {
                return "ok";
            } else {
                // Si ocurre un error, arrojamos una excepción con la información del error
                throw new Exception("Error al actualizar el producto: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            // Capturamos cualquier excepción y devolvemos el mensaje de error
            return [
                "Respuesta" => [
                    "Mensaje" => "Hubo un problema al actualizar el producto.",
                    "Detalle" => $e->getMessage(),
                    "Estado" => 500
                ]
            ];
        }
    }

    static public function eliminar($tabla, $id)
    {
        try {
            // Preparar la consulta SQL
            $stmt = conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_producto = :id");

            // Asociar el parámetro
            $stmt->bindParam(":id", $id);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                return "ok"; // Si la ejecución fue exitosa
            } else {
                // Arrojamos una excepción si algo falla
                throw new Exception("Error al eliminar el producto: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            // Capturamos y retornamos un mensaje de error
            return $e->getMessage();
        }
    }

    static public function buscar($query)
    {
        try {
            // Preparar la consulta SQL
            $stmt = conexion::conectar()->prepare("SELECT * FROM productos WHERE nombre_producto LIKE :query");
            $searchTerm = "%$query%";
            $stmt->bindParam(':query', $searchTerm);

            // Ejecutar la consulta
            $stmt->execute();

            // Devolver los resultados
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de errores: devuelve el error como un array
            return ["error" => $e->getMessage()];
        }
    }
}

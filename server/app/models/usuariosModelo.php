<?php


class usuariosModelo
{

    # Mostrar registros
    static function inicio($tabla)
    {

        $stmt = conexion::conectar()->prepare(query: "SELECT * FROM $tabla");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function registrar($tabla, $datos)
    {

        $stmt = conexion::conectar()->prepare("INSERT INTO $tabla (
                `cedula_usuario`,
                `nombres_usuario`, 
                `apellidos_usuario`,
                `id_cliente`,
                `llave_secreta`,
                `usuario_usuario`,
                `contra_usuario`,
                `celular_usuario`, 
                `correo_usuario`, 
                `direccion_usuario`,
                `fecha_registro_usuario`, 
                `rol_usuario`, 
                `estado_usuario`
                ) VALUES (
                :cedula,
                :nombres, 
                :apellidos,
                :id_cliente,
                :llave_secreta,
                :usuario, 
                :contra, 
                :celular, 
                :correo, 
                :direccion,
                :fecha_registro, 
                :rol, 
                :estado)");

        $stmt->bindParam(':cedula', $datos['cedula']);
        $stmt->bindParam(':nombres', $datos['nombres']);
        $stmt->bindParam(':apellidos', $datos['apellidos']);
        $stmt->bindParam(':id_cliente', $datos['id_cliente']);
        $stmt->bindParam(':llave_secreta', $datos['llave_secreta']);
        $stmt->bindParam(':usuario', $datos['usuario_usuario']);
        $stmt->bindParam(':contra', $datos['contra_usuario']);
        $stmt->bindParam(':celular', $datos['celular']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':fecha_registro', $datos["fecha_registro"]);
        $stmt->bindParam(':rol', $datos["rol"]);
        $stmt->bindParam(':estado', $datos["estado"]);

        if ($stmt->execute()) {
            return "ok";
        } else {
            print_r(conexion::conectar()->errorInfo());
        }
    }

    static public function verificarCorreoExistente($tabla, $correo)
    {
        $stmt = conexion::conectar()->prepare("SELECT correo_usuario FROM $tabla WHERE correo_usuario = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        // Si encuentra una fila con el correo, devuelve true
        return $stmt->rowCount() > 0;
    }
}

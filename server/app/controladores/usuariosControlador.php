<?php


class usuariosControlador
{

    public function inicio()
    {

        $usuarios = usuariosModelo::inicio("productos");
        
        $respuesta = [
            "Respuesta" => "Mostrar usuarios",
            "Estado" => 200, 
            "Datos" => $usuarios
        ];


        echo json_encode($respuesta, true);

        return;
    }


    public function registrar($datos)
    {
        date_default_timezone_set('America/Bogota');
        $errores = [];

        #echo "<pre>";
        #print_r($datos);
        #echo "</pre>";

        if ($datos["nombres"] == "" || $datos["apellidos"] == "" || $datos["celular"] == "") {
            $errores[] = [
                "Mensaje" => "Debes completar todos los compos.",
                "Estado" => 400
            ];
        }

        // Validación del nombre
        if (strlen($datos["nombres"]) < 3 || strlen($datos["nombres"]) > 24 || miModelo::verificar("^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$", $datos["nombres"])) {
            $errores[] = [
                "Mensaje" => "El nombre es muy corto y solo debe contener letras.",
                "Estado" => 400
            ];
        }

        if (strlen($datos["apellidos"]) < 3 || strlen($datos["apellidos"]) > 24 || miModelo::verificar("^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$", $datos["apellidos"])) {
            $errores[] = [
                "Mensaje" => "El apellido es muy corto y solo debe contener letras.",
                "Estado" => 400
            ];
        }

        if (miModelo::verificar("^\d{10}$", $datos["celular"])) {
            $errores[] = [
                "Mensaje" => "El número de celular no coincide con el formato solicitado.",
                "Estado" => 400
            ];
        }

        if (isset($datos["rol"])) {
            if (miModelo::verificar("^\d{1}$", $datos["rol"])) {
                $errores[] = [
                    "Mensaje" => "El número de rol no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }
        } else {
            $datos["rol"] = 2;
        }

        // Validación del correo electrónico (opcional)
        if (!empty($datos["correo"]) && !filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            $errores[] = [
                "Mensaje" => "El formato del correo electrónico no es válido.",
                "Estado" => 400
            ];
        }


        // Verificar si el correo ya existe en la base de datos
        $usuarioExistente = usuariosModelo::verificarCorreoExistente("usuarios", $datos["correo"]);

        if ($usuarioExistente) {
            $errores[] = [
                "Mensaje" => "El correo electrónico ya se encuentra registrado.",
                "Estado" => 400
            ];
        }


        $id_cliente = miModelo::encryption(string: $datos["nombres"]);
        $llave_secreta = $datos["nombres"] . $datos["apellidos"] . $datos["celular"];
        $llave_secreta = miModelo::encryption($llave_secreta);

        $fecha = date("Y-m-d H:i:s");

        $datos = [
            "nombres" => $datos["nombres"],
            "apellidos" => $datos["apellidos"],
            "id_cliente" => $id_cliente,
            "llave_secreta" => $llave_secreta,
            "celular" => $datos["celular"],
            "correo" => $datos["correo"],
            "fecha_registro" => $fecha,
            "rol" => $datos["rol"],
            "estado" => "activo"
        ];

        // Si hay errores, devolverlos
        if (!empty($errores)) {
            $respuesta = ["Respuesta" => $errores];
            http_response_code(400);
            echo json_encode($respuesta);
            return;
        }

        $registrar = usuariosModelo::registrar("usuarios", $datos);

        // Verificar si el registro fue exitoso

        if ($registrar == "ok") {
            $respuesta = [
                "Mensaje" => "Usuario añadido exitosamente.",
                "Estado" => 200
            ];
            http_response_code(200);
        } else {
            $respuesta = [
                "Mensaje" => "Hubo un problema al añadir el usuario.",
                "Estado" => 500
            ];
            http_response_code(500);
        }

        http_response_code(200);
        echo json_encode($respuesta);

        return;
    }
}

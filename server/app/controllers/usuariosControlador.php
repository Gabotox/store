<?php


class usuariosControlador
{

    public function inicio()
    {

        $usuarios = usuariosModelo::inicio("usuarios");

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


        // Validaciones
        if ($datos["nombres"] == "" || $datos["apellidos"] == "" || $datos["celular"] == "") {
            $errores[] = [
                "Mensaje" => "Debes completar todos los campos obligatorios.",
                "Estado" => 400
            ];
        }

        // Validación de la cédula (opcional)
        if (isset($datos["cedula"]) && !empty($datos["cedula"])) {
            if (!preg_match("/^\d{1,15}$/", $datos["cedula"])) {
                $errores[] = [
                    "Mensaje" => "El número de cédula no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            } else {
                // Solo asignar si es válida
                $datos["usuario_usuario"] = $datos["cedula"];
                $datos["contra_usuario"] = $datos["cedula"];
            }
        } else {
            // Solo asignar si es válida
            $datos["usuario_usuario"] = "";
            $datos["contra_usuario"] = "";
        }



        // Validación de los nombres (obligatorio)
        if (strlen($datos["nombres"]) < 3 || strlen($datos["nombres"]) > 24 || miModelo::verificar("^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$", $datos["nombres"])) {
            $errores[] = [
                "Mensaje" => "El nombre es muy corto y solo debe contener letras.",
                "Estado" => 400
            ];
        }

        // Validación de los apellidos (obligatorio)
        if (strlen($datos["apellidos"]) < 3 || strlen($datos["apellidos"]) > 24 || miModelo::verificar("^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$", $datos["apellidos"])) {
            $errores[] = [
                "Mensaje" => "El apellido es muy corto y solo debe contener letras.",
                "Estado" => 400
            ];
        }


        // Validación del número de celular (obligatorio)
        if (empty($datos["celular"])) {
            $errores[] = [
                "Mensaje" => "El número de celular es obligatorio.",
                "Estado" => 400
            ];
        } elseif (!preg_match("/^\d{10}$/", $datos["celular"])) {
            $errores[] = [
                "Mensaje" => "El número de celular no coincide con el formato solicitado.",
                "Estado" => 400
            ];
        }

        // Validación del correo electrónico (opcional)
        if (isset($datos["correo"])) {
            if (empty($datos["correo"])) {
                $datos["correo"] = null; // o simplemente no asignar
            } elseif (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
                $errores[] = [
                    "Mensaje" => "El formato del correo electrónico no es válido.",
                    "Estado" => 400
                ];
            }
        } else {
            // Manejo si correo no está presente en el array
            $datos["correo"] = null; // o simplemente no asignar
        }

        // Validación del rol (opcional)
        if (isset($datos["rol"])) {
            if ($datos["rol"] == "") {
                $datos["rol"] = "";
            } else if (!preg_match("/^\d{1}$/", $datos["rol"])) {
                // Verifica si el rol no coincide con el patrón
                $errores[] = [
                    "Mensaje" => "El número de rol no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }
        } else {
            // Asigna un valor predeterminado si no está definido
            $datos["rol"] = 2;
        }

        // Validación de la dirección (opcional)
        if (isset($datos["direccion"])) {
            if ($datos["direccion"] == "") {
                $datos["direccion"] = "";
            } else if (!preg_match("/^[\w\s.,#-]+$/", $datos["direccion"])) {
                $errores[] = [
                    "Mensaje" => "La dirección no es válida. Debe contener solo letras, números y algunos caracteres permitidos.",
                    "Estado" => 400
                ];
            }
        } else {
            // Manejo si no se proporciona dirección (opcional)
            $datos["direccion"] = "";
        }

        // Fecha de registro
        $fecha = date("Y-m-d H:i:s");

        // Creación de credenciales
        $id_cliente = miModelo::encryption(string: $datos["nombres"]);
        $llave_secreta = $datos["nombres"] . $datos["apellidos"] . $datos["celular"];
        $llave_secreta = miModelo::encryption($llave_secreta);

        // Verificar si el correo ya existe en la base de datos
        $usuarioExistente = usuariosModelo::verificarCorreoExistente("usuarios", $datos["correo"]);

        if ($usuarioExistente) {
            $errores[] = [
                "Mensaje" => "El correo electrónico ya se encuentra registrado.",
                "Estado" => 400
            ];
        }

        // Crear el array con los datos a enviar
        $datos = [
            "cedula" => $datos["cedula"],
            "nombres" => $datos["nombres"],
            "apellidos" => $datos["apellidos"],
            "id_cliente" => $id_cliente,
            "llave_secreta" => $llave_secreta,
            "usuario_usuario" => $datos["usuario_usuario"],
            "contra_usuario" => $datos["contra_usuario"],
            "celular" => $datos["celular"],
            "correo" => $datos["correo"],
            "direccion" => $datos["direccion"],
            "fecha_registro" => $fecha,
            "rol" => $datos["rol"],
            "estado" => "activo"
        ];

        // Verificar y depurar el contenido de $datos
        # echo "<pre>";
        # print_r($datos);
        #echo "</pre>";
        #return;

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

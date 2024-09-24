<?php

class productosControlador
{

    public function inicio()
    {

        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {

            foreach ($usuarios as $key => $value) {

                if (base64_encode($_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"]) == base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"])) {

                    $productos = productosModelo::inicio("productos");

                    $json = array(
                        "Todal de registros" => count($productos),
                        "Estado" => 200,
                        "Respuesta" => $productos
                    );

                    echo json_encode($json, true);

                    return;
                } else {
                    $json = array(
                        "Estado" => 400,
                        "Respuesta" => "Credenciales inválidas"
                    );

                    echo json_encode($json, true);

                    return;
                }
            }
        } else {
            $json = array(
                "Todal de registros" => "Para tí no hay, pa.",
                "Estado" => 404,
                "Respuesta" => "Para ti no hay productos"
            );

            echo json_encode($json, true);

            return;
        }
    }

    public function registrar($datos)
    {

        date_default_timezone_set('America/Bogota');
        $errores = [];

        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
            $usuarioValido = false;

            foreach ($usuarios as $valueUsuario) {

                if (base64_encode($_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"]) == base64_encode($valueUsuario["id_cliente"] . ":" . $valueUsuario["llave_secreta"])) {

                    $usuarioValido = true;
                    break;
                }
            }

            if (!$usuarioValido) {
                $errores[] = [
                    "Mensaje" => "Credenciales inválidas.",
                    "Estado" => 401
                ];
                http_response_code(401);
                echo json_encode(["Respuesta" => $errores]);
                return;
            }


            // Validaciones de datos
            if (empty($datos["nombre"]) || empty($datos["precio"]) || empty($datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "Debes completar todos los campos obligatorios.",
                    "Estado" => 400
                ];
            }

            if (strlen($datos["nombre"]) < 3 || strlen($datos["nombre"]) > 50 || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $datos["nombre"])) {
                $errores[] = [
                    "Mensaje" => "El nombre del producto es inválido.",
                    "Estado" => 400
                ];
            }

            if (!preg_match("/^\d+(\.\d{1,2})?$/", $datos["precio"])) {
                $errores[] = [
                    "Mensaje" => "El precio no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            if (!preg_match("/^\d+$/", $datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "La categoría no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            if (empty($errores)) {
                $fecha = date("Y-m-d H:i:s");
                $datos = [
                    "nombre" => $datos["nombre"],
                    "precio" => $datos["precio"],
                    "categoria" => $datos["categoria"],
                    "fecha_insercion" => $fecha
                ];

                if (productosModelo::verificarProductoExistente("productos", $datos["nombre"])) {
                    $errores[] = [
                        "Mensaje" => "El producto ya se encuentra registrado en la base de datos.",
                        "Estado" => 400
                    ];
                } else {
                    $registrar = productosModelo::registrar("productos", $datos);

                    if ($registrar == "ok") {
                        $respuesta = [
                            "Mensaje" => "Producto añadido exitosamente.",
                            "Estado" => 200
                        ];
                        http_response_code(200);
                    } else {
                        $errores[] = [
                            "Mensaje" => "Hubo un problema al añadir el producto.",
                            "Estado" => 500
                        ];
                        http_response_code(500);
                    }
                }
            }

            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "Respuesta" => $errores
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode($respuesta);
        } else {
            $errores[] = [
                "Mensaje" => "No se ha proporcionado autenticación.",
                "Estado" => 401
            ];
            http_response_code(401);
            echo json_encode(["Respuesta" => $errores]);
        }
    }

    public function mostrar($id)
    {

        date_default_timezone_set('America/Bogota');
        $errores = [];

        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
            $usuarioValido = false;

            foreach ($usuarios as $valueUsuario) {

                if (base64_encode($_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"]) == base64_encode($valueUsuario["id_cliente"] . ":" . $valueUsuario["llave_secreta"])) {

                    $usuarioValido = true;
                    break;
                }
            }

            if (!$usuarioValido) {
                $errores[] = [
                    "Mensaje" => "Credenciales inválidas.",
                    "Estado" => 401
                ];
                http_response_code(401);
                echo json_encode(["Respuesta" => $errores]);
                return;
            }

            if(empty($errores)){
                $productos = productosModelo::mostrar("productos", $id);

                if(empty($productos)) {
                    $respuesta[] = [
                        "Estado" => 404,
                        "Mensaje" => "No se encuentra el producto."
                    ];  
                } else {
                    $respuesta[] = [
                        "Estado" => 200,
                        "Mensaje" => "Mostrando productos",
                        "Rspuesta" => $productos
                    ];
                }
            }

            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "Respuesta" => $errores
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode($respuesta);

        }  else {
            $errores[] = [
                "Mensaje" => "No se ha proporcionado autenticación.",
                "Estado" => 401
            ];
            http_response_code(401);
            echo json_encode(["Respuesta" => $errores]);
        }

    }

    public function actualizar($id, $datos)
    {


        $json = array(
            "Detalles" => "Producto actualizado, su id es => " . $id
        );

        echo json_encode($json, true);

        return;
    }

    public function eliminar($id)
    {
        $json = array(
            "Detalles" => "Producto eliminado, su id es => " . $id
        );

        echo json_encode($json, true);

        return;
    }
}

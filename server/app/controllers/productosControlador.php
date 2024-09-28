<?php

class productosControlador
{

    public function inicio()
    {

        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {

            foreach ($usuarios as $key => $value) {

                if (base64_encode($_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"]) == base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) && $value["rol_usuario"] == 1) {

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

                if (
                    $_SERVER["PHP_AUTH_USER"] === $valueUsuario["id_cliente"] &&
                    $valueUsuario["llave_secreta"] &&
                    $valueUsuario["rol_usuario"] == 1 && $valueUsuario["estado_usuario"] == "activo"
                ) {

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



            if (strlen($datos["nombre"]) < 3 || strlen($datos["nombre"]) > 50 || !preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/", $datos["nombre"])) {
                $errores[] = [
                    "Mensaje" => "El nombre del producto no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            if (!preg_match("/^\d{1,3}(\.\d{3})*(\,\d{1,2})?$/", $datos["precio"])) {
                $errores[] = [
                    "Mensaje" => "El precio no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            } else {
                $precio = str_replace('.', '', $datos["precio"]); // Eliminar puntos
                $precio = str_replace(',', '.', $precio); // Reemplazar la coma por punto
                $precio = floatval($precio); // Convertir a float o decimal
            }

            // Validación de categoría
            if (!isset($datos["categoria"]) || empty($datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "Debe seleccionar una categoría de producto.",
                    "Estado" => 400
                ];
            } else if (!preg_match("/^\d+$/", $datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "La categoría no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            // Validando descripción
            if (isset($datos["descripcion"]) && $datos["descripcion"] !== "") {
                if (!preg_match("/^[\w\s.,#-áéíóúÁÉÍÓÚñÑ]+$/u", $datos["descripcion"])) {
                    $errores[] = [
                        "Mensaje" => "El formato de la descripción no es válido, contiene caracteres no permitidos.",
                        "Estado" => 400
                    ];
                }
            } else {
                $datos["descripcion"] = "";
            }

            // Validando stock
            if (isset($datos["stock"]) && $datos["stock"] !== "") {
                if (!preg_match("/^\d+$/", $datos["stock"])) {
                    $errores[] = [
                        "Mensaje" => "El formato de stock no es válido, debe contener números enteros.",
                        "Estado" => 400
                    ];
                }
            } else {
                $datos["stock"] = "";
            }

            // Validando imágen
            if ($datos['imagen'] && $datos['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagenTmp = $datos['imagen']['tmp_name'];
                $imagenNombre = $datos['imagen']['name'];
                $imagenExtension = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));

                // Validación de la extensión de la imagen
                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($imagenExtension, $extensionesPermitidas)) {
                    $errores[] = "Formato de imagen no permitido. Solo se permiten jpg, jpeg, png o webp.";
                }

                // Validación del tamaño de la imagen // 5MB máximo
                if ($datos['imagen']['size'] > 5 * 1024 * 1024) {
                    $errores[] = [
                        "Mensaje" => "El tamaño de la imagen excede el límite permitido de 5MB.",
                        "Estado" => 400
                    ];
                }

                // Si no hay errores, mover la imagen
                if (empty($errores)) {
                    $directorioDestino = __DIR__ . '/../../uploads/productos/';
                    $imagenNuevaRuta = $directorioDestino . uniqid() . '.' . $imagenExtension;

                    if (!move_uploaded_file($imagenTmp, $imagenNuevaRuta)) {
                        $errores[] = [
                            "Mensaje" => "Error al subir la imagen. Inténtalo de nuevo.",
                            "Estado" => 500
                        ];
                    } else {
                        // Guardamos la ruta de la imagen para la base de datos
                        $datos['imagen'] = $imagenNuevaRuta;
                    }
                }
            } else {
                $datos['imagen'] = '';
            }

            $fecha = date("Y-m-d H:i:s");

            // Validando el descuento
            if (isset($datos["descuento"]) && $datos["descuento"] !== "") {
                if (!preg_match("/^\d{1,3}(\.\d{3})*(\,\d{1,2})?$/", $datos["descuento"])) {
                    $errores[] = [
                        "Mensaje" => "El formato de descuento no es válido, debe contener números enteros.",
                        "Estado" => 400
                    ];
                }
            } else {
                $datos["descuento"] = "";
            }

            if (productosModelo::verificarProductoExistente("productos", $datos["nombre"])) {
                $errores[] = [
                    "Mensaje" => "El producto ya se encuentra registrado en la base de datos.",
                    "Estado" => 400
                ];
            }

            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode([
                    "Respuesta" => $errores
                ]);
                return;
            }

            $datos = [
                "nombre" => $datos["nombre"],
                "descripcion" => $datos["descripcion"],
                "precio" => $precio,
                "stock" => $datos["stock"],
                "imagen" => $datos["imagen"],
                "categoria" => $datos["categoria"],
                "fecha_insercion" => $fecha,
                "estado" => "activo",
                "descuento" => $datos["descuento"],
            ];

            try {
                // Intenta registrar el producto
                $registrar = productosModelo::registrar("productos", $datos);

                if ($registrar == "ok") {
                    $respuesta = [
                        "Mensaje" => "Producto añadido exitosamente.",
                        "Estado" => 200
                    ];
                    http_response_code(200);
                } else {
                    throw new Exception("Hubo un problema al añadir el producto.");
                }
            } catch (Exception $e) {
                $errores[] = [
                    "Mensaje" => $e->getMessage(),
                    "Estado" => 500
                ];
                http_response_code(500);
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

                if (base64_encode($_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"]) == base64_encode($valueUsuario["id_cliente"] . ":" . $valueUsuario["llave_secreta"]) && $valueUsuario["rol_usuario"] == 1) {

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

            if (empty($errores)) {
                $productos = productosModelo::mostrar("productos", $id);

                if (empty($productos)) {
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
        } else {
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

        date_default_timezone_set('America/Bogota');
        $errores = [];

        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
            $usuarioValido = false;

            foreach ($usuarios as $valueUsuario) {

                if (base64_encode($_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"]) == base64_encode($valueUsuario["id_cliente"] . ":" . $valueUsuario["llave_secreta"]) && $valueUsuario["rol_usuario"] == 1) {
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
        } else {
            $errores[] = [
                "Mensaje" => "No se ha proporcionado autenticación.",
                "Estado" => 401
            ];
            http_response_code(401);
            echo json_encode(["Respuesta" => $errores]);
        }


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

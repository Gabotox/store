<?php

class productosControlador
{

    public function inicio($pagina)
    {
        $errores = [];

        // Obtener lista de usuarios para validar credenciales
        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {

            $usuarioValido = false;
            foreach ($usuarios as $valueUsuario) {
                if (
                    $_SERVER["PHP_AUTH_USER"] . ":" . $_SERVER["PHP_AUTH_PW"] == $valueUsuario["id_cliente"] . ":" . $valueUsuario["llave_secreta"] &&
                    $valueUsuario["rol_usuario"] == 1 && $valueUsuario["estado_usuario"] == "activo"
                ) {
                    $usuarioValido = true;
                    break;
                }
            }

            if ($usuarioValido) {
                try {
                    if ($pagina != null) {

                        $cantidad = 10;
                        $desde = ($pagina - 1) * 10;

                        $productos = productosModelo::inicio("productos", $cantidad, $desde);
                    } else {
                        $productos = productosModelo::inicio("productos", null, null);
                    }
                    // Intentar obtener los productos

                    // Responder con los productos
                    $respuesta = [
                        "Total de registros" => count($productos),
                        "Estado" => 200,
                        "Respuesta" => $productos
                    ];
                    http_response_code(200);
                    echo json_encode($respuesta);
                } catch (Exception $e) {
                    // Capturar cualquier error al obtener los productos
                    $errores[] = [
                        "Mensaje" => "Hubo un problema al obtener los productos.",
                        "Detalle" => $e->getMessage(),
                        "Estado" => 500
                    ];
                    http_response_code(500);
                    echo json_encode(["Respuesta" => $errores]);
                }
            } else {
                // Responder con credenciales inválidas
                http_response_code(401);
                echo json_encode([
                    "Estado" => 401,
                    "Respuesta" => "Credenciales inválidas"
                ]);
            }
        } else {
            // Responder si no se proporcionaron credenciales
            http_response_code(401);
            echo json_encode([
                "Estado" => 401,
                "Respuesta" => "No se proporcionaron credenciales"
            ]);
        }
    }

    public function registrar($datos)
    {
        date_default_timezone_set('America/Bogota');
        $errores = [];

        // Obtener los usuarios
        $usuarios = usuariosModelo::inicio("usuarios");

        // Verificar si se proporciona autenticación
        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
            $usuarioValido = false;

            // Validar usuario y credenciales
            foreach ($usuarios as $valueUsuario) {
                if (
                    $_SERVER["PHP_AUTH_USER"] === $valueUsuario["id_cliente"] &&
                    $valueUsuario["llave_secreta"] &&
                    $valueUsuario["rol_usuario"] == 1 &&
                    $valueUsuario["estado_usuario"] == "activo"
                ) {
                    $usuarioValido = true;
                    break;
                }
            }

            // Si el usuario no es válido, retornar error
            if (!$usuarioValido) {
                http_response_code(401);
                echo json_encode([
                    "Respuesta" => [["Mensaje" => "Credenciales inválidas.", "Estado" => 401]]
                ]);
                return;
            }

            // Validaciones de datos
            if (empty($datos["nombre"]) || empty($datos["precio"]) || empty($datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "Debes completar todos los campos obligatorios.",
                    "Estado" => 400
                ];
            }

            // Validación del nombre
            if (strlen($datos["nombre"]) < 3 || strlen($datos["nombre"]) > 50 || !preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/", $datos["nombre"])) {
                $errores[] = [
                    "Mensaje" => "El nombre del producto no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            // Validación del precio
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
            if (empty($datos["categoria"]) || !preg_match("/^\d+$/", $datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "La categoría no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            // Validación de descripción
            if (isset($datos["descripcion"]) && $datos["descripcion"] !== "" && !preg_match("/^[\w\s.,#-áéíóúÁÉÍÓÚñÑ]+$/u", $datos["descripcion"])) {
                $errores[] = [
                    "Mensaje" => "El formato de la descripción no es válido, contiene caracteres no permitidos.",
                    "Estado" => 400
                ];
            } else {
                $datos["descripcion"] = $datos["descripcion"] ?? ""; // Inicializa si no está definido
            }

            // Validación de stock
            if (isset($datos["stock"]) && $datos["stock"] !== "" && !preg_match("/^\d+$/", $datos["stock"])) {
                $errores[] = [
                    "Mensaje" => "El formato de stock no es válido, debe contener números enteros.",
                    "Estado" => 400
                ];
            } else {
                $datos["stock"] = $datos["stock"] ?? ""; // Inicializa si no está definido
            }

            // Validación de imagen
            if (!empty($datos['imagen']) && $datos['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagenTmp = $datos['imagen']['tmp_name'];
                $imagenNombre = $datos['imagen']['name'];
                $imagenExtension = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));

                // Validación de la extensión de la imagen
                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($imagenExtension, $extensionesPermitidas)) {
                    $errores[] = [
                        "Mensaje" => "Formato de imagen no permitido. Solo se permiten jpg, jpeg, png o webp.",
                        "Estado" => 400
                    ];
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
                $datos['imagen'] = ''; // Asegúrate de que la imagen esté definida
            }

            // Validación de descuento
            if (isset($datos["descuento"]) && $datos["descuento"] !== "" && !preg_match("/^\d{1,3}(\.\d{3})*(\,\d{1,2})?$/", $datos["descuento"])) {
                $errores[] = [
                    "Mensaje" => "El formato de descuento no es válido, debe contener números enteros.",
                    "Estado" => 400
                ];
            } else {
                $datos["descuento"] = $datos["descuento"] ?? ""; // Inicializa si no está definido
            }

            // Verificar si el producto ya existe
            if (productosModelo::verificarProductoExistente("productos", $datos["nombre"])) {
                $errores[] = [
                    "Mensaje" => "El producto ya se encuentra registrado en la base de datos.",
                    "Estado" => 400
                ];
            }

            // Si hay errores, retornar respuesta
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(["Respuesta" => $errores]);
                return;
            } else {
                // Preparar los datos para registrar el producto
                $datos = [
                    "nombre" => $datos["nombre"],
                    "descripcion" => $datos["descripcion"],
                    "precio" => $precio,
                    "stock" => $datos["stock"],
                    "imagen" => $datos["imagen"],
                    "categoria" => $datos["categoria"],
                    "fecha_insercion" => date("Y-m-d H:i:s"),
                    "estado" => "activo",
                    "descuento" => $datos["descuento"],
                ];

                // Intentar registrar el producto
                $respuesta = [];
                try {
                    $registrar = productosModelo::registrar("productos", $datos);
                    if ($registrar == "ok") {
                        $respuesta = [
                            "Mensaje" => "Producto añadido exitosamente.",
                            "Estado" => 200
                        ];
                        http_response_code(200);
                        echo json_encode($respuesta);
                    } else {
                        throw new Exception("Hubo un problema al añadir el producto.");
                    }
                } catch (Exception $e) {
                    $errores[] = [
                        "Mensaje" => $e->getMessage(),
                        "Estado" => 500
                    ];
                    http_response_code(500);
                    echo json_encode(["Respuesta" => $errores]);
                    return;
                }
            }
        } else {
            // Si no se proporciona autenticación
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


            // Verificar si el producto existe
            $productoExistente = productosModelo::mostrar("productos", $id);

            if (!$productoExistente) {
                $errores[] = [
                    "Mensaje" => "El producto con el ID proporcionado no existe.",
                    "Estado" => 404
                ];
                http_response_code(404);
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

            // Validación del nombre
            if (strlen($datos["nombre"]) < 3 || strlen($datos["nombre"]) > 50 || !preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/", $datos["nombre"])) {
                $errores[] = [
                    "Mensaje" => "El nombre del producto no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            // Validación del precio
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
            if (empty($datos["categoria"]) || !preg_match("/^\d+$/", $datos["categoria"])) {
                $errores[] = [
                    "Mensaje" => "La categoría no coincide con el formato solicitado.",
                    "Estado" => 400
                ];
            }

            // Validación de descripción
            if (isset($datos["descripcion"]) && $datos["descripcion"] !== "" && !preg_match("/^[\w\s.,#-áéíóúÁÉÍÓÚñÑ]+$/u", $datos["descripcion"])) {
                $errores[] = [
                    "Mensaje" => "El formato de la descripción no es válido, contiene caracteres no permitidos.",
                    "Estado" => 400
                ];
            } else {
                $datos["descripcion"] = $datos["descripcion"] ?? ""; // Inicializa si no está definido
            }

            // Validación de stock
            if (isset($datos["stock"]) && $datos["stock"] !== "" && !preg_match("/^\d+$/", $datos["stock"])) {
                $errores[] = [
                    "Mensaje" => "El formato de stock no es válido, debe contener números enteros.",
                    "Estado" => 400
                ];
            } else {
                $datos["stock"] = $datos["stock"] ?? ""; // Inicializa si no está definido
            }

            // Validación de imagen
            if (!empty($datos['imagen']) && $datos['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagenTmp = $datos['imagen']['tmp_name'];
                $imagenNombre = $datos['imagen']['name'];
                $imagenExtension = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));

                // Validación de la extensión de la imagen
                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($imagenExtension, $extensionesPermitidas)) {
                    $errores[] = [
                        "Mensaje" => "Formato de imagen no permitido. Solo se permiten jpg, jpeg, png o webp.",
                        "Estado" => 400
                    ];
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
                $datos['imagen'] = ''; // Asegúrate de que la imagen esté definida
            }

            // Validación de descuento
            if (isset($datos["descuento"]) && $datos["descuento"] !== "" && !preg_match("/^\d{1,3}(\.\d{3})*(\,\d{1,2})?$/", $datos["descuento"])) {
                $errores[] = [
                    "Mensaje" => "El formato de descuento no es válido, debe contener números enteros.",
                    "Estado" => 400
                ];
            } else {
                $datos["descuento"] = $datos["descuento"] ?? ""; // Inicializa si no está definido
            }



            // Si hay errores, retornar respuesta
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(["Respuesta" => $errores]);
                return;
            } else {


                $productos = productosModelo::mostrar("productos", $id);

                // Preparar los datos para registrar el producto
                $datos = [
                    "id" => $id,
                    "nombre" => $datos["nombre"],
                    "descripcion" => $datos["descripcion"],
                    "precio" => $precio,
                    "stock" => $datos["stock"],
                    "imagen" => $datos["imagen"],
                    "categoria" => $datos["categoria"],
                    "fechaUpdate" => date("Y-m-d H:i:s"),
                    "estado" => "activo",
                    "descuento" => $datos["descuento"],
                ];

                // Intentar registrar el producto
                $respuesta = [];
                try {

                    $actualizar = productosModelo::actualizar("productos", $datos);
                    if ($actualizar == "ok") {
                        $respuesta = [
                            "Mensaje" => "Producto actualizado exitosamente.",
                            "Estado" => 200
                        ];
                        http_response_code(200);
                        echo json_encode($respuesta);
                    } else {
                        throw new Exception("Hubo un problema al actualizar el producto.");
                    }
                } catch (Exception $e) {
                    $errores[] = [
                        "Mensaje" => $e->getMessage(),
                        "Estado" => 500
                    ];
                    http_response_code(500);
                    echo json_encode(["Respuesta" => $errores]);
                    return;
                }
            }
        } else {
            $errores[] = [
                "Mensaje" => "No se ha proporcionado autenticación.",
                "Estado" => 401
            ];
            http_response_code(401);
            echo json_encode(["Respuesta" => $errores]);
        }


        /* $json = array(
            "Detalles" => "Producto actualizado, su id es => " . $id
        );

        echo json_encode($json, true);

        return; */
    }

    public function eliminar($id)
    {
        $errores = [];

        // Obtener lista de usuarios para validar credenciales
        $usuarios = usuariosModelo::inicio("usuarios");

        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
            $usuarioValido = false;

            // Validación de usuario y credenciales
            foreach ($usuarios as $valueUsuario) {
                if (
                    $_SERVER["PHP_AUTH_USER"] === $valueUsuario["id_cliente"] &&
                    $valueUsuario["llave_secreta"] &&
                    $valueUsuario["rol_usuario"] == 1 &&
                    $valueUsuario["estado_usuario"] == "activo"
                ) {
                    $usuarioValido = true;
                    break;
                }
            }

            if (!$usuarioValido) {
                // Respuesta de credenciales inválidas
                http_response_code(401);
                echo json_encode([
                    "Respuesta" => [
                        [
                            "Mensaje" => "Credenciales inválidas.",
                            "Estado" => 401
                        ]
                    ]
                ]);
                return;
            }

            // Verificar si el producto existe
            $productoExistente = productosModelo::mostrar("productos", $id);

            if (!$productoExistente) {
                $errores[] = [
                    "Mensaje" => "El producto con el ID proporcionado no existe.",
                    "Estado" => 404
                ];
                http_response_code(404);
                echo json_encode(["Respuesta" => $errores]);
                return;
            }

            // Intentar eliminar el producto
            try {
                $eliminar = productosModelo::eliminar("productos", $id);
                if ($eliminar == "ok") {
                    // Producto eliminado exitosamente
                    $respuesta = [
                        "Mensaje" => "Producto eliminado exitosamente.",
                        "Estado" => 200
                    ];
                    http_response_code(200);
                    echo json_encode($respuesta);
                } else {
                    // Si eliminar no retorna "ok", manejar como error
                    throw new Exception($eliminar);
                }
            } catch (Exception $e) {
                // Capturar y manejar excepciones
                $errores[] = [
                    "Mensaje" => $e->getMessage(),
                    "Estado" => 500
                ];
                http_response_code(500);
                echo json_encode(["Respuesta" => $errores]);
                return;
            }
        } else {
            // Respuesta si no se proporcionaron credenciales
            $errores[] = [
                "Mensaje" => "No se ha proporcionado autenticación.",
                "Estado" => 401
            ];
            http_response_code(401);
            echo json_encode(["Respuesta" => $errores]);
        }
    }


    public function buscar($query)
    {
        // Asegúrate de limpiar el input para evitar inyecciones SQL
        $query = miModelo::limpiar($query);

        // Suponiendo que tienes un modelo que interactúa con la base de datos
        $resultado = productosModelo::buscar($query);

        if (isset($resultado['error'])) {
            // Imprimir el error en formato JSON
            echo json_encode(["error" => $resultado['error']]);
        } elseif ($resultado) {
            echo json_encode($resultado);
        } else {
            // Respuesta si no se encuentran productos
            echo json_encode(["mensaje" => "No se encontraron productos"]);
        }
    }
}

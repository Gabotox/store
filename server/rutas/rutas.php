<?php

// Separa la URL en partes usando "/" como delimitador
$array_rutas = explode("/", $_SERVER['REQUEST_URI']);

$errores = [];
// Si solo hay un segmento en la URL, retorna un mensaje de error
if (count(array_filter($array_rutas)) == 1) {
    $respuesta = [
        "Respuesta" => "No encontrado",
        "Estado" => 404
    ];
    echo json_encode($respuesta);
    http_response_code(404); // Código 404: No encontrado
    return;
} else {

    // Verificar si hay dos partes en la ruta y manejar cada recurso
    if (count(array_filter($array_rutas)) == 2) {

        // Obtener la segunda parte de la ruta, que representa la acción
        $accion = $array_rutas[2] ?? null;
        $subAccion = $array_rutas[3] ?? null;

        switch ($accion) {
            case 'usuarios':
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        // Llama al método para listar usuarios
                        $usuarios = new usuariosControlador();
                        $usuarios->inicio();
                        break;

                    default:
                        // Si el método no es permitido, retorna un error
                        $respuesta = ["error" => "Método no permitido"];
                        echo json_encode($respuesta);
                        http_response_code(405); // Código 405: Método no permitido
                        break;
                }
                break;

            case 'registro':
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        // Verifica si la acción es un registro

                        // Limpiando las variables
                        $nombres = miModelo::limpiar($_POST["nombres"] ?? '');
                        $apellidos = miModelo::limpiar($_POST["apellidos"] ?? '');
                        $celular = miModelo::limpiar($_POST["celular"] ?? '');
                        $correo = miModelo::limpiar($_POST["correo"] ?? '');

                        $datos = [
                            'nombres' => $nombres,
                            'apellidos' => $apellidos,
                            'celular' => $celular,
                            'correo' => $correo
                        ];

                        // Instancio la clase usuariosControlador
                        $usuarios = new usuariosControlador();

                        // Le envío los datos por parámetros al método registrar
                        $usuarios->registrar($datos);
                        break;

                    default:
                        // Si el método no es permitido, retorna un error
                        $respuesta = ["error" => "Método no permitido"];
                        echo json_encode($respuesta);
                        http_response_code(405); // Código 405: Método no permitido
                        break;
                }
                break;

            case 'productos':
                // Manejo de las peticiones para productos
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        // Llama al método para listar todos los productos
                        $productos = new productosControlador();
                        $productos->inicio();
                        break;

                    case 'POST':
                        // Llama al método para agregar un nuevo producto
                        $productos = new productosControlador();
                        $productos->crear();
                        break;

                    default:
                        // Si el método no es permitido, retorna un error
                        $respuesta = ["error" => "Método no permitido"];
                        echo json_encode($respuesta);
                        http_response_code(405); // Código 405: Método no permitido
                        break;
                }
                break;

            case 'pedidos':
                // Manejo de las peticiones para pedidos
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        // Retorna la vista de pedidos para el administrador
                        $respuesta = ["Vista" => "Pedidos"];
                        echo json_encode($respuesta);
                        break;

                    case 'POST':
                        // Lógica para realizar un nuevo pedido (por implementar)
                        break;

                    default:
                        // Si el método no es permitido, retorna un error
                        $respuesta = ["error" => "Método no permitido"];
                        echo json_encode($respuesta);
                        http_response_code(405); // Código 405: Método no permitido
                        break;
                }
                break;

            case 'admin':
                // Manejo de las peticiones para administración
                $subAccion = $array_rutas[3] ?? null; // Obtener subacción si existe
                if ($subAccion === 'login') {
                    // Manejar el inicio de sesión del administrador (por implementar)
                } elseif ($subAccion === 'logout') {
                    // Manejar el cierre de sesión del administrador (por implementar)
                }
                break;

            default:
                // Si la acción no es reconocida, retorna un error
                $respuesta = ["error" => "Recurso no encontrado"];
                echo json_encode($respuesta);
                http_response_code(404); // Código 404: No encontrado
                break;
        }

        // Manejo de rutas con ID de producto
    } elseif ($array_rutas[2] == 'productos' && is_numeric($array_rutas[3])) {
        $idProducto = $array_rutas[3]; // El ID del producto

        // Manejo de diferentes métodos HTTP para un producto específico
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // Obtener y mostrar un producto por su ID
                $producto = new productosControlador();
                $producto->mostrar($idProducto);
                break;

            case 'PUT':
                // Editar un producto por su ID
                $editar_producto = new productosControlador();
                $editar_producto->editar($idProducto);
                break;

            case 'DELETE':
                // Eliminar un producto por su ID
                $eliminar_producto = new productosControlador();
                $eliminar_producto->eliminar($idProducto);
                break;

            default:
                // Si el método no es permitido, retorna un error
                $respuesta = ["error" => "Método no permitido"];
                echo json_encode($respuesta);
                http_response_code(405); // Código 405: Método no permitido
                break;
        }
    } else {
        // Si la ruta no coincide con ninguna opción, retorna un error
        $respuesta = ["error" => "Recurso no encontrado"];
        echo json_encode($respuesta);
        http_response_code(404); // Código 404: No encontrado
    }

}

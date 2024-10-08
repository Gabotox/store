<?php


// Aquí va tu lógica para obtener los productos

// Separa la URL en partes usando "/" como delimitador
$array_rutas = explode("/", trim($_SERVER['REQUEST_URI'], characters: '/'));

// Verifica si la paginación está presente
if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
    $productos = new productosControlador();
    $productos->inicio($_GET["pagina"]);
    return; // Detiene la ejecución si se está utilizando la paginación
}

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
        $accion = $array_rutas[1] ?? null; // Cambiado de [2] a [1]

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
                        // Limpiando las variables
                        $cedula = miModelo::limpiar($_POST["cedula"] ?? '');
                        $nombres = miModelo::limpiar($_POST["nombres"] ?? '');
                        $apellidos = miModelo::limpiar($_POST["apellidos"] ?? '');
                        $celular = miModelo::limpiar($_POST["celular"] ?? '');
                        $correo = miModelo::limpiar($_POST["correo"] ?? '');
                        $direccion = miModelo::limpiar($_POST["direccion"] ?? '');
                        $rol = miModelo::limpiar($_POST["rol"] ?? '');

                        // Crear el array con los datos a enviar
                        $datos = [
                            "cedula" => $cedula,
                            "nombres" => $nombres,
                            "apellidos" => $apellidos,
                            "celular" => $celular,
                            "correo" => $correo,
                            "direccion" => $direccion,
                            "rol" => $rol
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
                        // Llamar al método para listar todos los productos
                        $productos = new productosControlador();
                        $productos->inicio(null);
                        break;

                    case 'POST':
                        $nombre = miModelo::limpiar($_POST["nombre"] ?? "");
                        $descripcion = miModelo::limpiar($_POST["descripcion"] ?? "");
                        $precio = miModelo::limpiar($_POST["precio"] ?? "");
                        $stock = miModelo::limpiar($_POST["stock"] ?? "");
                        $imagen = $_FILES['imagen'] ?? null;
                        $descuento = miModelo::limpiar($_POST["descuento"] ?? "");
                        $categoria = miModelo::limpiar($_POST["categoria"] ?? "");

                        $datos = [
                            "nombre" => $nombre,
                            "descripcion" => $descripcion,
                            "precio" => $precio,
                            "stock" => $stock,
                            "categoria" => $categoria,
                            "imagen" => $imagen,
                            "descuento" => $descuento
                        ];

                        // Llama al método para agregar un nuevo producto
                        $productos = new productosControlador();
                        $productos->registrar($datos);
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
                $subAccion = $array_rutas[2] ?? null; // Obtener subacción si existe
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
    } elseif (count(array_filter($array_rutas)) == 3 && $array_rutas[1] == 'productos' && is_numeric($array_rutas[2])) {
        // Manejo de diferentes métodos HTTP para un producto específico
        $productoId = array_filter($array_rutas)[2];

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // Obtener y mostrar un producto por su ID
                $producto = new productosControlador();
                $producto->mostrar($productoId);
                break;

            case 'PUT':
                // Editar un producto por su ID
                parse_str(file_get_contents("php://input"), $datos);
                // Asegúrate de limpiar los datos que vinieron en el cuerpo
                $nombre = miModelo::limpiar($datos["nombre"] ?? "");
                $precio = miModelo::limpiar($datos["precio"] ?? "");
                $categoria = miModelo::limpiar($datos["categoria"] ?? "");

                $datos = [
                    "nombre" => $nombre,
                    "precio" => $precio,
                    "categoria" => $categoria,
                ];

                $editarProducto = new productosControlador();
                $editarProducto->actualizar($productoId, $datos);
                break;

            case 'DELETE':
                // Eliminar un producto por su ID
                $eliminarProducto = new productosControlador();
                $eliminarProducto->eliminar($productoId);
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

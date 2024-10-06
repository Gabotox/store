<?php

header("Access-Control-Allow-Origin: http://localhost"); // Permite solicitudes desde localhost
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Cabeceras permitidas

// Manejar las solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . "/server/app/controllers/rutasControlador.php";
require_once __DIR__ . "/server/app/controllers/productosControlador.php";
require_once __DIR__ . "/server/app/controllers/usuariosControlador.php";
require_once __DIR__ . "/server/app/models/usuariosModelo.php";
require_once __DIR__ . "/server/app/models/productosModelo.php";
require_once __DIR__ . "/server/app/models/miModelo.php";

// Obtén la consulta de búsqueda si está presente
$query = $_GET['busqueda'] ?? null;

// Muestra lo que se está buscando
if ($query) {
    // Crea una instancia del controlador de productos y llama a la función buscar
    $productos = new productosControlador();
    $productos->buscar($query);
} else {
    // Si no hay búsqueda, instancia el controlador de rutas y llama a la función inicio
    $rutas = new rutasControlador();
    $rutas->inicio();
}

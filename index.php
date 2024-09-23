<?php
require_once __DIR__ . "/server/app/controladores/rutasControlador.php";
require_once __DIR__ . "/server/app/controladores/productosControlador.php";
require_once __DIR__ . "/server/app/controladores/usuariosControlador.php";
require_once __DIR__ . "/server/app/modelos/usuariosModelo.php";
require_once __DIR__ . "/server/app/modelos/productosModelo.php";
require_once __DIR__ . "/server/app/modelos/miModelo.php";

$rutas = new rutasControlador();

$rutas -> inicio();
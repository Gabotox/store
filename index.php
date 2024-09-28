<?php
require_once __DIR__ . "/server/app/controllers/rutasControlador.php";
require_once __DIR__ . "/server/app/controllers/productosControlador.php";
require_once __DIR__ . "/server/app/controllers/usuariosControlador.php";
require_once __DIR__ . "/server/app/models/usuariosModelo.php";
require_once __DIR__ . "/server/app/models/productosModelo.php";
require_once __DIR__ . "/server/app/models/miModelo.php";

$rutas = new rutasControlador();

$rutas -> inicio();
<?php
// Función para hacer solicitudes GET con autenticación básica
function hacerPeticionGET($url, $id_cliente, $llave_secreta)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$id_cliente:$llave_secreta")
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => $response, 'code' => $http_code];
}

// Función para hacer solicitudes POST con autenticación básica
function hacerPeticionPOST($url, $datos, $id_cliente, $llave_secreta)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$id_cliente:$llave_secreta")
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => $response, 'code' => $http_code];
}

// Función para hacer solicitudes PUT con autenticación básica
function hacerPeticionPUT($url, $datos, $id_cliente, $llave_secreta)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$id_cliente:$llave_secreta"),
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => $response, 'code' => $http_code];
}

// Función para hacer solicitudes DELETE con autenticación básica
function hacerPeticionDELETE($url, $id_cliente, $llave_secreta)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$id_cliente:$llave_secreta")
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => $response, 'code' => $http_code];
}


// Credenciales de autenticación (Reemplaza esto con tus credenciales reales)
$id_cliente = 'MmhDYUdtOaB4aHN1ejJNS3RVUjJJUT09';
$llave_secreta = 'SEVnaW9aSXVvSU45SGpHOVJWbngyQUFsTlEzeXFsSDVac0g5ZFlYWlNiUXZqOEhEOWhPMmRVZUdKQ1JKMVFSQQ==';

// Prueba ruta GET /productos con autenticación
echo "Probando ruta GET /productos: \n";
$resultadoGET = hacerPeticionGET('http://localhost/store/productos', $id_cliente, $llave_secreta);
echo "<br> Código de respuesta: " . $resultadoGET['code'] . "\n";
echo "<br> Respuesta: " . $resultadoGET['response'] . "\n\n";
echo "<br><br><br><br><br>";

// Prueba ruta GET /productos con paginación
echo "Probando ruta GET /productos con paginación: \n";
$resultadoGETPag = hacerPeticionGET('http://localhost/store/productos/?page=1', $id_cliente, $llave_secreta);
echo "<br>Código de respuesta: " . $resultadoGETPag['code'] . "\n";
echo " <br> Respuesta: " . $resultadoGETPag['response'] . "\n\n";
echo "<br><br><br><br><br>";

// Prueba ruta POST /productos con autenticación
echo "Probando ruta POST /productos: \n";
echo "<br>";
$datosPOST = [
    'nombre' => 'Producto de Prueba',
    'descripcion' => 'Descripción del producto de prueba',
    'precio' => 100,
    'stock' => 50,
    'categoria' => 3,
    'descuento' => 10
];
$resultadoPOST = hacerPeticionPOST('http://localhost/store/productos', $datosPOST, $id_cliente, $llave_secreta);
echo "<br> Código de respuesta: " . $resultadoPOST['code'] . "\n";
echo "<br> Respuesta: " . $resultadoPOST['response'] . "\n\n";
echo "<br><br><br><br><br>";

// Prueba POST con datos faltantes
echo "Probando ruta POST /productos con datos faltantes: \n";
$datosPOSTIncompletos = [
    'descripcion' => 'Descripción del producto de prueba'
];
$resultadoPOSTIncompleto = hacerPeticionPOST('http://localhost/store/productos', $datosPOSTIncompletos, $id_cliente, $llave_secreta);
echo "<br>Código de respuesta: " . $resultadoPOSTIncompleto['code'] . "\n";
echo "<br> Respuesta: " . $resultadoPOSTIncompleto['response'] . "\n\n";
echo "<br><br><br><br><br>";

// Prueba de credenciales inválidas
echo "Probando ruta GET /productos con credenciales inválidas: \n";
$resultadoGETInvalido = hacerPeticionGET('http://localhost/store/productos', 'usuarioInvalido', 'llaveInvalida');
echo "<br>Código de respuesta: " . $resultadoGETInvalido['code'] . "\n";
echo "<br>Respuesta: " . $resultadoGETInvalido['response'] . "\n\n";



echo "<br><br><br><br><br>";
// Prueba ruta inexistente
echo "Probando ruta GET /productos/noexiste: \n";
$resultadoGETInexistente = hacerPeticionGET('http://localhost/store/productos/noexiste', $id_cliente, $llave_secreta);
echo "<br>Código de respuesta: " . $resultadoGETInexistente['code'] . "\n";
echo "<br>Respuesta: " . $resultadoGETInexistente['response'] . "\n\n";

echo "<br><br><br><br><br>";
// Prueba ruta inexistente
echo "Probando ruta GET /productos/noexiste: \n";
$resultadoGETInexistente = hacerPeticionGET('http://localhost/store/productos/'.$id_producto, $id_cliente, $llave_secreta);
echo "<br>Código de respuesta: " . $resultadoGETInexistente['code'] . "\n";
echo "<br>Respuesta: " . $resultadoGETInexistente['response'] . "\n\n";




echo "<br><br><br><br><br>";
// Prueba ruta PUT /productos/{id} con autenticación
echo "Probando ruta PUT /productos/$id_producto: \n";
$datosPUT = [
    'nombre' => 'Producto Actualizado',
    'precio' => 120,
    'stock' => 40,
    "categoria" => 2
];
$resultadoPUT = hacerPeticionPUT("http://localhost/store/productos/$id_producto", $datosPUT, $id_cliente, $llave_secreta);
echo "<br>Código de respuesta: " . $resultadoPUT['code'] . "\n";
echo "<br>Respuesta: " . $resultadoPUT['response'] . "\n\n";


echo "<br><br><br><br><br>";
// Prueba ruta DELETE /productos/{id} con autenticación
echo "Probando ruta DELETE /productos/$id_producto: \n";
$resultadoDELETE = hacerPeticionDELETE("http://localhost/store/productos/$id_producto", $id_cliente, $llave_secreta);
echo "<br>Código de respuesta: " . $resultadoDELETE['code'] . "\n";
echo "<br>Respuesta: " . $resultadoDELETE['response'] . "\n\n";
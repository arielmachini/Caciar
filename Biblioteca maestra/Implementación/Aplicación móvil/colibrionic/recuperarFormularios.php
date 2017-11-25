<?php

// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET");
header("Content-Type: text/plain");

/* Evitar que la información se guarde en caché */
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

error_reporting(E_ALL);
ini_set("display_errors", 1);

define("__ROOT__", dirname(__FILE__));

require_once __ROOT__ . '/lib/ObjetoDatos.class.php';
include_once __ROOT__ . '/modelo/Campos.class.php';

/* Se obtienen todos los campos del formulario desde la base de datos */
$formulariosRecibidos = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO");

/* Si no existen campos asociados al ID de formulario recibido... */
if ($formulariosRecibidos === false || $formulariosRecibidos->num_rows === 0) {
    die(); // Se cancela la operación.
}

$formulariosParseados = "[";

while ($formularioRecibido = $formulariosRecibidos->fetch_assoc()) {
    $formulariosParseados .= '{"titulo": "' . $formularioRecibido['titulo'] . '", ';
    $formulariosParseados .= '"descripcion": "' . $formularioRecibido['descripcion'] . '", ';
    $formulariosParseados .= '"emailReceptor": "' . $formularioRecibido['emailReceptor'] . '", ';
    $formulariosParseados .= '"fechaInicio": "' . $formularioRecibido['fechaInicio'] . '", ';
    $formulariosParseados .= '"fechaFin": "' . $formularioRecibido['fechaFin'] . '", ';
    $formulariosParseados .= '"fechaCreacion": "' . $formularioRecibido['fechaCreacion'] . '"}, ';
}

$formulariosParseados = substr($formulariosParseados, 0, strlen($formulariosParseados) - 2);
$formulariosParseados .= "]";

echo $formulariosParseados;

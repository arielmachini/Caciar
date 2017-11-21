<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

/* Evitar que la información se guarde en caché */
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

error_reporting(E_ALL);
ini_set("display_errors", 1);

define("__ROOT__", dirname(__FILE__));

require_once __ROOT__ . '/lib/ObjetoDatos.class.php';
include_once __ROOT__ . '/modelo/Campos.class.php';

$id = filter_input(INPUT_GET, "id");

if (!isset($id)) { // Se necesita un ID de formulario para realizar la operación.
    die();
}

/* Se obtienen todos los campos del formulario desde la base de datos */
$camposRecibidos = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM CAMPO WHERE `idFormulario` = {$id}");

/* Si no existen campos asociados al ID de formulario recibido... */
if ($camposRecibidos === false || $camposRecibidos->num_rows === 0) {
    die(); // Se cancela la operación.
}

$camposParseados = "{"; // En esta variable se van a almacenar todos los campos obtenidos.

if (count($camposRecibidos) > 1) {
    while ($campoActual = $camposRecibidos->fetch_assoc()) {
        $CampoFormulario = new CampoTexto();
        $idcampo = $campoActual['idCampo'];

        $CampoFormulario->setTitulo($campoActual['titulo']);
        $CampoFormulario->setDescripcion($campoActual['descripcion']);

        $CampoFormulario->setEsObligatorio($campoActual['esObligatorio']);

        $campoTextoRecibido = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM CAMPO_TEXTO WHERE `idCampo` = {$idcampo}");
        $pistaCampoActual = $campoTextoRecibido->fetch_assoc()['pista'];

        $CampoFormulario->setPista($pistaCampoActual);

        $codigoHTML = $CampoFormulario->getCodigo();
        $camposParseados .= "{codigo: '{$codigoHTML}'}, ";
    }
    
    $camposParseados = substr($camposParseados, 0, strlen($camposParseados) - 2);
} else { // Si el formulario sólo tiene un campo no se puede retornar un array.
    $campoActual = $camposRecibidos->fetch_assoc();
    $CampoFormulario = new CampoTexto();
    $idcampo = $campoActual['idCampo'];

    $CampoFormulario->setTitulo($campoActual['titulo']);
    $CampoFormulario->setDescripcion($campoActual['descripcion']);

    $CampoFormulario->setEsObligatorio($campoActual['esObligatorio']);

    $campoTextoRecibido = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM CAMPO_TEXTO WHERE `idCampo` = {$idcampo}");
    $pistaCampoActual = $campoTextoRecibido->fetch_assoc()['pista'];

    $CampoFormulario->setPista($pistaCampoActual);

    $codigoHTML = json_encode($CampoFormulario->getCodigo());
    $camposParseados .= "codigo: " . $codigoHTML;
}

$camposParseados .= "}";

echo $camposParseados;

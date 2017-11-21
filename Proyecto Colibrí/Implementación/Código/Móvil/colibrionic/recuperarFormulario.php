<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

/* Evitar que la información se guarde en caché */
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once './lib/ObjetoDatos.class.php';
include_once './modelo/Formulario.class.php';

$id = filter_input(INPUT_GET, "id");

if (!isset($id)) { // Se necesita un ID de formulario para realizar la operación.
    die();
}

/* Se obtiene el formulario de la base de datos */
$formularioRecibido = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO WHERE `idFormulario` = {$id}")->fetch_assoc();

/* Si no existen campos asociados al ID de formulario recibido... */
if ($formularioRecibido === false || count($formularioRecibido) === 0) {
    die(); // Se cancela la operación.
}

$FormularioRecuperado = new Formulario($formularioRecibido['fechaCreacion']);
$formularioParseado = "{"; // En esta variable se van a almacenar las propiedades del formulario obtenido.

$formularioParseado .= "titulo: '{$formularioRecibido['titulo']}', ";
$formularioParseado .= "descripcion: '{$formularioRecibido['descripcion']}', ";
$formularioParseado .= "receptor: '{$formularioRecibido['emailReceptor']}', ";
$formularioParseado .= "fechaInicio: '{$formularioRecibido['fechaInicio']}', ";
$formularioParseado .= "fechaFin: '{$formularioRecibido['fechaFin']}', ";
$formularioParseado .= "fechaCreacion: '{$formularioRecibido['fechaCreacion']}'}";

?>
<plaintext>
<?php

echo $formularioParseado;
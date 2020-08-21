<?php
/* Quién tiene permitido realizar solicitudes. */
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die();
}

require_once '../modelo/BDConexion.Class.php';
require_once '../lib/BDCatalogoTablas.Class.php';

$respuesta = json_decode(file_get_contents("php://input"), true);
$idFormulario = $respuesta["idFormulario"];

array_shift($respuesta); // Se elimina "idFormulario" del arreglo porque ya no se necesita.

$estaHabilitado = BDConexion::getInstancia()->query("" .
        "SELECT `estaHabilitado` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `idFormulario` = {$idFormulario}")->fetch_array();

if ($estaHabilitado[0] == 0) {
    /* El formulario no está habilitado, por lo tanto no puede recibir nuevas
     * respuestas.
     */
    die();
}

require_once '../lib/Colibri.Class.php';

$datosFormulario = BDConexion::getInstancia()->query("" .
        "SELECT `emailReceptor`, `titulo` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `idFormulario` = {$idFormulario}")->fetch_assoc();

$camposFormulario = BDConexion::getInstancia()->query("" .
        "SELECT `titulo` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " " .
        "WHERE `idFormulario` = {$idFormulario} " .
        "ORDER BY `posicion` ASC");

$csvRespuesta = '"' . date("d/m/Y H:i:s") . '",';

/* VARIABLES PARA EL ENVÍO DE LA RESPUESTA POR E-MAIL: */
$colibri = new Colibri($datosFormulario["emailReceptor"], $datosFormulario["titulo"]);
$arregloCamposFormulario = array();
$cuerpoMensaje = "Estimado usuario,\n\nEl formulario «{$datosFormulario["titulo"]}» tiene una nueva respuesta. A continuación se muestran los datos de dicha respuesta:\n\n";
$fueEnviada = 0;

while ($tituloCampo = $camposFormulario->fetch_array()[0]) {
    $valorCampo = $respuesta[$tituloCampo];
    
    if (is_array($valorCampo)) { // Sólo hay que realizar un tratamiento diferente para la lista de casillas de verificación.
        $csvRespuesta .= '"';
        
        if (!empty($valorCampo)) {
            $cuerpoMensaje .= $tituloCampo . ":\n";
            $enumeracionCasillasSeleccionadas = "";
            
            foreach ($valorCampo as $casilla) {
                $csvRespuesta .= str_replace('"', '""', $casilla) . ';';

                $cuerpoMensaje .= "☑ " . $casilla . "\n";
                $enumeracionCasillasSeleccionadas .= $casilla . ';';
            }

            $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);

            $cuerpoMensaje .= "\n";
            $enumeracionCasillasSeleccionadas = substr($enumeracionCasillasSeleccionadas, 0, strlen($enumeracionCasillasSeleccionadas) - 1);
            $arregloCamposFormulario[$tituloCampo] = $enumeracionCasillasSeleccionadas;
        }
        
        $csvRespuesta .= '",';
    } else {
        $arregloCamposFormulario[$tituloCampo] = $valorCampo;
        $csvRespuesta .= '"' . str_replace('"', '""', $valorCampo) . '",';
        
        if (isset($valorCampo) && trim($valorCampo) != "") {
            $cuerpoMensaje .= $tituloCampo . ":\n" . $valorCampo . "\n\n";
        }
    }
}

$csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);
$cuerpoMensaje .= "En el presente mensaje también se encuentra adjunto un documento PDF con los detalles de esta respuesta.\nRecuerde que puede acceder a todas las respuestas que registra este formulario cuando usted desee desde el gestor de formularios.";

if ($colibri->enviarMensaje($cuerpoMensaje, $arregloCamposFormulario)) {
    $fueEnviada = 1;
}

BDConexion::getInstancia()->query("" .
        "INSERT INTO " . BDCatalogoTablas::BD_TABLA_RESPUESTA . "(`idFormulario`, `csv`, `fueEnviada`) " .
        "VALUES ({$idFormulario}, '{$csvRespuesta}', {$fueEnviada})");
<?php
include_once '../lib/ControlAcceso.Class.php';

if (!(ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES))) {
    ControlAcceso::redireccionar();
}

require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

$usuario = new Usuario($_SESSION['usuario']->id);

/* Se sanitizan las variables recibidas por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);
$enCsv = filter_var(filter_input(INPUT_GET, "csv"), FILTER_SANITIZE_STRING);
$fechaDesde = filter_var(filter_input(INPUT_GET, "desde"), FILTER_SANITIZE_STRING);
$fechaHasta = filter_var(filter_input(INPUT_GET, "hasta"), FILTER_SANITIZE_STRING);

if (isset($fechaDesde) && $fechaDesde != "") {
    $fechaDesde = strtotime($fechaDesde);
}

if (isset($fechaHasta) && $fechaHasta != "") {
    $fechaHasta = strtotime($fechaHasta);
}

if ($usuario->esAdministradorDeGestores()) {
    $query = "" .
            "SELECT `csv` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_RESPUESTA . " " .
            "WHERE `idFormulario` = {$idFormulario} " .
            "ORDER BY `idRespuesta` ASC";
} else {
    $query = "" .
            "SELECT `csv` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_RESPUESTA . " `A` INNER JOIN " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " `B` " .
            "ON `A`.`idFormulario` = `B`.`idFormulario` " .
            "WHERE `idCreador` = {$_SESSION['usuario']->id} AND `A`.`idFormulario` = {$idFormulario} " .
            "ORDER BY `idRespuesta` ASC";
}

$respuestas = BDConexion::getInstancia()->query($query);

if (mysqli_num_rows($respuestas) == 0) {
    /* El formulario no registra respuestas, no existe o el usuario que intenta acceder no tiene acceso a este. */
    ControlAcceso::redireccionar("formulario.gestor.php");
}

$campos = BDConexion::getInstancia()->query("" .
        "SELECT `titulo` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " " .
        "WHERE `idFormulario` = {$idFormulario}");

$titulosCampos = array();

while ($campo = $campos->fetch_array()) {
    $titulosCampos[] = $campo[0];
}

if (isset($enCsv) && $enCsv == "true") { // Se genera un documento CSV con las respuestas al formulario.
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Respuestas.csv"');

    $cabeceraCsv = '"Marca temporal",';
    
    foreach ($titulosCampos as $tituloCampo) {
        $cabeceraCsv .= '"' . $tituloCampo . '",';
    }
    
    $cabeceraCsv = substr($cabeceraCsv, 0, strlen($cabeceraCsv) - 1);
    
    file_put_contents('php://output', $cabeceraCsv . PHP_EOL); // La cabecera del documento CSV contiene los títulos de todos los campos del formulario.
    
    while ($respuesta = $respuestas->fetch_array()) {
        file_put_contents('php://output', $respuesta[0] . PHP_EOL, FILE_APPEND);
    }
} else { // Se genera un documento PDF con las respuestas al formulario.
    require_once '../lib/FabricaPDF.php';
    
    $respuestasFormulario = array();
    $tituloFormulario = BDConexion::getInstancia()->query("" .
            "SELECT `titulo` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `idFormulario` = {$idFormulario}")->fetch_array()[0];
    
    while ($respuesta = $respuestas->fetch_array()) {
        if ((isset($fechaDesde) && $fechaDesde != "") && (isset($fechaHasta) && $fechaHasta != "")) {
            if ($fechaDesde === false || $fechaHasta === false) {
                /* El formato de una de las fechas recibidas es inválido. */
                ControlAcceso::redireccionar("formulario.gestor.php");
            }
            
            if ($fechaDesde > $fechaHasta) {
                /* Se ingresó manualmente una fecha inicial mayor a la fecha final (inválido). */
                ControlAcceso::redireccionar("formulario.gestor.php");
            }
            
            $fechaEnvioRespuesta = strtotime(str_replace("/", "-", substr($respuesta[0], 1, 10)));
            
            if ($fechaEnvioRespuesta >= $fechaDesde && $fechaEnvioRespuesta <= $fechaHasta) {
                $respuestasFormulario[] = $respuesta[0];
            }
        } else if (isset($fechaDesde) && $fechaDesde != "") {
            if ($fechaDesde === false) {
                /* El formato de la fecha recibida es inválido. */
                ControlAcceso::redireccionar("formulario.gestor.php");
            }
            
            $fechaEnvioRespuesta = strtotime(str_replace("/", "-", substr($respuesta[0], 1, 10)));
            
            if ($fechaEnvioRespuesta >= $fechaDesde) {
                $respuestasFormulario[] = $respuesta[0];
            }
        } else if (isset($fechaHasta) && $fechaHasta != "") {
            if ($fechaHasta === false) {
                /* El formato de la fecha recibida es inválido. */
                ControlAcceso::redireccionar("formulario.gestor.php");
            }
            
            $fechaEnvioRespuesta = strtotime(str_replace("/", "-", substr($respuesta[0], 1, 10)));
            
            if ($fechaEnvioRespuesta <= $fechaHasta) {
                $respuestasFormulario[] = $respuesta[0];
            }
        } else { // El usuario optó por la opción de descarga #2 (formulario.ver.detalles.php).
            $respuestasFormulario[] = $respuesta[0];
        }
    }
    
    FabricaPDF::generarPdfRespuestas($tituloFormulario, $titulosCampos, $respuestasFormulario);
}

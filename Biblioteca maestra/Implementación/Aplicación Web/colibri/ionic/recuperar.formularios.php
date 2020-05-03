<?php

header('Content-Type: text/html; charset=utf-8');

/* Se evita que los datos se guarden en caché. */
header('Cache-Control: no-cache,  must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

/* Quién tiene permitido realizar solicitudes. */
header('Access-Control-Allow-Origin: *');

require_once '../lib/Constantes.Class.php';

/* Llave de acceso, provista por el cliente. */
$llave = filter_var(filter_input(INPUT_GET, 'llave'), FILTER_SANITIZE_STRING);

/* Datos dinámicos para la llave de acceso. */
$hashFechaAyer = md5(date('dmY', strtotime("-1 days")));
$hashFechaHoy = md5(date('dmY'));

if ($llave == Constantes::LLAVE . $hashFechaHoy || $llave == Constantes::LLAVE . $hashFechaAyer) { // Si la solicitud del cliente es enviada a las 23:59, puede que sea recibida a las 0:00 del día siguiente.
    require_once '../modelo/BDConexion.Class.php';
    require_once '../lib/BDCatalogoTablas.Class.php';

    $formularios = BDConexion::getInstancia()->query("" .
            "SELECT `idFormulario`, `titulo`, `descripcion`, `fechaApertura`, `fechaCierre` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `estaHabilitado` = 1");

    if (mysqli_num_rows($formularios) == 0) {
        /* No existen formularios habilitados. */
        die();
    }
    
    $formulariosJSON = "[";

    while ($formulario = $formularios->fetch_assoc()) {
        $esAbiertoAlPublico = BDConexion::getInstancia()->query("" .
                "SELECT `idFormulario` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
                "WHERE `idFormulario` = {$formulario['idFormulario']} AND `idRol` = -1"); // NOTA: -1 equivale a la constante PermisosSistema::IDROL_PUBLICO_GENERAL en la clase ControlAcceso.Class.php. No se puede incluir acá porque se haría session_start().

        if (mysqli_num_rows($esAbiertoAlPublico) == 0) {
            /* El formulario no está abierto al público, por lo tanto se omite. */
            continue;
        }

        if ($formulario['fechaApertura'] != "") {
            if (date("Y-m-d") < $formulario['fechaApertura']) {
                /* El formulario no está habilitado, por lo tanto se omite. */
                continue;
            }
        }

        if ($formulario['fechaCierre'] != "") {
            if (date("Y-m-d") > $formulario['fechaCierre']) {
                /* El formulario no está habilitado, por lo tanto se omite. */
                continue;
            }
        }

        $formulariosJSON .= '{"idFormulario": ' . $formulario['idFormulario'] . ', ';
        $formulariosJSON .= '"titulo": "' . $formulario['titulo'] . '", ';
        $formulariosJSON .= '"descripcion": "' . $formulario['descripcion'] . '"}, ';
    }

    $formulariosJSON = substr($formulariosJSON, 0, strlen($formulariosJSON) - 2);
    $formulariosJSON .= ']';

    echo $formulariosJSON;
} else {
    echo '[]';
}
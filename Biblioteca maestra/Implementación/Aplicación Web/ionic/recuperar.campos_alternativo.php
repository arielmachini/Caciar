<?php

header('Content-Type: text/html; charset=utf-8');

/* Se evita que los datos se guarden en caché. */
header('Cache-Control: no-cache,  must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

/* Quién tiene permitido realizar solicitudes. */
header('Access-Control-Allow-Origin: *');

$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

if (!isset($idFormulario)) {
    /* No se proporcionó una ID de formulario. */
    die();
}

require_once '../colibri/lib/Constantes.Class.php';

/* Llave de acceso, provista por el cliente. */
$llave = filter_var(filter_input(INPUT_GET, 'llave'), FILTER_SANITIZE_STRING);

/* Datos dinámicos para la llave de acceso. */
$hashFechaAyer = md5(date('dmY', strtotime("-1 days")));
$hashFechaHoy = md5(date('dmY'));

if ($llave == Constantes::LLAVE . $hashFechaHoy || $llave == Constantes::LLAVE . $hashFechaAyer) { // Si la solicitud del cliente es enviada a las 23:59, puede que sea recibida a las 0:00 del día siguiente.
    require_once '../colibri/modelo/BDConexion.Class.php';
    require_once '../colibri/lib/BDCatalogoTablas.Class.php';
    require_once '../colibri/modelo/Campos.Class.php';
    
    $formulario = BDConexion::getInstancia()->query("" .
            "SELECT `idFormulario`, `fechaApertura`, `fechaCierre` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `estaHabilitado` = 1 && `idFormulario` = {$idFormulario}")->fetch_assoc();

    if (!$formulario) {
        /* El formulario no existe o está deshabilitado. */
        die();
    }
    
    $esAbiertoAlPublico = BDConexion::getInstancia()->query("" .
            "SELECT `idFormulario` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
            "WHERE `idFormulario` = {$idFormulario} AND `idRol` = -1"); // NOTA: -1 equivale a la constante PermisosSistema::IDROL_PUBLICO_GENERAL en la clase ControlAcceso.Class.php. No se puede incluir acá porque se haría session_start().
    
    if (mysqli_num_rows($esAbiertoAlPublico) == 0) {
        /* El formulario no está abierto al público. */
        die();
    }
    
    if ($formulario['fechaApertura'] != "") {
        if (date("Y-m-d") < $formulario['fechaApertura']) {
            /* El formulario no está habilitado. */
            die();
        }
    }
    
    if ($formulario['fechaCierre'] != "") {
        if (date("Y-m-d") > $formulario['fechaCierre']) {
            /* El formulario no está habilitado. */
            die();
        }
    }
    
    $camposFormulario = BDConexion::getInstancia()->query("" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " " .
            "WHERE `idFormulario` = {$idFormulario} " .
            "ORDER BY `posicion` ASC");
    
    $camposJSON = '[';

    while ($campo = $camposFormulario->fetch_assoc()) {
        $idCampo = $campo['idCampo'];
        
        $camposJSON .= '{"titulo": "' . $campo['titulo'] . '", ';
        $camposJSON .= '"descripcion": "' . $campo['descripcion'] . '", ';
        
        if ($campo['esObligatorio'] == 1) {
            $camposJSON .= '"esObligatorio": "true", ';
        } else {
            $camposJSON .= '"esObligatorio": "false", ';
        }

        /* ¿Se trata de un CAMPO DE TEXTO? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT `pista`, `subtipo` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_CAMPO_TEXTO . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $consultaPorSubtipo = $consultaPorSubtipo->fetch_assoc();
            
            $camposJSON .= '"pista": "' . $consultaPorSubtipo['pista'] . '", ';
            $camposJSON .= '"tipo": "' .substr(BDCatalogoTablas::BD_TABLA_CAMPO_TEXTO, strlen(BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS) + 1) . '", ';
            
            if ($consultaPorSubtipo['subtipo'] == CampoTexto::$CAMPO_TEXTO) {
                $camposJSON .= '"subtipo": "text"}, ';
            } else if ($consultaPorSubtipo['subtipo'] == CampoTexto::$CAMPO_NUMERICO) {
                $camposJSON .= '"subtipo": "number"}, ';
            } else { // Por descarte, se asume que es un campo para direcciones de e-mail.
                $camposJSON .= '"subtipo": "email"}, ';
            }

            continue;
        }

        /* Falso. ¿Se trata de un ÁREA DE TEXTO? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT `limiteCaracteres` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_AREA_TEXTO . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $camposJSON .= '"limiteCaracteres": ' . $consultaPorSubtipo->fetch_assoc()['limiteCaracteres'] . ', ';
            $camposJSON .= '"tipo": "' .substr(BDCatalogoTablas::BD_TABLA_AREA_TEXTO, strlen(BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS) + 1) . '"}, ';

            continue;
        }

        /* Falso. ¿Se trata de un SELECTOR DE FECHAS? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_FECHA . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $camposJSON .= '"tipo": "' .substr(BDCatalogoTablas::BD_TABLA_FECHA, strlen(BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS) + 1) . '"}, ';

            continue;
        }

        /* Falso. ¿Se trata de una LISTA DESPLEGABLE? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_LISTA_DESPLEGABLE . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $elementos = BDConexion::getInstancia()->query("" .
                    "SELECT * " .
                    "FROM " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                    "WHERE `idLista` = {$idCampo} " .
                    "ORDER BY `posicion` ASC");

            $camposJSON .= '"opciones": [';

            while ($elemento = $elementos->fetch_assoc()) {
                $camposJSON .= '"' . $elemento['textoOpcion'] . '", ';
            }
            
            $camposJSON = substr($camposJSON, 0, strlen($camposJSON) - 2);
            $camposJSON .= '], ';
            $camposJSON .= '"tipo": "' .substr(BDCatalogoTablas::BD_TABLA_LISTA_DESPLEGABLE, strlen(BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS) + 1) . '"}, ';
            
            continue;
        }

        /* Falso. ¿Se trata de una LISTA DE CASILLAS DE VERIFICACIÓN? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_LISTA_CHECKBOX . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $elementos = BDConexion::getInstancia()->query("" .
                    "SELECT * " .
                    "FROM " . BDCatalogoTablas::BD_TABLA_CHECKBOX . " " .
                    "WHERE `idLista` = {$idCampo} " .
                    "ORDER BY `posicion` ASC");
            
            $camposJSON .= '"opciones": [';

            while ($elemento = $elementos->fetch_assoc()) {
                $camposJSON .= '"' . $elemento['textoOpcion'] . '", ';
            }
            
            $camposJSON = substr($camposJSON, 0, strlen($camposJSON) - 2);
            $camposJSON .= '], ';
            $camposJSON .= '"tipo": "' .substr(BDCatalogoTablas::BD_TABLA_LISTA_CHECKBOX, strlen(BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS) + 1) . '"}, ';

            continue;
        }

        /* Falso. Se trata de una LISTA DE BOTONES DE RADIO. */
        $elementos = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_BOTON_RADIO . " " .
                "WHERE `idLista` = {$idCampo} " .
                "ORDER BY `posicion` ASC");

        $camposJSON .= '"opciones": [';

        while ($elemento = $elementos->fetch_assoc()) {
            $camposJSON .= '"' . $elemento['textoOpcion'] . '", ';
        }

        $camposJSON = substr($camposJSON, 0, strlen($camposJSON) - 2);
        $camposJSON .= '], ';
        $camposJSON .= '"tipo": "' .substr(BDCatalogoTablas::BD_TABLA_LISTA_BOTON_RADIO, strlen(BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS) + 1) . '"}, ';
    }
    
    $camposJSON = substr($camposJSON, 0, strlen($camposJSON) - 2);
    $camposJSON .= ']';

    echo $camposJSON;
} else {
    echo '[]';
}

?>
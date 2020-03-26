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
    require_once '../colibri/modelo/Formulario.Class.php';
    
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

    $formulario = new Formulario();
    
    $camposJSON = "[";

    while ($campo = $camposFormulario->fetch_assoc()) {
        /* Se guardan los atributos generales del campo. */
        $idCampo = $campo['idCampo'];
        $titulo = $campo['titulo'];
        $descripcion = $campo['descripcion'];
        $esObligatorio = $campo['esObligatorio'];
        $posicion = $campo['posicion'];

        /* ¿Se trata de un CAMPO DE TEXTO? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT `pista`, `subtipo` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_CAMPO_TEXTO . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $consultaPorSubtipo = $consultaPorSubtipo->fetch_assoc();
            $campoTexto = new CampoTexto();
            $pista = $consultaPorSubtipo['pista'];
            $subtipo = $consultaPorSubtipo['subtipo'];

            $campoTexto->setDescripcion($descripcion);
            $campoTexto->setEsObligatorio($esObligatorio);
            $campoTexto->setPista($pista);
            $campoTexto->setPosicion($posicion);
            $campoTexto->setSubtipo($subtipo);
            $campoTexto->setTitulo($titulo);

            $formulario->agregarCampo($campoTexto);

            continue;
        }

        /* Falso. ¿Se trata de un ÁREA DE TEXTO? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT `limiteCaracteres` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_AREA_TEXTO . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $areaTexto = new AreaTexto();
            $limiteCaracteres = $consultaPorSubtipo->fetch_assoc()['limiteCaracteres'];

            $areaTexto->setDescripcion($descripcion);
            $areaTexto->setEsObligatorio($esObligatorio);
            $areaTexto->setLimiteCaracteres($limiteCaracteres);
            $areaTexto->setPosicion($posicion);
            $areaTexto->setTitulo($titulo);

            $formulario->agregarCampo($areaTexto);

            continue;
        }

        /* Falso. ¿Se trata de un SELECTOR DE FECHAS? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_FECHA . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $campoFecha = new Fecha();

            $campoFecha->setDescripcion($descripcion);
            $campoFecha->setEsObligatorio($esObligatorio);
            $campoFecha->setPosicion($posicion);
            $campoFecha->setTitulo($titulo);

            $formulario->agregarCampo($campoFecha);

            continue;
        }

        /* Falso. ¿Se trata de una LISTA DESPLEGABLE? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_LISTA_DESPLEGABLE . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $listaDesplegable = new ListaDesplegable();

            $listaDesplegable->setDescripcion($descripcion);
            $listaDesplegable->setEsObligatorio($esObligatorio);
            $listaDesplegable->setPosicion($posicion);
            $listaDesplegable->setTitulo($titulo);

            $elementos = BDConexion::getInstancia()->query("" .
                    "SELECT * " .
                    "FROM " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                    "WHERE `idLista` = {$idCampo}");

            while ($elemento = $elementos->fetch_assoc()) {
                $valorElemento = $elemento['textoOpcion'];

                $listaDesplegable->agregarElemento($valorElemento);
            }

            $formulario->agregarCampo($listaDesplegable);

            continue;
        }

        /* Falso. ¿Se trata de una LISTA DE CASILLAS DE VERIFICACIÓN? */
        $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_LISTA_CHECKBOX . " " .
                "WHERE `idFormulario` = {$idFormulario} AND `idCampo` = {$idCampo}");

        if (mysqli_num_rows($consultaPorSubtipo) != 0) {
            $listaCheckbox = new ListaCheckbox();

            $listaCheckbox->setDescripcion($descripcion);
            $listaCheckbox->setEsObligatorio(false); // Este tipo de campo siempre "es opcional".
            $listaCheckbox->setPosicion($posicion);
            $listaCheckbox->setTitulo($titulo);

            $elementos = BDConexion::getInstancia()->query("" .
                    "SELECT * " .
                    "FROM " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                    "WHERE `idLista` = {$idCampo}");
            
            while ($elemento = $elementos->fetch_assoc()) {
                $valorElemento = $elemento['textoOpcion'];

                $listaCheckbox->agregarElemento($valorElemento);
            }

            $formulario->agregarCampo($listaCheckbox);

            continue;
        }

        /* Falso. Se trata de una LISTA DE BOTONES DE RADIO. */
        $listaRadio = new ListaRadio();

        $listaRadio->setDescripcion($descripcion);
        $listaRadio->setEsObligatorio($esObligatorio);
        $listaRadio->setPosicion($posicion);
        $listaRadio->setTitulo($titulo);

        $elementos = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                "WHERE `idLista` = {$idCampo}");

        while ($elemento = $elementos->fetch_assoc()) {
            $valorElemento = $elemento['textoOpcion'];

            $listaRadio->agregarElemento($valorElemento);
        }

        $formulario->agregarCampo($listaRadio);
    }
    
    foreach ($formulario->getCampos() as $campo) {
        $camposJSON .= '"' . htmlspecialchars($campo->getCodigo()) . '", ';
    }

    $camposJSON = substr($camposJSON, 0, strlen($camposJSON) - 2);
    $camposJSON .= ']';

    echo $camposJSON;
} else {
    echo '[]';
}

?>
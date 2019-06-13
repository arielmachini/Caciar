<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Formulario.Class.php';
require_once '../modelo/Usuario.Class.php';

BDConexion::getInstancia()->autocommit(true);

/* Se sanitiza la variable recibida por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

if (isset($_SESSION['usuario']->id)) {
    $usuario = new Usuario($_SESSION['usuario']->id);
    $idRol;
    $tienePermiso = false;

    $consulta = BDConexion::getInstancia()->query("" .
            "SELECT `idRol` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
            "WHERE `idFormulario` = {$idFormulario}");

    if (!$consulta) {
        /* No existe formulario con la ID recibida por GET. */
        ControlAcceso::redireccionar();
    }

    while ($idRol = $consulta->fetch_assoc()['idRol']) {
        if ($usuario->buscarRolPorId($idRol)) {
            $tienePermiso = true;

            break;
        }
    }
    
    $creador = BDConexion::getInstancia()->query("" .
                "SELECT `idCreador` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
                "WHERE `idFormulario` = {$idFormulario}")->fetch_assoc();
    
    if ($usuario->getId() == $creador['idCreador']) {
        /* Si el usuario creó el formulario, entonces puede verlo. */
        $tienePermiso = true;
    }

    if (!$tienePermiso) {
        /* El usuario no tiene permitido ver el formulario. Se cancela la carga. */
        ControlAcceso::redireccionar();
    }
} else {
    $consulta = BDConexion::getInstancia()->query("" .
            "SELECT `idRol` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
            "WHERE `idFormulario` = {$idFormulario} AND `idRol` = " . PermisosSistema::IDROL_PUBLICO_GENERAL);

    if (!$consulta) {
        /* Esto puede ocurrir por dos razones: O no existe formulario con la ID
         * recibida por GET o el formulario no es visible para el público
         * general.
         */
        ControlAcceso::redireccionar();
    }
}

$consulta = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
                "WHERE `idFormulario` = {$idFormulario}")->fetch_assoc();

if (!$consulta) {
    /* No existe formulario con la ID recibida por GET. */
    ControlAcceso::redireccionar();
}

if ($consulta['estaHabilitado'] == 0) {
    /* El formulario no está habilitado. */
    ControlAcceso::redireccionar();
}

$formulario = new Formulario($consulta['fechaCreacion']);

$formulario->setID($idFormulario);
$formulario->setFechaApertura($consulta['fechaApertura']);
$formulario->setFechaCierre($consulta['fechaCierre']);

if ($formulario->getFechaApertura() != "") {
    if (date("Y-m-d") < $formulario->getFechaApertura()) {
        /* El formulario no está habilitado. */
        ControlAcceso::redireccionar();
    }
}

if ($formulario->getFechaCierre() != "") {
    if (date("Y-m-d") > $formulario->getFechaCierre()) {
        /* El formulario no está habilitado. */
        ControlAcceso::redireccionar();
    }
}

$formulario->setEmailReceptor($consulta['emailReceptor']);
$formulario->setTitulo($consulta['titulo']);
$formulario->setDescripcion($consulta['descripcion']);
$formulario->setCantidadRespuestas($consulta['cantidadRespuestas']);

$consulta = BDConexion::getInstancia()->query("" .
        "SELECT * " .
        "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " " .
        "WHERE `idFormulario` = {$formulario->getID()} " .
        "ORDER BY `posicion` ASC");

while ($campo = $consulta->fetch_assoc()) {
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
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

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
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

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
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

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
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $listaDesplegable = new ListaDesplegable();

        $listaDesplegable->setDescripcion($descripcion);
        $listaDesplegable->setEsObligatorio($esObligatorio);
        $listaDesplegable->setPosicion($posicion);
        $listaDesplegable->setTitulo($titulo);

        $elementos = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                "WHERE `idLista` = {$idCampo} " .
                "ORDER BY `posicion` ASC");

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
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $listaCheckbox = new ListaCheckbox();

        $listaCheckbox->setDescripcion($descripcion);
        $listaCheckbox->setEsObligatorio(false); // Este tipo de campo siempre "es opcional".
        $listaCheckbox->setPosicion($posicion);
        $listaCheckbox->setTitulo($titulo);

        $elementos = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CHECKBOX . " " .
                "WHERE `idLista` = {$idCampo} " .
                "ORDER BY `posicion` ASC");

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
            "FROM " . BDCatalogoTablas::BD_TABLA_BOTON_RADIO . " " .
            "WHERE `idLista` = {$idCampo} " .
            "ORDER BY `posicion` ASC");

    while ($elemento = $elementos->fetch_assoc()) {
        $valorElemento = $elemento['textoOpcion'];

        $listaRadio->agregarElemento($valorElemento);
    }

    $formulario->agregarCampo($listaRadio);
}

/* El formulario ya fue recuperado. Ahora, se almacena en el arreglo SESSION
 * para poder acceder a sus datos más adelante.
 */
$_SESSION['formulario'] = $formulario;
?>

<html>
    <head>
        <noscript>
        <meta http-equiv="refresh" content="0; url=noscript.php">
        </noscript>

        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <script src="https://www.google.com/recaptcha/api.js?render=6LdFxpMUAAAAAKrchJP-4SR5BZrkj5-tdFxuUvsY"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - <?php echo $formulario->getTitulo(); ?></title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3><?= $formulario->getTitulo(); ?><a href="formularios.php"><button class="btn btn-outline-primary" style="float: right;"><span class="oi oi-account-logout" style="margin-right: 5px;"></span>Salir</button></a></h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Atención:</strong> Todos los campos acompañados por un asterisco (<span style="color: red; font-weight: bold;">*</span>) son obligatorios.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php if ($formulario->getDescripcion() != "") { ?>
                        <p><?= $formulario->getDescripcion(); ?></p>
                        <hr/>
                    <?php } ?>
                    <?= $formulario->getCodigo(); ?>
                    <script>
                        grecaptcha.ready(function () {
                            grecaptcha.execute('6LdFxpMUAAAAAKrchJP-4SR5BZrkj5-tdFxuUvsY', {action: 'homepage'}).then(function (token) {
                                $('input[name=g-recaptcha-response]').val(token);
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

    <script type="text/javascript" src="../lib/colibri.formularios.js"></script>
</html>

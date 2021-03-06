<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';

if (!(ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES))) {
    ControlAcceso::redireccionar();
    
    exit();
}

require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

/* Se sanitiza la variable recibida por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);
$nuevoEstado = filter_var(filter_input(INPUT_GET, "estado"), FILTER_SANITIZE_NUMBER_INT);

if ($nuevoEstado != 0 && $nuevoEstado != 1) {
    /* El estado recibido por GET no es válido. */
    ControlAcceso::redireccionar("formulario.gestor.php");
    
    exit();
}

$usuario = new Usuario($_SESSION['usuario']->id);

if ($usuario->esAdministradorDeGestores()) {
    $query = "" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "SET `estaHabilitado` = {$nuevoEstado} " .
            "WHERE `idFormulario` = {$idFormulario}";
} else {
    $gestorFormularios = BDConexion::getInstancia()->query("" .
                    "SELECT `puedePublicar` " .
                    "FROM " . BDCatalogoTablas::BD_TABLA_GESTOR_FORMULARIOS . " " .
                    "WHERE `idUsuario` = {$_SESSION['usuario']->id}")->fetch_assoc();

    $puedePublicar = $gestorFormularios['puedePublicar'];

    if ($puedePublicar == 1) {
        $query = "" .
                "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
                "SET `estaHabilitado` = {$nuevoEstado} " .
                "WHERE `idCreador` = {$_SESSION['usuario']->id} AND `idFormulario` = {$idFormulario}";
    } else {
        /* El gestor de formularios no tiene permiso para habilitar/deshabilitar formularios */
        ControlAcceso::redireccionar("formulario.gestor.php");

        exit();
    }
}

$consulta = BDConexion::getInstancia()->query($query);
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Actualizar el estado de un formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Actualizar el estado de un formulario</h3>
                </div>
                <div class="card-body">
                    <?php if ($consulta) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php if ($nuevoEstado == 1) { ?>
                                Ahora el formulario está <strong>habilitado</strong>. Si desea verlo ahora, <a class="alert-link" href="formulario.ver.php?id=<?= $idFormulario; ?>" target="_blank">haga clic aquí</a>.
                            <?php } else { ?>
                                Ahora el formulario está <strong>deshabilitado</strong>. Tenga en cuenta que no podrá ser visualizado por nadie hasta que vuelva a ser habilitado.
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar la solicitud.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="#" onclick="window.history.back()">
                        <span class="oi oi-account-logout"></span> Volver
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


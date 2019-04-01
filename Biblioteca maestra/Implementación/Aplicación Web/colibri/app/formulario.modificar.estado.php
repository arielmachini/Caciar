<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

function cancelarCarga() {
    echo("" .
    "<script type=\"text/javascript\">" .
    "window.location.replace(\"formulario.gestor.php\");" .
    "</script>");

    /**
     * Si el usuario tiene desactivado JavaScript en su navegador, de igual
     * manera se cancela la carga del formulario.
     */
    die();
}

/* Se sanitiza la variable recibida por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);
$nuevoEstado = filter_var(filter_input(INPUT_GET, "estado"), FILTER_SANITIZE_NUMBER_INT);

if ($nuevoEstado != 0 && $nuevoEstado != 1) {
    /* El estado recibido por GET no es válido. */
    cancelarCarga();
}

$usuario = new Usuario($_SESSION['usuario']->id);

if ($usuario->esAdministradorDeGestores()) {
    $query = "" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "SET `estaHabilitado` = {$nuevoEstado} " .
            "WHERE `idFormulario` = {$idFormulario}";
} else {
    $query = "" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "SET `estaHabilitado` = {$nuevoEstado} " .
            "WHERE `idCreador` = {$_SESSION['usuario']->id} AND `idFormulario` = {$idFormulario}";
}

$consulta = BDConexion::getInstancia("bdFormularios")->query($query);
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
                                Ahora el formulario está <b>habilitado</b>. Si desea verlo ahora, <a class="alert-link" href="formulario.ver.php?id=<?= $idFormulario; ?>" target="_blank">haga clic aquí</a>.
                            <?php } else { ?>
                                Ahora el formulario está <b>deshabilitado</b>. Tenga en cuenta que no podrá ser visualizado por nadie hasta que vuelva a ser habilitado.
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar la solicitud.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a href="formulario.gestor.php">
                        <button type="button" class="btn btn-primary">
                            <span class="oi oi-account-logout"></span> Volver
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


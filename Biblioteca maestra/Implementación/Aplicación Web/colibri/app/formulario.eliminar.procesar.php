<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ELIMINAR_FORMULARIOS);

require_once '../modelo/BDConexion.Class.php';

$idFormulario = filter_var(filter_input(INPUT_POST, "idFormulario"), FILTER_SANITIZE_NUMBER_INT);

BDConexion::getInstancia()->autocommit(false);
BDConexion::getInstancia()->begin_transaction();

$consulta = BDConexion::getInstancia()->query("" .
        "DELETE FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `idFormulario` = {$idFormulario}");

if (!$consulta) {
    BDConexion::getInstancia()->rollback();
}

BDConexion::getInstancia()->commit();
BDConexion::getInstancia()->autocommit(true);
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

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Eliminar formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Eliminar formulario</h3>
                </div>
                <div class="card-body">
                    <?php if ($consulta) { ?>
                        <div class="alert alert-success" role="alert">
                            El formulario fue eliminado con éxito.
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar la solicitud.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="formulario.gestor.php">
                        <span class="oi oi-account-logout"></span> Volver
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


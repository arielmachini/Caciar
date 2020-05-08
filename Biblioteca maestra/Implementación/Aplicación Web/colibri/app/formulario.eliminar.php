<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ELIMINAR_FORMULARIOS);

/*
 * ID del formulario que se quiere eliminar.
 */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

$formulario = BDConexion::getInstancia()->query("" .
                "SELECT `titulo` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
                "WHERE `idFormulario` = {$idFormulario}")->fetch_assoc();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Eliminar formulario</title>

    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>
        <div class="container">
            <form action="formulario.eliminar.procesar.php" method="post">
                <div class="card">
                    <div class="card-header">
                        <h3>Eliminar formulario</h3>
                    </div>
                    <div class="card-body">
                        <p>¿Confirma que desea eliminar el formulario "<strong><?= $formulario['titulo']; ?></strong>" junto a todas las respuestas que registra? ¡Esta acción no se puede deshacer!<br/>Si desea guardar las respuestas de este formulario antes de eliminarlo, recuerde que puede descargarlas <strong>desde <a href="formulario.ver.detalles.php?id=<?= $idFormulario; ?>" target="_blank">esta página</a></strong>.</p>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="idFormulario" class="form-control" value="<?= $idFormulario; ?>" >
                        <button type="submit" class="btn btn-outline-warning">
                            <span class="oi oi-trash"></span> Confirmar (eliminar el formulario)
                        </button>
                        <a class="btn btn-outline-danger" href="#" onclick="window.history.back()">
                            <span class="oi oi-x"></span> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>
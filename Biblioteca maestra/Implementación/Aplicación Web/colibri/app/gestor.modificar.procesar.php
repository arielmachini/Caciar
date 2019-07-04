<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

include_once '../modelo/Usuario.Class.php';
require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/ColeccionRoles.php';

$ColeccionRoles = new ColeccionRoles();
$idRolGestorFormularios;

$idUsuario = filter_var(filter_input(INPUT_POST, "idUsuario"), FILTER_SANITIZE_NUMBER_INT);

$Usuario = new Usuario($idUsuario);

BDConexion::getInstancia()->autocommit(false);
BDConexion::getInstancia()->begin_transaction();

$query = "UPDATE " . BDCatalogoTablas::BD_TABLA_GESTOR_FORMULARIOS . " " .
        "SET `cuotaCreacion` = ";

$cuotaIlimitada = filter_input(INPUT_POST, "cuotaIlimitada");

if (isset($cuotaIlimitada)) {
    $query .= "-1, ";
} else {
    $cuotaCreacion = filter_var(filter_input(INPUT_POST, "cuotaCreacion"), FILTER_SANITIZE_NUMBER_INT);
    
    $query .= "{$cuotaCreacion}, ";
}

$puedePublicar = filter_input(INPUT_POST, "puedePublicar");

if (!isset($puedePublicar)) {
    $query .= "`puedePublicar` = 0 ";
} else {
    $query .= "`puedePublicar` = 1 ";
}

$query .= "WHERE `idUsuario` = {$idUsuario}";

$consulta = BDConexion::getInstancia()->query($query);

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

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Alta de gestor de formularios</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Alta de gestor de formularios</h3>
                </div>
                <div class="card-body">
                    <?php if ($consulta) { ?>
                        <div class="alert alert-success" role="alert">
                            Operación completada con éxito.
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar la solicitud.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="gestores.php">
                        <span class="oi oi-account-logout"></span> Volver
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


<!DOCTYPE html>

<?php
header('Content-Type: text/html; charset=utf-8');

include_once '../lib/ControlAcceso.Class.php';

$idFormulario = $_SESSION['idFormulario'];
unset($_SESSION['idFormulario']);

include_once '../modelo/ColeccionRoles.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

/*
 * Se realiza esta comprobación para evitar que el usuario cree formularios
 * vacíos accediendo directamente a esta página.
 */
if (empty($_POST)) {
    ControlAcceso::redireccionar("formulario.gestor.php");
    
    exit();
}

require_once '../modelo/BDConexion.Class.php';

function sanitizar($valor_) {
    $valor_ = trim($valor_);
    $valor_ = stripslashes($valor_);
    $valor_ = htmlspecialchars($valor_);

    return $valor_;
}

BDConexion::getInstancia()->query("" .
        "SET NAMES 'utf8'");

BDConexion::getInstancia()->autocommit(false);
BDConexion::getInstancia()->begin_transaction();

$emailReceptor = sanitizar(filter_input(INPUT_POST, "destinatarioFormulario", FILTER_SANITIZE_EMAIL));
$fechaApertura = sanitizar(filter_input(INPUT_POST, "fechaAperturaFormulario"));
$fechaCierre = sanitizar(filter_input(INPUT_POST, "fechaCierreFormulario"));

if (empty($fechaApertura) && empty($fechaCierre)) {
    $consulta = BDConexion::getInstancia()->query("" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " SET " .
                "`emailReceptor` = '{$emailReceptor}', " .
                "`fechaApertura` = NULL, " .
                "`fechaCierre` = NULL " .
            "WHERE `idFormulario` = {$idFormulario}");
} else if (empty($fechaCierre)) {
    $consulta = BDConexion::getInstancia()->query("" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " SET " .
                "`emailReceptor` = '{$emailReceptor}', " .
                "`fechaApertura` = STR_TO_DATE('{$fechaApertura}', '%Y-%m-%d'), " .
                "`fechaCierre` = NULL " .
            "WHERE `idFormulario` = {$idFormulario}");
} else if (empty($fechaApertura)) {
    $consulta = BDConexion::getInstancia()->query("" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " SET " .
                "`emailReceptor` = '{$emailReceptor}', " .
                "`fechaApertura` = NULL, " .
                "`fechaCierre` = STR_TO_DATE('{$fechaCierre}', '%Y-%m-%d') " .
            "WHERE `idFormulario` = {$idFormulario}");
} else {
    $consulta = BDConexion::getInstancia()->query("" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " SET " .
                "`emailReceptor` = '{$emailReceptor}', " .
                "`fechaApertura` = STR_TO_DATE('{$fechaApertura}', '%Y-%m-%d'), " .
                "`fechaCierre` = STR_TO_DATE('{$fechaCierre}', '%Y-%m-%d') " .
            "WHERE `idFormulario` = {$idFormulario}");
}

if ($consulta) { // Si la actualización del formulario se completó exitosamente, se continúa con el procesamiento.
    $elimCorrectaFormularioRol = BDConexion::getInstancia()->query("DELETE FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " WHERE `idFormulario` = {$idFormulario}");
    
    /* Se guardan los roles de destino para el formulario. */
    $rolesDestinoFormulario = filter_input(INPUT_POST, "rolesDestinoFormulario", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    foreach ($rolesDestinoFormulario as $idRol) {
        $consulta = BDConexion::getInstancia()->query("" .
                "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
                "VALUES ({$idFormulario}, {$idRol})");
    }
    
    $ColeccionRoles = new ColeccionRoles();
    $numeroOcurrencias = 0;

    foreach ($ColeccionRoles->getRoles() as $Rol) {
        if ($Rol->getNombre() === PermisosSistema::ROL_ADMINISTRADOR_GESTORES) {
            $idAdministradorGestores = $Rol->getId();
            $numeroOcurrencias++;
        } else if ($Rol->getNombre() === PermisosSistema::ROL_ADMINISTRADOR) {
            $idAdministrador = $Rol->getId();
            $numeroOcurrencias++;
        }

        /* Si ya se encontraron los dos ID que se buscaban, se rompe el bucle. */
        if ($numeroOcurrencias === 2) {
            break;
        }
    }

    /* Los usuarios con los roles "Administrador de gestores de formularios" o
     * "Administrador" pueden ver todos los formularios.
     */
    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
            "VALUES ({$idFormulario}, {$idAdministradorGestores})");

    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
            "VALUES ({$idFormulario}, {$idAdministrador})");
    
    if ($elimCorrectaFormularioRol && $consulta) {
        BDConexion::getInstancia()->commit();
    } else {
        BDConexion::getInstancia()->rollback();
    }
} else {
    BDConexion::getInstancia()->rollback();
}
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

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Modificar formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Modificar formulario</h3>
                </div>
                <div class="card-body">
                    <?php if ($consulta) { ?>
                        <div class="alert alert-success" role="alert">
                            El formulario ha sido modificado con éxito.
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar la solicitud.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="formularios.php"><span class="oi oi-account-logout"></span> Finalizar</a> <a class="btn btn-primary" href="formulario.gestor.php"><span class="oi oi-dashboard"></span> Gestor de formularios</a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


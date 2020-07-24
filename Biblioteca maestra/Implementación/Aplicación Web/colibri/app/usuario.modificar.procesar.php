<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);

include_once '../modelo/BDConexion.Class.php';
include_once '../modelo/ColeccionRoles.php';

$DatosFormulario = $_POST;
$idUsuario = $DatosFormulario["id"];
$RolesSistema = new ColeccionRoles();

$idRolGestorFormularios;

foreach ($RolesSistema->getRoles() as $Rol) {
    if ($Rol->getNombre() === PermisosSistema::ROL_GESTOR) {
        $idRolGestorFormularios = $Rol->getId();

        break 1;
    }
}

$idRolUsuarioRegistrado;

foreach ($RolesSistema->getRoles() as $Rol) {
    if ($Rol->getNombre() === PermisosSistema::ROL_ESTANDAR) {
        $idRolUsuarioRegistrado = $Rol->getId();

        break 1;
    }
}

BDConexion::getInstancia()->autocommit(false);
BDConexion::getInstancia()->begin_transaction();

$query = "UPDATE " . BDCatalogoTablas::BD_TABLA_USUARIO . " "
        . "SET nombre = '{$DatosFormulario["nombre"]}', email = '{$DatosFormulario["email"]}' "
        . "WHERE id = {$idUsuario}";
$consulta = BDConexion::getInstancia()->query($query);
if (!$consulta) {
    BDConexion::getInstancia()->rollback();
    //arrojar una excepcion
    die(BDConexion::getInstancia()->errno);
}

$query = "DELETE FROM " . BDCatalogoTablas::BD_TABLA_USUARIO_ROL . " "
        . "WHERE id_usuario = {$idUsuario} AND (id_rol <> {$idRolUsuarioRegistrado} AND id_rol <> {$idRolGestorFormularios})";
$consulta = BDConexion::getInstancia()->query($query);
if (!$consulta) {
    BDConexion::getInstancia()->rollback();
    //arrojar una excepcion
    die(BDConexion::getInstancia()->errno);
}

foreach ($DatosFormulario["rol"] as $idRol) {
    $query = "INSERT INTO " . BDCatalogoTablas::BD_TABLA_USUARIO_ROL . " "
            . "VALUES ({$idUsuario}, {$idRol})";
    $consulta = BDConexion::getInstancia()->query($query);
    if (!$consulta) {
        BDConexion::getInstancia()->rollback();
        //arrojar una excepcion
        die(BDConexion::getInstancia()->errno);
    }
}
BDConexion::getInstancia()->commit();
BDConexion::getInstancia()->autocommit(true);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <title><?= Constantes::NOMBRE_SISTEMA; ?> - Actualizar Usuario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Actualizar Usuario</h3>
                </div>
                <div class="card-body">
                    <?php if ($consulta) { ?>
                        <div class="alert alert-success" role="alert">
                            Operación realizada con éxito.
                        </div>
                    <?php } ?>   
                    <?php if (!$consulta) { ?>
                        <div class="alert alert-danger" role="alert">
                            Ha ocurrido un error.
                        </div>
                    <?php } ?>
                    <hr />
                    <h5 class="card-text">Opciones</h5>
                    <a class="btn btn-primary" href="usuarios.php">
                        <span class="oi oi-account-logout"></span> Salir
                    </a>
                </div>
            </div>
        </div>
        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>

<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

$usuario = new Usuario($_SESSION['usuario']->id);

/* Se sanitiza la variable recibida por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

if ($usuario->esAdministradorDeGestores()) {
    $query = "" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `idFormulario` = {$idFormulario}";
} else {
    $query = "" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `idCreador` = {$_SESSION['usuario']->id} AND `idFormulario` = {$idFormulario}";
}

$formulario = BDConexion::getInstancia()->query($query);

if (mysqli_num_rows($formulario) == 0) {
    /* El formulario no existe o el usuario que intenta acceder no tiene acceso a este. */
    ControlAcceso::redireccionar("formulario.gestor.php");
}

$formulario = $formulario->fetch_assoc();

$rolesDestino = BDConexion::getInstancia()->query("" .
        "SELECT `nombre` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " JOIN " . BDCatalogoTablas::BD_TABLA_ROL . " ON " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . ".`idRol` = " . BDCatalogoTablas::BD_TABLA_ROL . ".`id` " .
        "WHERE `idFormulario` = {$idFormulario}");
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Detalles del formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Detalles del formulario</h3>
                </div>
                <div class="card-body">
                    <h4 class="card-text">Título</h4>
                    <p><?= $formulario['titulo']; ?></p>
                    <hr/>
                    <h4 class="card-text">Dirección de e-mail que recibe las respuestas</h4>
                    <p><?= $formulario['emailReceptor']; ?></p>
                    <hr/>
                    <h4 class="card-text">Descripción</h4>
                    <p>«<i><?= $formulario['descripcion']; ?></i>»</p>
                    <hr/>
                    <h4 class="card-text">Destinatarios del formulario</h4>
                    <?php while ($rol = $rolesDestino->fetch_assoc()) {
                        $nombreRol = $rol['nombre'];
                        
                        if ($nombreRol !== PermisosSistema::ROL_GESTOR && $nombreRol !== PermisosSistema::ROL_ADMINISTRADOR_GESTORES && $nombreRol !== PermisosSistema::ROL_ADMINISTRADOR) { ?>
                            <p><?= $nombreRol; ?></p>
                        <?php }
                    } ?>
                    <hr/>
                    <h4 class="card-text">Fecha de creación</h4>
                    <p><?= $formulario['fechaCreacion']; ?></p>
                    <hr/>
                    <h4 class="card-text">Fecha de apertura</h4>
                    <?php
                    $fechaApertura = $formulario['fechaApertura'];

                    if ($fechaApertura != "") {
                        ?>
                        <p><?= $fechaApertura; ?></p>
                    <?php } else { ?>
                        <p><i>No fue definida</i></p>
                    <?php } ?>
                    <hr/>
                    <h4 class="card-text">Fecha de cierre</h4>
                    <?php
                    $fechaCierre = $formulario['fechaCierre'];

                    if ($fechaCierre != "") {
                        ?>
                        <p><?= $fechaCierre; ?></p>
                    <?php } else { ?>
                        <p><i>No fue definida</i></p>
                    <?php } ?>
                    <hr/>
                    <h4 class="card-text">Cantidad de respuestas</h4>
                    <?php
                    $cantidadRespuestas = $formulario['cantidadRespuestas'];

                    if ($cantidadRespuestas > 0) {
                        ?>
                        <p><?= $cantidadRespuestas; ?> respuestas</p>
                    <?php } else { ?>
                        <p><i>Sin respuestas</i></p>
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


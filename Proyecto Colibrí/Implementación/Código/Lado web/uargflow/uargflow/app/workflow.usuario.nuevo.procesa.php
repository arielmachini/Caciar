<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);

$mensaje = "El registro ha sido agregado con exito.";
$WorkflowRoles = new WorkflowRoles();

ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();
if (isset($_POST['nombre'])) {
    try {
        ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "INSERT INTO " . Constantes::BD_USERS . ".USUARIO "
                . "VALUES (NULL, '{$_POST['email']}', '{$_POST['nombre']}', 'Google', '{$_POST['estado']}', {$_POST['idSec']})");
    } catch (Exception $exc) {
        $mensaje = "Ha ocurrido un error. "
                . "Codigo de error MYSQL: " . $exc->getCode() . ". ";
        ObjetoDatos::getInstancia()->rollback();
        
    }
}
$idusuario = ObjetoDatos::getInstancia()->insert_id;

if (isset($_POST["idrol"])) {
    try {
        ObjetoDatos::getInstancia()->ejecutarQuery("" .
                "INSERT " .
                "INTO " . Constantes::BD_USERS . ".USUARIO_ROL " .
                "VALUES " .
                "({$_POST["idrol"]}, {$idusuario})");
    } catch (Exception $exc) {
        $mensaje2 = "Ha ocurrido un error. "
                . "Codigo de error MYSQL: " . $exc->getCode() . ". ";
        ObjetoDatos::getInstancia()->rollback();
    }
}

ObjetoDatos::getInstancia()->commit();
?>




<html>
    <head>
        <script>function alerta() {
                alert("<?php echo $mensaje; ?>");
            }
        </script>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
    </head>
    <body onload="alerta();">
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Alta de Usuario</h3>
                    <p><?php echo $mensaje; ?></p>
                    <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.usuario.nuevo.php">
                            <input type="button" value="Agregar Otro" />
                        </a>
                        <a href="workflow.usuarios.ver.php">
                            <input type="button" value="Ver Usuarios" />
                        </a>
                    </fieldset>    
                    
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>

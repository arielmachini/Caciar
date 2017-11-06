<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);

$mensaje = "El registro ha sido agregado con exito.";
$WorkflowPermisos = new WorkflowPermisos();


ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();
if (isset($_POST['nombre'])) {
    try {
        ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "INSERT INTO " . Constantes::BD_USERS . ".ROL "
                . "VALUES (NULL,'" . $_POST['nombre'] . "')");
    } catch (Exception $exc) {
        $mensaje = "Ha ocurrido un error. "
                . "Codigo de error MYSQL: " . $exc->getCode() . ". ";
    }
}
$idrol = ObjetoDatos::getInstancia()->insert_id;

foreach ($WorkflowPermisos->getPermisos() as $WorkflowPermiso) {
    try {
        if (isset($_POST["permiso_" . $WorkflowPermiso->getIdPermiso()])) {
            ObjetoDatos::getInstancia()->ejecutarQuery("" .
                    "INSERT " .
                    "INTO " . Constantes::BD_USERS . ".ROL_PERMISO " .
                    "VALUES " .
                    "({$idrol}, {$WorkflowPermiso->getIdPermiso()})");
        }
    } catch (Exception $exc) {
        $mensaje = "La creación del Rol ha fallado. "
                . "Código de error MYSQL: " . $exc->getCode() . ". ";
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
                    <h3>Alta de Rol</h3>
                    <p><?php echo $mensaje; ?></p>
                    <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.rol.nuevo.php">
                            <input type="button" value="Agregar Otro" />
                        </a>
                        <a href="workflow.roles.ver.php">
                            <input type="button" value="Ver Roles" />
                        </a>
                    </fieldset>    
                </div>
            </article>
        </section>
<?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
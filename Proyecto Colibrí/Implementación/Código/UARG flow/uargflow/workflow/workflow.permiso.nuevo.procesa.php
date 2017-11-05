<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$mensaje = "El registro ha sido agregado con exito";
if (isset($_POST['nombre'])) {
    try {
        ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "INSERT INTO " . Constantes::BD_USERS . ".PERMISO "
                . "VALUES (NULL,'" . $_POST['nombre'] . "')");
    } catch (Exception $exc) {
        $mensaje = "La creacion del permiso ha fallado. "
                . "Código de error MYSQL: " . $exc->getCode() . ". ";
             
    }
}
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
                    <h3>Alta de Permiso</h3>
                    <p><?php echo $mensaje;?></p>
                    <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.permiso.nuevo.php">
                            <input type="button" value="Agregar Otro" />
                        </a>
                        <a href="workflow.permisos.ver.php">
                            <input type="button" value="Ver Permisos" />
                        </a>
                    </fieldset>                    
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
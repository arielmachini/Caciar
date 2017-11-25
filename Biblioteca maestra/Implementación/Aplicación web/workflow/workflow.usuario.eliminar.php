<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
?>
<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
    </head>
    <body onload="document.getElementById('nombre').focus()">
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Eliminar Usuario</h3>
                    <?php
                    $mensaje = "Registro eliminado con exito";
                    $UsuarioWorkflow = new WorkflowUsuario($_GET['id']);
                    try {
                        $UsuarioWorkflow->eliminar();
                    } catch (Exception $ex) {
                        $mensaje = "Ha ocurrido un error : {$ex->getCode()}";
                    }
                    ?>
                    <p><?= $mensaje; ?></p>
                    <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.usuarios.ver.php"><input type="button" value="Ir a Usuarios" /></a>
                    </fieldset>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
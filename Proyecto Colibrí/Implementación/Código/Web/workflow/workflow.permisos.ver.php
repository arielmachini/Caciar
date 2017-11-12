<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$PermisosWorkflow = new WorkflowPermisos();
?>
<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Gesti&oacute;n de Usuarios</h3>
                    <h4>Permisos del Sistema</h4>
                    <p>A continuaci&oacute;n se encuentran los permisos del Sistema.</p>
                    <p>
                    <fieldset>
                        <legend>Opciones</legend>                        
                        <a href="workflow.permiso.nuevo.php">
                            <input type="button" value="Nuevo Permiso" title="Agregar Nuevo Permiso" />
                        </a>
                    </fieldset>
                    </p>
                    <table class="tablaDatos">
                        <thead>
                            <tr>
                                <th style="width: 85%">Permiso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($PermisosWorkflow->getPermisos() as $WorkflowPermiso) {
                                ?>
                                <tr>                                        
                                    <td><?= $WorkflowPermiso->getNombre(); ?></td>
                                    <td>
                                        <!--<img src="../imagenes/abm_ver.png" />-->
                                        <a href="workflow.permiso.eliminar.php?id=<?= $WorkflowPermiso->getIdPermiso(); ?>" onclick="return confirm('Seguro que desea eliminar el Permiso <?= $WorkflowPermiso->getNombre(); ?>?');">
                                            <img src="../imagenes/abm_eliminar.png"/>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>                
                    <p>&nbsp;</p>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
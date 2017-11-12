<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$UsuariosWorkflow = new WorkflowUsuarios();
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
                    <h4>Usuario del Sistema</h4>
                    <p>A continuaci&oacute;n se encuentran los usuarios del Sistema.</p>
                    <p>
                    <fieldset>
                        <legend>Opciones</legend>                        
                        <a href="workflow.usuario.nuevo.php">
                            <input type="button" value="Nuevo Usuario" title="Agregar Nuevo Usuario" />
                        </a>
                    </fieldset>
                    </p>
                    <table class="tablaDatos">
                        <thead>
                            <tr>
                                <th style="width: 85%">Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($UsuariosWorkflow->getUsuarios() as $WorkflowUsuario) {
                                ?>
                                <tr>                                        
                                    <td><?= $WorkflowUsuario->getNombre(); ?></td>
                                    <td>
                                        <a href="workflow.usuario.ver.php?id=<?= $WorkflowUsuario->getIdUsuario(); ?>">
                                            <img src="../imagenes/abm_ver.png" />
                                        </a>
                                        <a href="workflow.usuario.eliminar.php?id=<?= $WorkflowUsuario->getIdUsuario();?>" onclick="return confirm('Seguro que desea eliminar el Usuario <?= $WorkflowUsuario->getNombre(); ?>?');">
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
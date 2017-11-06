<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$RolesWorkflow = new WorkflowRoles();
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
                    <h4>Roles del Sistema</h4>
                    <p>Los Roles corresponden a los tipos de usuarios del sistema, de acuerdo a sus funciones en la Estructura Administrativa de la UARG.</p>
                    <p>
                    <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.rol.nuevo.php" title="Agregar nuevo Rol">
                            <input type="button" value="Nuevo Rol" />
                        </a>
                    </fieldset>
                    </p>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 85%">Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($RolesWorkflow->getRoles() as $WorkflowRol) {
                                ?>
                                <tr>                                        
                                    <td><?= $WorkflowRol->getNombre(); ?></td>
                                    <td>
                                        <a href="workflow.rol.ver.php?id=<?= $WorkflowRol->getIdRol(); ?>">
                                            <img src="../imagenes/abm_ver.png" title="Ver/Editar" />
                                        </a>
                                        <a href="workflow.rol.eliminar.php?id=<?= $WorkflowRol->getIdRol(); ?>" onclick="return confirm('Seguro que desea eliminar el Rol <?= $WorkflowRol->getNombre(); ?>?');">
                                            <img src="../imagenes/abm_eliminar.png"/>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
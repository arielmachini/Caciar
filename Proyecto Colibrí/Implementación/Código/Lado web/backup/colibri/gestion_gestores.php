<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$UsuariosWorkflow = new WorkflowUsuarios();
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?>: Administrar gestores de formularios</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="colibri.js"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet"/>
    </head>

    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Administrar gestores de formularios</h3>
                    <p>A continuación se muestran los gestores de formularios <strong>actualmente registrados en el sistema Colibrí</strong>. Puede realizar todas las tareas de gestión sobre los mismos a través de esta página.</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: center">Nombre completo</th>
                                <th style="text-align: center">Dirección de correo-e</th>
                                <th style="text-align: center">Formularios que puede crear</th>
                                <th style="text-align: center">Libertad de publicación</th>
                                <th style="text-align: center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hayGestores = false;

                            foreach ($UsuariosWorkflow->getUsuarios() as $WorkflowUsuario) {
                                if ($WorkflowUsuario->poseeRol(5)) {
                                    $hayGestores = true; // Se encontró al menos un gestor de formularios.
                                    $GestorFormularios = new GestorFormularios($WorkflowUsuario->getIdUsuario()); // Ya conociendo que el rol del usuario es "Gestor de formularios" se puede instanciar como tal para acceder a sus datos de gestión.
                                    ?>
                                    <tr>
                                        <td style="text-align: center"><?= $GestorFormularios->getNombre(); ?></td>
                                        <td style="text-align: center"><?= $GestorFormularios->getEmail(); ?></td>
                                        <td style="text-align: center"><strong><?= $GestorFormularios->getLimite() ?></strong></td>
                                        <td style="text-align:center"><?= $GestorFormularios->getLibertad() ?></td>
                                        <td style="text-align: center"><img onclick="abrirDialogoModificacion('<?= $GestorFormularios->getIdUsuario() ?>')" src="../imagenes/gestor_editar.png" style='cursor:pointer' title="Editar los detalles de gestión de formularios de <?= $WorkflowUsuario->getNombre() ?>"/></a> <a href="BajaGestor.php?idusuario=<?= $WorkflowUsuario->getIdUsuario() ?>" onclick="return confirm('¿Está seguro de que desea revocarle los permisos de gestión de formularios a <?= $GestorFormularios->getNombre() ?> ?');" target="_self"><img src="../imagenes/gestor_revocar_permisos.png" title="Revocar a <?= $WorkflowUsuario->getNombre() ?> el permiso de gestionar formularios"/></a></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php if ($hayGestores === false) { ?>
                        <br/><div style="text-align: center; width: 100%"><span style="font-size: 14px; width: 70%"><img alt="/!\\" src='../imagenes/informacion.png'/> No se encontró ningún usuario con el rol de gestor de formularios.</span></div>
                    <?php } ?>
                    <br/><div style='align-items: center; display: flex; justify-content: flex-end; width: 100%'><input onclick="abrirDialogoBusquedaAlta();" style='height: 35px' type="button" value="Dar de alta a un nuevo gestor de formularios"/></div>
                </div>
            </article>
        </section>
        <?php
        include_once '../gui/GUIfooter.php';
        ?>
    </body>
</html>

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
        <script src="../lib/validador.js" type="text/javascript"></script>
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
                                <th style="text-align: center">UID</th>
                                <th style="text-align: center">Nombre completo</th>
                                <th style="text-align: center">Dirección de correo-e</th>
                                <th style="text-align: center">Formularios que puede crear</th>
                                <th style="text-align: center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hayGestores = false;

                            foreach ($UsuariosWorkflow->getUsuarios() as $WorkflowUsuario) {
                                if ($WorkflowUsuario->poseeRol(5)) {
                                    $hayGestores = true; // Se encontró al menos un gestor de formularios.
                                    ?>
                                    <tr>
                                        <td style="text-align: center"><?= $WorkflowUsuario->getIdUsuario(); ?></td>
                                        <td style="text-align: center"><?= $WorkflowUsuario->getNombre(); ?></td>
                                        <td style="text-align: center"><?= $WorkflowUsuario->getEmail(); ?></td>
                                        <td style="text-align: center"><strong>
                                                <?php if ($limite == -1) { ?>
                                                    Sin límite
                                                <?php } else { ?>
                                                    <?php
                                                    // Ejecución de ver cantidad formularios. Crear usuario Gestor en clase Workflow?
                                                }
                                                ?>
                                            </strong></td>
                                        <td style="text-align: center"><img src="../imagenes/gestor_editar.png" title="Editar los detalles de gestión de formularios de <?= $WorkflowUsuario->getNombre() ?>"/> <img src="../imagenes/gestor_revocar_permisos.png" title="Revocar a <?= $WorkflowUsuario->getNombre() ?> el permiso de gestionar formularios"/></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php if ($hayGestores == false) { ?>
                        <p>No se encontró ningún usuario con el rol de gestor de formularios.</p>
                    <?php } ?>
                    <br/><input onclick="abrirDialogoBusquedaAlta();" type="button" value="Dar de alta a un nuevo gestor de formularios"/>
                </div>
            </article>
        </section>
        <?php
        include_once '../gui/GUIfooter.php';
        ?>
    </body>
</html>

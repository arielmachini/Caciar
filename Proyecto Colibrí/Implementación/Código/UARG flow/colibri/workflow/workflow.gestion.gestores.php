<?php
    include_once '../lib/ControlAcceso.class.php';
    include_once '../modelo/Workflow.class.php';
    ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
    $UsuariosWorkflow = new WorkflowUsuarios();
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?>: Gestión de gestores de formularios</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet"/>
    </head>

    <body>
        <?php include_once '../gui/GUImenu.php';?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Administrar gestores de formularios</h3>
                    <h4>Usuarios registrados en el sistema Colibrí</h4>
                    <p>Test</p>
                    <table>
                        <thead>
                            <tr>
                                <th>UID</th>
                                <th>Nombre completo</th>
                                <th>Dirección de correo-e</th>
                                <th>Gestor de formularios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($UsuariosWorkflow->getUsuarios() as $WorkflowUsuario) {
                                    $hayGestores = false;

                                    if ($WorkflowUsuario->poseeRol(5)) {
                                        $hayGestores = true; ?>
                                        <tr>
                                            <td><?=$WorkflowUsuario->getIdUsuario(); ?></td>
                                            <td><?=$WorkflowUsuario->getNombre() ;?></td>
                                            <td><?=$WorkflowUsuario->getEmail(); ?></td>
                                            <td>
                                            No
                                            <label class="cajaSwitch">
                                                <input type="checkbox">
                                                <span class="switchSlider redondeado"></span>
                                            </label>
                                            Sí
                                            </td>
                                        </tr>
                            <?php
                                    }                       
                                }
                            
                                if ($hayGestores == false) { ?>
                                        <tr>No se encontró ningún usuario con el rol de gestor de formularios.</tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
        <?php
            include_once '../gui/GUIfooter.php';
        ?>
    </body>
</html>

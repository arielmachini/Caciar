<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$UsuariosWorkflow = new WorkflowUsuarios();
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA ?>: Búsqueda de usuarios</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet"/>
    </head>
    <body>
        <section id="main-content">
            <article>
                <div class="content">

                    <?php $email = filter_input(INPUT_GET, email); ?>
                    <h4>Búsqueda de usuarios</h4>
                    <form action="?" autocomplete="off" method="get" style='width: 100%'>
                        <input type="text" name="email" placeholder="Ingrese el correo electrónico de la persona que quiere encontrar..." style="height: 30px; vertical-align:top; width:92%" value="<?= $email ?>"/><button type="submit" style='height: 29px; width: 8%'><img src="../imagenes/buscar.png"/></button>
                    </form>

                    <?php
                    if (isset($email)) {
                        if ($email == "") {
                            ?>
                            <div style="text-align: center; width: 100%"><span style="font-size: 14px; width: 70%"><img alt="/!\\" src='../imagenes/atencion.png'/> <strong>Error:</strong> No ingresó ninguna dirección de correo electrónico para buscar.</span></div>
                        <?php } else { ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="text-align: center">Nombre completo</th>
                                        <th style="text-align: center">Dirección de correo-e</th>
                                        <th style="text-align: center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $hayResultados = false;

                                    foreach ($UsuariosWorkflow->getUsuariosComunes($email) as $WorkflowUsuario) {
                                        $hayResultados = true;
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?= $WorkflowUsuario->getNombre(); ?></td>
                                            <td style="text-align: center"><?= $WorkflowUsuario->getEmail(); ?></td>
                                            <td style="text-align: center"><a href="alta_gestor.php?idusuario=<?= $WorkflowUsuario->getIdUsuario() ?>" target="_self"><img src="../imagenes/seleccionar.png" title="Seleccionar a <?= $WorkflowUsuario->getNombre() ?> (UID <?= $WorkflowUsuario->getIdUsuario() ?>) como nuevo gestor de formularios"/></a></td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                            <?php if ($hayResultados == false) { ?>
                                <br/><div style="text-align: center; width: 100%"><span style="font-size: 14px; width: 70%">No se han encontrado personas que contengan "<strong><?= $email ?></strong>" en su dirección de correo electrónico.</span></div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </article>
        </section>
        <div style="text-align: center; width: 100%"><span style="font-size: 14px; width: 70%"><img alt="(i)" src='../imagenes/informacion.png'/> <strong>Nota:</strong> La persona a dar de alta como gestor de formularios debe estar registrada en el sistema Colibrí.</style></div>
    </body>
</html>
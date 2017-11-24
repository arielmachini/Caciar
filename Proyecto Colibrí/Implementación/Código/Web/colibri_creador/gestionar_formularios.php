<?php
include_once '../lib/Constantes.class.php';
require_once '../lib/ControlAcceso.class.php';
require_once '../lib/ObjetoDatos.class.php';

ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= Constantes::NOMBRE_SISTEMA ?> ~ Gestionar mis formularios</title>

        <script src="../colibri/colibri.js" type="text/javascript"></script>
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Administrar mis formularios</h3>
                    <p>Desde esta página puede gestionar formularios que estén <strong>bajo su responsabilidad</strong>.</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: center">Título del formulario</th>
                                <th style="text-align: center">Fecha de creación</th>
                                <th style="text-align: center">Visibilidad</th>
                                <!-- <th style="text-align: center">Respuestas</th> -->
                                <th style="text-align: center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $formulariosUsuario = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO WHERE `idCreador` = 3"); // Cambiar

                            if ($formulariosUsuario === false) {
                                ?>
                            </tbody>
                        </table>
                        <br/><div style="text-align: center; width: 100%"><span style="font-size: 14px; width: 70%"><img alt="/!\\" src='../imagenes/informacion.png'/> Todavía no tiene formularios bajo su responsabilidad.</span></div>
                        <?php
                    } else {
                        while ($formularioActual = $formulariosUsuario->fetch_assoc()) {
                            ?>
                            <tr>
                                <td style="text-align: center"><?= $formularioActual['titulo']; ?></td>
                                <td style="text-align: center"><?= $formularioActual['fechaCreacion']; ?></td>
                                <td style="text-align: center">
                                    <?php
                                    $estaHabilitado = $formularioActual['estaHabilitado'];
                                    $fechaInicio = $formularioActual['fechaInicio'];
                                    $fechaFin = $formularioActual['fechaFin'];

                                    if ($estaHabilitado == 0) {
                                        ?>
                                        No publicado
                                    <?php } else if ($fechaInicio > date("Y-m-d") || date("Y-m-d") > $fechaFin) { ?>
                                        Publicado e invisible
                                        <?php
                                    } else {
                                        ?>
                                        Publicado
                                    <?php } ?>
                                </td>
                                <!-- <td style="text-align:center"><?= $formularioActual['cantidadRespuestas'] ?></td> -->
                                <td style="text-align: center"><img onclick="window.open('./editar_formulario.php?id=<?= $formularioActual['idFormulario'] ?>', '_self');" src="../imagenes/gestor_editar.png" style='cursor: pointer' title="Editar este formulario"/></a> <a href="baja_formulario.php?id=<?= $formularioActual['idFormulario'] ?>" onclick="return confirm('¿Está seguro de que desea eliminar el formulario bajo el título \"<?= $formularioActual['titulo'] ?>\"?');" target="_self"><img src="../imagenes/gestor_revocar_permisos.png" title="Eliminar este formulario"/></a></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                        </table>
                        <?php
                    }
                    ?>
                    <br/><div style='align-items: center; display: flex; justify-content: flex-end; width: 100%'><input onclick="window.open('./crear_formulario.php', '_self');" style='height: 35px' type="button" value="Crear un nuevo formulario"/></div>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>

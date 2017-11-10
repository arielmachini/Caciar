<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA ?>: Modificación de gestor de formularios</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet"/>
    </head>
    <body>
        <section id="main-content">
            <article>
                <div class="content">

                    <?php
                    $idusuario = filter_input(INPUT_GET, idusuario);
                    $_SESSION['idusuario'] = $idusuario;
                    $GestorFormularios = new GestorFormularios($idusuario);
                    ?>
                    <h3>Modificación de gestor de formularios</h3>
                    <p>Está modificando los detalles de gestión de formularios de <strong><?= $GestorFormularios->getNombre() ?></strong> (UID <strong><?= $GestorFormularios->getIdUsuario() ?></strong>). Cuando haya terminado haga clic en el botón "<strong>Guardar cambios</strong>".</p>
                    <form action="ModificacionGestor.php" method="post" onload="revisarInputLimiteManual('limite')" style='width: 100%'>
                        <strong style='display: inline-block; padding-bottom: 5px'>Correo electrónico:</strong><br/>
                        <input title="El correo electrónico institucional se obtiene automáticamente" type="email" disabled="true" style="width: 60%" value="<?= $GestorFormularios->getEmail() ?>"/><br/><br/>
                        <strong>Límite de creación de formularios:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Define cuántos cuántos formularios podrá crear el nuevo gestor de formularios. <i>La cantidad puede ser ilimitada</i>.</span><br/>
                        <?php if ($GestorFormularios->getLimite() == "Sin límite") { ?>
                            <div><input disabled="true" id="limiteCreacion" name="limite" style='width: 60%' type="number" min="1" max="32767" placeholder="Cantidad mínima: 1" style='width: 60%'/> <input checked="true" name="sinlimite" onclick="document.getElementById('limiteCreacion').disabled = this.checked;" style='vertical-align: middle' type="checkbox" value="Sin límite"/> <span style='font-size: 13px; vertical-align:middle'><strong>Sin límite</strong></span></div><br/>
                        <?php } else { ?>
                            <div><input id="limiteCreacion" name="limite" style='width: 60%' type="number" min="1" max="32767" placeholder="Cantidad mínima: 1" style='width: 60%' value="<?= $GestorFormularios->getLimite() ?>"/> <input name="sinlimite" onclick="document.getElementById('limiteCreacion').disabled = this.checked;" style='vertical-align: middle' type="checkbox" value="Sin límite"/> <span style='font-size: 13px; vertical-align:middle'><strong>Sin límite</strong></span></div><br/>
                        <?php } ?>
                        <strong>Libre publicación:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Define si el gestor de formularios podrá publicar sus formularios sin previa revisión de un administrador del sistema.</span><br/>
                        <?php if ($GestorFormularios->getLibertad() == "Sí") { ?>
                            <select name="libertad" style='width: 60%'>
                                <option value="Sí" selected="selected">Sí</option>
                                <option value="No">No</option>
                            </select><br/><br/>
                        <?php } else { ?>
                            <select name="libertad" style='width: 60%'>
                                <option value="Sí">Sí</option>
                                <option value="No" selected="selected">No</option>
                            </select><br/><br/>
                        <?php } ?>
                        <input type="submit" value="Guardar cambios" style="height: 40px; width: 30%"/><input onclick="window.close();" type="button" value="Cancelar" style="height: 40px; width: 30%"/>
                    </form>
                </div>
            </article>
        </section>
    </body>
</html>
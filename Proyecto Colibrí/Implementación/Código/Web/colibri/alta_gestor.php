<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$UsuariosWorkflow = new WorkflowUsuarios();
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA ?>: Alta de gestor de formularios</title>
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
                    $GestorFormularios = $UsuariosWorkflow->getUsuario($idusuario);
                    ?>
                    <h3>Alta de gestor de formularios</h3>
                    <p>Rellene el siguiente formulario para dar de alta al usuario <strong><?= $GestorFormularios->getNombre() ?></strong> (UID <strong><?= $GestorFormularios->getIdUsuario() ?></strong>) como gestor de formularios.</p>
                    <form action="AltaGestor.php" method="post" onload="revisarInputLimiteManual('limite')" style='width: 100%'>
                        <strong style='display: inline-block; padding-bottom: 5px'>Correo electrónico:</strong><br/>
                        <input title="El correo electrónico institucional se obtiene automáticamente" type="email" disabled style="width: 60%" value="<?= $GestorFormularios->getEmail() ?>"/><br/><br/>
                        <strong>Límite de creación de formularios:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Define cuántos cuántos formularios podrá crear el nuevo gestor de formularios. <i>La cantidad puede ser ilimitada</i>.</span><br/>
                        <div><input id="limiteCreacion" name="limite" style='width: 60%' type="number" min="1" max="32767" placeholder="Cantidad mínima: 1" style='width: 60%'/> <input name="sinlimite" onclick="document.getElementById('limiteCreacion').disabled = this.checked;" style='vertical-align: middle' type="checkbox" value="Sin límite"/> <span style='font-size: 13px; vertical-align:middle'><strong>Sin límite</strong></span></div><br/>
                        <strong>Libre publicación:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Define si el gestor de formularios podrá publicar sus formularios sin previa revisión de un administrador del sistema.</span><br/>
                        <select name="libertad" style='width: 60%'>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select><br/><br/>
                        <input type="submit" value="Dar de alta" style="height: 40px; width: 30%"/><input onclick="window.history.back();" type="button" value="Volver" style="height: 40px; width: 30%"/>
                    </form>
                </div>
            </article>
        </section>
    </body>
</html>
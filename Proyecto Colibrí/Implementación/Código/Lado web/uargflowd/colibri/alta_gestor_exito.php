<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA ?>: ¡Éxito!</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet"/>
    </head>
    <body>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>¡Éxito!</h3>
                    <p><strong>El gestor de formularios fue agregado con éxito al sistema</strong>. ¿Qué desea hacer a continuación?</p>
                    <input onclick="window.open('alta_gestor_busqueda.php', '_self');" type="button" value="Dar de alta a otro gestor de formularios" style="height: 40px; width: 100%"/><br/>
                    <input onclick="window.close();" type="button" value="Terminar" style="height: 40px; width: 100%"/>
                </div>
            </article>
        </section>
    </body>
</html>
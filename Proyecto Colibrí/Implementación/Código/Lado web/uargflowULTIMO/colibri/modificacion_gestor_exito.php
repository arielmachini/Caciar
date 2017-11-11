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
                    <p><strong>Sus cambios fueron guardados con éxito</strong>. Al cerrar esta ventana podrá ver reflejados los cambios en la pantalla de administración de gestores de formularios.</p>
                    <input onclick="window.close();" type="button" value="Cerrar ventana" style="height: 40px; width: 100%"/>
                </div>
            </article>
        </section>
    </body>
</html>
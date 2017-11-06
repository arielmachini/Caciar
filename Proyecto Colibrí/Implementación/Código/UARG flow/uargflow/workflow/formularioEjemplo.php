<?php
    include_once '../lib/ControlAcceso.class.php';
    ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CONSULTAR);
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?>: Solicitud de certificado de alumno regular</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet"/>
    </head>

    <body>
        <?php include_once '../gui/GUImenu.php';?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Certificado de alumno regular</h3>
                    <p>A través de este formulario podrá solicitar certificados de alumno regular.</p>
                    <form action="../lib/procesarFormulario.php" method="post">
                        Nombre completo:<br>
                        <input type="text" name="nombrecompleto"><br>
                        <br>
                        Dirección de e-mail:<br>
                        <input type="text" name="direccionemail"><br>
                        <br>
                        Cantidad de certificados:<br>
                        <input type="number" name="cantidadcertificados" min="1" max="3"><br>
                        Número de DNI:<br>
                        <input type="number" name="dni"><br>
                        <br>
                        Carrera:<br>
                        <input type="text" name="carrera"><br>
                        <br>
                        <input type="submit" name="enviar" value="Enviar">
                    </form>
                </div>
            </article>
        </section>
        <?php
            include_once '../gui/GUIfooter.php';
        ?>
    </body>
</html>

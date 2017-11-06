<?php
    error_reporting(-1);
    ini_set('display_errors', 'On');
    set_error_handler("var_dump");

    include_once 'ControlAcceso.class.php';
    require('fpdf.php');

    if(isset($_POST['submit'])){
        $to = "arielmachini.pruebas@gmail.com";
        $from = "Sistema Colibrí";
        $nombrecompleto = $_POST['nombrecompleto'];
        $direccionemail = $_POST['direccionemail'];
        $cantidadcertificados = $_POST['cantidadcertificados'];
        $dni = $_POST['dni'];
        $carrera = $_POST['carrera'];
        $subject = "Solicitud de certificado de alumno regular";
        $subject2 = "Copia de su solicitud";
        $message = $nombrecompleto . " hizo una nueva solicitud en \"Certificado de alumno regular\":" . "\n\n";
        $message2 = "Este mensaje contiene adjunto una copia de la solicitud enviada.";

        $headers = "From:" . $from;
        $headers2 = "From:" . $to;
        mail($to,$subject,$message,$headers);
        mail($emailusuario,$subject2,$message2,$headers2);
        echo $nombre . ", su mensaje fue enviado con éxito.";
    }
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
                    <p>Formulario enviado con éxito.</p>
                </div>
            </article>
        </section>
        <?php
            include_once '../gui/GUIfooter.php';
        ?>
    </body>
</html>

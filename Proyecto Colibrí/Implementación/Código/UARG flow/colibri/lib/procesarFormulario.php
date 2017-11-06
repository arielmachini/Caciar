<?php
    error_reporting(-1);
    ini_set('display_errors', 'On');
    set_error_handler("var_dump");

    include_once 'ControlAcceso.class.php';
    require('fpdf.php');
    
    $mail1 = false;
    $mail2 = true;

    if(isset($_POST['submit'])){
        $to = "arielmachini.pruebas@gmail.com";
        $from = $_POST['direccionemail'];
        $nombrecompleto = $_POST['nombrecompleto'];
        $cantidadcertificados = $_POST['cantidadcertificados'];
        $dni = $_POST['dni'];
        $carrera = $_POST['carrera'];
        $subject = "Mensaje de solicitud";
        $subject2 = "Mensaje de copia";
        $message = $nombrecompleto . " hizo una nueva solicitud de certificado de alumno regular.\n\nCantidad de certificados: " . $cantidadcertificados . "\n\nDNI: " . $dni . "\n\nCarrera: " . $carrera;
        $message = wordwrap($message, 70, "\n");
        $message2 = "Este mensaje es una copia para el que hizo la solicitud.";

        $headers = 'From: arielmachini.pruebas@gmail.com' . "\r\n" . 'Reply-To: arielmachini.pruebas@gmail.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        mail($to,$subject,$message,$headers);
        mail($from,$subject2,$message2,$headers);
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
                    <p>Formulario enviado con éxito.<br/>
                    Mail1: <?= ($mail1) ? 'true' : 'false'; ?><br/>Mail2: <?= ($mail2) ? 'true' : 'false'; ?></p>
                </div>
            </article>
        </section>
        <?php
            include_once '../gui/GUIfooter.php';
        ?>
    </body>
</html>

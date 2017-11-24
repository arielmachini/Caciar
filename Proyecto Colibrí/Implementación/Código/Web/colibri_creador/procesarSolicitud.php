<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once './Formulario.class.php';
include_once './Campos.class.php';

if (!isset($_SESSION)) {
    session_start();
}

$mensaje = '';
$Formulario = $_SESSION['formulario'];

if (trim($Formulario->getEmailReceptor()) != '' && isset($Formulario)) {
    date_default_timezone_set("America/Argentina/Rio_Gallegos");

    require_once dirname(dirname(__FILE__)) . '/lib/PHPMailer/PHPMailerAutoload.php';

    $mensajero = new PHPMailer();

    $mensajero->isSMTP();
    $mensajero->Host = 'smtp.gmail.com';
    $mensajero->Port = 587;
    $mensajero->SMTPSecure = 'tls';
    $mensajero->SMTPAuth = true;
    $mensajero->Username = "arielmachini.pruebas@gmail.com";
    $mensajero->Password = "titpfa312";

    $mensajero->setFrom('arielmachini.pruebas@gmail.com', 'Sistema Colibrí');

    // Si se quieren añadir más receptores...
    $mensajero->addAddress($Formulario->getEmailReceptor(), 'Ariel Machini');

    $mensajero->CharSet = 'UTF-8';
    $mensajero->Subject = "Sistema Colibrí: Nueva solicitud en \"" . $Formulario->getTitulo() . "\"";
    $mensajero->isHTML(false);
    $mensajero->Body = "¡Hola! Alguien acaba de enviar una solicitud a través de su formulario.\nA continuación se mostrarán los datos extraídos de la solicitud:\n\n";

    foreach ($_POST as $tituloCampo => $valorVariable) {
        foreach ($Formulario->getCampos() as $CampoActual) {
            if ($tituloCampo !== "emailReceptor") {
                $tituloCampoActual = $CampoActual->getTitulo();
                $tituloCampoActual = str_replace(' ', '_', $tituloCampoActual);
                $tituloCampoActual = str_replace('.', '_', $tituloCampoActual);
                
                if ("nombre_" . $tituloCampoActual === $tituloCampo) {
                    $mensajero->Body .= $CampoActual->getTitulo() . ": " . $valorVariable . "\n";
                    break;
                }
            }
        }
    }

    if ($mensajero->send()) {
        echo "¡¡¡Mensaje enviado con éxito!!! Revise el buzón de entrada de {$Formulario->getEmailReceptor()}.";
    } else {
        echo "ERROR. El mensaje no pudo ser enviado. Razón: " . $mensajero->ErrorInfo;
    }
} else {
    echo "El e-mail al que se reciben las respuestas es INVÁLIDO o el formulario recibido por parámetros es NULL. Verifique que la información se envíe por POST, que se haya rellenado correctamente dicho campo en las propiedades del formulario y que se esté recibiendo el formulario por SESSION correctamente.";
}
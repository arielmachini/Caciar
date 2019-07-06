<!DOCTYPE html>

<?php
require_once '../modelo/Formulario.Class.php';
include_once '../lib/ControlAcceso.Class.php';

$formulario = $_SESSION['formulario'];
unset($_SESSION['formulario']);

include_once '../lib/FabricaPDF.php';
require_once '../modelo/BDConexion.Class.php';

date_default_timezone_set("America/Argentina/Rio_Gallegos");

use PHPMailer\PHPMailer\PHPMailer;

require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

/* Google reCAPTCHA */

function reCaptcha() {
    $claveSecreta = "6LdFxpMUAAAAAG80fCKsIt3RLXiXX1WxW-3vboTI";
    $g_recaptcha_response = filter_var(filter_input(INPUT_POST, "g-recaptcha-response"), FILTER_SANITIZE_STRING);

    $resultado = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$claveSecreta}&response={$g_recaptcha_response}"));

    return $resultado;
}

/*
 * Se realiza esta comprobación para evitar que el usuario acceda directamente
 * a esta página.
 */
if (empty($_POST) || !isset($_SESSION['formulario'])) {
    ControlAcceso::redireccionar();
    
    exit();
}

$reCaptcha = reCaptcha();

$puntajeCaptcha = $reCaptcha->score;
$resultadoCaptcha = $reCaptcha->success;

if ($resultadoCaptcha && $puntajeCaptcha > 0.5) {
    $colibri = new PHPMailer(true);
    $direccionEmisor = "arielmachini.pruebas@gmail.com";
    // $direccionEmisor = "sistema-colibri@uarg.unpa.edu.ar"
    $errorEnvio = true;

    /* CONFIGURACIÓN DEL SERVIDOR */
    $colibri->isSMTP();
    $colibri->Host = "smtp.gmail.com";
    $colibri->Port = 587;
    $colibri->SMTPAuth = true;
    $colibri->SMTPSecure = "tls";

    /* CONFIGURACIÓN DE LA DIRECCIÓN DE E-MAIL EMISORA */
    $colibri->Username = $direccionEmisor;
    $colibri->Password = "titpfa312";

    $colibri->setFrom($direccionEmisor, "Sistema Colibrí");
    $colibri->addAddress($formulario->getEmailReceptor());
    $colibri->Subject = "Nueva solicitud en \"" . $formulario->getTitulo() . "\"";
    $colibri->isHTML(false);
    $colibri->Body = "Estimado usuario,\n\nSe acaba de enviar una nueva solicitud en su formulario \"" . $formulario->getTitulo() . "\". A continuación se muestran los datos de dicha solicitud:\n\n";

    $cuerpoHtmlPdf = '';

    foreach ($formulario->getCampos() as $campo) {
        if ($campo instanceof ListaCheckbox) { // Sólo hay que realizar un tratamiento diferente para la lista de casillas de verificación.
            $nombreCampo = str_replace(" ", "_", $campo->getTitulo());
            $casillasSeleccionadas = filter_input(INPUT_POST, $nombreCampo, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            if (!empty($casillasSeleccionadas)) {
                $colibri->Body .= "\n" . $campo->getTitulo() . ":\n";
                $cuerpoHtmlPdf .= '<strong>' . $campo->getTitulo() . '</strong><br/>';

                foreach ($casillasSeleccionadas as $casilla) {
                    $colibri->Body .= "(✓) " . $casilla . "\n";
                    $cuerpoHtmlPdf .= '<div style="display: inline-block;"><img alt="(X)" height="12px" src="../lib/img/documentos-pdf/casilla_verificacion.svg" style="background-color: white;" width="12px"> ' . $casilla . '</div>';
                }

                $colibri->Body .= "\n";
                $cuerpoHtmlPdf .= '<br/>';
            }
        } else {
            $nombreCampo = "nombre_" . str_replace(" ", "_", $campo->getTitulo());
            $valorCampo = filter_input(INPUT_POST, $nombreCampo);

            if ($valorCampo != "") {
                $colibri->Body .= $campo->getTitulo() . ": " . $valorCampo . "\n";
                $cuerpoHtmlPdf .= '<strong>' . $campo->getTitulo() . '</strong><br/>' . $valorCampo . '<br/><br/>';
            }
        }
    }

    FabricaPDF::generar($formulario->getID(), $formulario->getTitulo(), $formulario->incrementarRespuestas(), $cuerpoHtmlPdf);
    BDConexion::getInstancia("bdFormularios")->autocommit(false);

    $consulta = BDConexion::getInstancia()->query("" .
            "UPDATE " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "SET `cantidadRespuestas` = {$formulario->incrementarRespuestas()}" .
            "WHERE `idFormulario` = {$formulario->getID()}");

    if ($consulta) {
        if ($colibri->send()) {
            $errorEnvio = false;

            BDConexion::getInstancia()->commit();
        } else {
            BDConexion::getInstancia()->rollback();
        }
    } else {
        BDConexion::getInstancia()->rollback();
    }
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - <?php echo $formulario->getTitulo(); ?></title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3><?= $formulario->getTitulo(); ?></h3>
                </div>
                <div class="card-body">
                    <?php if (!$resultadoCaptcha || $puntajeCaptcha < 0.5) { ?>
                        <div class="alert alert-warning" role="alert">
                            Su respuesta no puede ser procesada porque no pasó el desafío de reCAPTCHA.
                        </div>
                    <?php } else if (!$consulta || $errorEnvio) { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar su respuesta. Por favor, inténtelo más tarde.
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-success" role="alert">
                            Su respuesta ha sido registrada con éxito.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="formularios.php"><span class="oi oi-account-logout"></span> Finalizar</a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>
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
if (empty($_POST) || !isset($formulario)) {
    ControlAcceso::redireccionar();
    
    exit();
}

$reCaptcha = reCaptcha();

$puntajeCaptcha = $reCaptcha->score;
$resultadoCaptcha = $reCaptcha->success;

if ($resultadoCaptcha && $puntajeCaptcha > 0.5) {
    $csvRespuesta = '"' . date("d/m/Y H:i:s") . '",';
    
    foreach ($formulario->getCampos() as $campo) {
        if ($campo instanceof ListaCheckbox) { // Sólo hay que realizar un tratamiento diferente para la lista de casillas de verificación.
            $nombreCampo = str_replace(" ", "_", $campo->getTitulo());
            
            $casillasSeleccionadas = filter_input(INPUT_POST, $nombreCampo, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            $csvRespuesta .= '"';


            foreach ($casillasSeleccionadas as $casilla) {
                $csvRespuesta .= str_replace('"', '""', $casilla) . ';';
            }
            
            $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);
            $csvRespuesta .= '",';
        } else {
            $nombreCampo = "nombre_" . str_replace(" ", "_", $campo->getTitulo());
            
            $valorCampo = filter_input(INPUT_POST, $nombreCampo);
            
            $csvRespuesta .= '"' . str_replace('"', '""', $valorCampo) . '",';
        }
    }
    
    $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);

    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_RESPUESTA . "(`idFormulario`, `csv`, `fueEnviada`) " .
            "VALUES ({$formulario->getID()}, '{$csvRespuesta}', 0)");
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
                    <?php } else if (!$consulta) { ?>
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
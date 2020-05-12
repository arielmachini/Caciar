<!DOCTYPE html>

<?php
require_once '../modelo/Formulario.Class.php';
include_once '../lib/ControlAcceso.Class.php';

$formulario = $_SESSION['formulario'];

/*
 * Se realiza esta comprobación para evitar que el usuario acceda directamente
 * a esta página.
 */
if (empty($_POST) || !isset($formulario)) {
    ControlAcceso::redireccionar();
    
    exit();
}

unset($_SESSION['formulario']);

require_once '../modelo/BDConexion.Class.php';

$estaHabilitado = BDConexion::getInstancia()->query("" .
        "SELECT `estaHabilitado` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `idFormulario` = {$formulario->getID()}")->fetch_array();

if ($estaHabilitado[0] == 1) {
    $estaHabilitado = true;
} else {
    /* El formulario no está habilitado, por lo tanto no puede recibir nuevas
     * respuestas.
     */
    $estaHabilitado = false;
}

date_default_timezone_set("America/Argentina/Rio_Gallegos");

/* Google reCAPTCHA */
function esHumano() {
    $claveSecreta = "6LfQZeoUAAAAAJ3YivsutzTSYkoy1sH7Zm0NYAy1";
    $g_recaptcha_response = $_POST["g-recaptcha-response"];

    $datos_consulta = array('header' => "Content-Type: application/x-www-form-urlencoded\r\n", 'secret' => $claveSecreta, 'response' => $g_recaptcha_response);
    $opciones = array('http' => array('method' => "POST", 'content' => http_build_query($datos_consulta)));
    $contexto = stream_context_create($opciones);
    
    $resultado = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $contexto));
    
    return $resultado->success;
}

$esHumano = esHumano();

if ($estaHabilitado && $esHumano) {
    if ($formulario->getNotificacionesCorreo() == 1) { // El creador del formulario quiere recibir esta respuesta por correo electrónico.
        require_once '../lib/Colibri.Class.php';

        $csvRespuesta = '"' . date("d/m/Y H:i:s") . '",';

        /* VARIABLES PARA EL ENVÍO DE LA RESPUESTA POR E-MAIL: */
        $colibri = new Colibri($formulario->getEmailReceptor(), $formulario->getTitulo());
        $arregloCamposFormulario = array();
        $cuerpoMensaje = "Estimado usuario,\n\nEl formulario «{$formulario->getTitulo()}» tiene una nueva respuesta. A continuación se muestran los datos de dicha respuesta:\n\n";
        $fueEnviada = 0;

        foreach ($formulario->getCampos() as $campo) {
            if ($campo instanceof ListaCheckbox) { // Sólo hay que realizar un tratamiento diferente para la lista de casillas de verificación.
                $nombreCampo = str_replace(" ", "_", $campo->getTitulo());
                $casillasSeleccionadas = filter_input(INPUT_POST, $nombreCampo, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

                $csvRespuesta .= '"';

                if (isset($casillasSeleccionadas)) {
                    $cuerpoMensaje .= $campo->getTitulo() . ":\n";
                    $enumeracionCasillasSeleccionadas = "";

                    foreach ($casillasSeleccionadas as $casilla) {
                        $csvRespuesta .= str_replace('"', '""', $casilla) . ';';

                        $cuerpoMensaje .= "☑ " . $casilla . "\n";
                        $enumeracionCasillasSeleccionadas .= $casilla . ';';
                    }

                    $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);

                    $cuerpoMensaje .= "\n";
                    $enumeracionCasillasSeleccionadas = substr($enumeracionCasillasSeleccionadas, 0, strlen($enumeracionCasillasSeleccionadas) - 1);
                    $arregloCamposFormulario[$campo->getTitulo()] = $enumeracionCasillasSeleccionadas;
                }

                $csvRespuesta .= '",';
            } else {
                $nombreCampo = "nombre_" . str_replace(" ", "_", $campo->getTitulo());
                $valorCampo = filter_input(INPUT_POST, $nombreCampo);

                $csvRespuesta .= '"' . str_replace('"', '""', $valorCampo) . '",';

                $arregloCamposFormulario[$campo->getTitulo()] = $valorCampo;

                if (isset($valorCampo) && trim($valorCampo) != "") {
                    $cuerpoMensaje .= $campo->getTitulo() . ":\n" . $valorCampo . "\n\n";
                }
            }
        }

        $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);
        $cuerpoMensaje .= "En el presente mensaje también se encuentra adjunto un documento PDF con los detalles de esta respuesta.\nRecuerde que puede acceder a todas las respuestas que registra este formulario cuando usted desee desde el gestor de formularios.";

        if ($colibri->enviarMensaje($cuerpoMensaje, $arregloCamposFormulario)) {
            $fueEnviada = 1;
        }
    } else { // El creador del formulario NO quiere recibir esta respuesta por correo electrónico.
        $csvRespuesta = '"' . date("d/m/Y H:i:s") . '",';
        $fueEnviada = 0;
        
        foreach ($formulario->getCampos() as $campo) {
            if ($campo instanceof ListaCheckbox) { // Sólo hay que realizar un tratamiento diferente para la lista de casillas de verificación.
                $nombreCampo = str_replace(" ", "_", $campo->getTitulo());
                $casillasSeleccionadas = filter_input(INPUT_POST, $nombreCampo, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

                $csvRespuesta .= '"';

                if (isset($casillasSeleccionadas)) {
                    foreach ($casillasSeleccionadas as $casilla) {
                        $csvRespuesta .= str_replace('"', '""', $casilla) . ';';
                    }

                    $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);
                }

                $csvRespuesta .= '",';
            } else {
                $nombreCampo = "nombre_" . str_replace(" ", "_", $campo->getTitulo());
                $valorCampo = filter_input(INPUT_POST, $nombreCampo);

                $csvRespuesta .= '"' . str_replace('"', '""', $valorCampo) . '",';
            }
        }

        $csvRespuesta = substr($csvRespuesta, 0, strlen($csvRespuesta) - 1);
    }

    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_RESPUESTA . "(`idFormulario`, `csv`, `fueEnviada`) " .
            "VALUES ({$formulario->getID()}, '{$csvRespuesta}', {$fueEnviada})");
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
                    <?php if (!$esHumano) { ?>
                        <div class="alert alert-warning" role="alert">
                            Su respuesta no puede ser procesada porque no completó el captcha.
                        </div>
                    <?php } else if (!$estaHabilitado || !$consulta) { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar su respuesta. Por favor, inténtelo más tarde.
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-success" role="alert">
                            Gracias. Su respuesta ha sido registrada con éxito.
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
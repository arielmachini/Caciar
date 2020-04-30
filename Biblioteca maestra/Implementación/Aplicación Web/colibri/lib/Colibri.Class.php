<?php
include_once 'FabricaPDF.php';
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

/**
 * Esta clase se encarga de enviar nuevas respuestas a formularios por correo
 * electrónico a las personas correspondientes.
 *
 * @author Ariel Machini <arielmachini@pm.me>
 */
class Colibri extends PHPMailer\PHPMailer\PHPMailer {
    private const DIRECCION_CORREO_EMISOR = "arielmachini.pruebas@gmail.com";
    private const CONTRASENIA_CORREO_EMISOR = "titpfa312";
    
    private const HOST_SERVIDOR_EMISOR = "smtp.gmail.com";
    private const PUERTO_SERVIDOR_EMISOR = 587;
    
    private $nombreArchivoDocumentoPdf;
    private $idRespuesta;
    private $tituloFormulario;
    
    function __construct($direccionCorreoReceptor_, $idRespuesta_, $tituloFormulario_) {
        $this->idRespuesta = $idRespuesta_;
        $this->tituloFormulario = $tituloFormulario_;
        $this->nombreArchivoDocumentoPdf = "Respuesta-" . $this->idRespuesta . "_" . preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), str_replace(" ", "-", $this->tituloFormulario)) . "_" . date("d-m-Y") .".pdf";
        
        $this->Username = Colibri::DIRECCION_CORREO_EMISOR;
        $this->Password = Colibri::CONTRASENIA_CORREO_EMISOR;
        
        $this->isSMTP();
        $this->Host = Colibri::HOST_SERVIDOR_EMISOR;
        $this->Port = Colibri::PUERTO_SERVIDOR_EMISOR;
        $this->SMTPAuth = true;
        $this->SMTPSecure = "tls";
        
        $this->setFrom(Colibri::DIRECCION_CORREO_EMISOR, "Sistema Colibrí");
        $this->addAddress($direccionCorreoReceptor_);
        $this->Subject = "Colibrí: «" . $this->tituloFormulario . "» tiene una nueva respuesta (" . date("d/m/Y") . ")";
        
        $this->CharSet = "UTF-8";
        $this->isHTML(false);
    }
    
    /**
     * @param string $cuerpoMensaje_ El texto que compondrá el cuerpo del
     * mensaje.
     * @param array $arregloCamposFormulario_ Los campos del formulario junto
     * con sus respectivos valores. <b>IMPORTANTE:</b> ¡Debe ser un arreglo
     * ASOCIATIVO (nombre_de_campo => valor)!
     * @return boolean
     */
    function enviarMensaje($cuerpoMensaje_, $arregloCamposFormulario_) {
        $this->Body = $cuerpoMensaje_;
        $documentoPdfRespuesta = FabricaPDF::generarPdf($this->nombreArchivoDocumentoPdf, $this->idRespuesta, $this->tituloFormulario, $arregloCamposFormulario_);
        
        $this->addStringAttachment($documentoPdfRespuesta, $this->nombreArchivoDocumentoPdf, "base64", "application/pdf");
        
        return $this->send();
    }
}

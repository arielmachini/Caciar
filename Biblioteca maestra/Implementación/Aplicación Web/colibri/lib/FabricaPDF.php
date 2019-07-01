<?php

require_once '../lib/tcpdf/tcpdf.php';
require_once '../lib/tcpdf/config/tcpdf_config.php';

/**
 * @author Ariel Machini <arielmachini@pm.me>
 * @since 2019-03
 */
class FabricaPDF {

    /**
     * Esta función genera un documento PDF haciendo uso de la información que
     * recibe por parámetros y, posteriormente, fuerza su descarga a través del
     * navegador web del usuario.
     * 
     * @param $idFormulario_ (integer) La ID del formulario para el cual se genera el documento PDF.
     * @param $tituloFormulario_ (string) El título del formulario para el cual se genera el documento PDF.
     * @param $numeroSolicitud_ (integer) El número de solicitud o respuesta del formulario para el cual se genera el documento PDF.
     * @param $codigoHtml_ (string) El código HTML que compondrá el cuerpo del documento PDF.
     */
    public static function generar($idFormulario_, $tituloFormulario_, $numeroSolicitud_, $codigoHtml_) {
        $documentoPDF = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);

        /* PROPIEDADES GENERALES DEL ARCHIVO PDF */
        $documentoPDF->SetAutoPageBreak(true);
        $documentoPDF->SetCreator(PDF_CREATOR);
        $documentoPDF->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $tituloFormulario_, "Solicitud #" . $numeroSolicitud_);
        $documentoPDF->setHeaderFont(array(PDF_FONT_NAME_MAIN, "B", PDF_FONT_SIZE_DATA));
        $documentoPDF->SetFont(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN);
        $documentoPDF->setFooterData(array(0, 0, 0), array(0, 0, 0));
        $documentoPDF->setFooterFont(array(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_DATA));
        $documentoPDF->setFooterMargin(PDF_MARGIN_FOOTER);
        $documentoPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $documentoPDF->SetTitle("Solicitud #" . $numeroSolicitud_ . " en " . $tituloFormulario_ . " (" . date("d") . "-" . date("m") . "-" . date("Y") . ")");
        
        $documentoPDF->AddPage();
        $documentoPDF->writeHTML($codigoHtml_);
        
        ob_end_clean();
        
        $documentoPDF->Output("Solicitud" . $numeroSolicitud_ . "_" . date("d-m-Y") . "_" . date("H:i") . "_" . $idFormulario_ . ".pdf", "I");
    }

}

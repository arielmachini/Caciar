<?php

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
require_once dirname(__FILE__) . '/tcpdf/config/tcpdf_config.php';

/**
 * @author Ariel Machini <arielmachini@pm.me>
 */
class FabricaPDF {
    
    /**
     * Genera un documento PDF para una respuesta específica de un formulario.
     * Esta función fue diseñada para ser utilizada dentro del proceso de envío
     * de nuevas respuestas por correo electrónico (específicamente en la clase
     * Colibri.Class.php).
     * 
     * @author Ariel Machini <arielmachini@pm.me>
     * @param string $nombreArchivo_ El nombre con el que se generará el
     * documento PDF.
     * @param string $tituloFormulario_ El título del formulario para el cual
     * se genera el documento PDF.
     * @param array $arregloCamposFormulario_ Los campos del formulario junto
     * con sus respectivos valores. <b>IMPORTANTE:</b> ¡Debe ser un arreglo
     * ASOCIATIVO (nombre_de_campo => valor)!
     */
    public static function generarPdf($nombreArchivo_, $tituloFormulario_, $arregloCamposFormulario_) {
        $documentoPdf = new TCPDF(PDF_PAGE_FORMAT, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);
        $estiloCssContenido = '' .
                '<style>' .
                    'p.numero-respuesta {' .
                        'color: #303030;' .
                        'font-size: 15px;' .
                        'font-weight: bold;' .
                        'line-height: 18px;' .
                    '}' .
                
                    'span.marca-temporal {' .
                        'font-size: 10px !important;' .
                        'font-weight: normal !important;' .
                    '}' .
                
                    'span.titulo-campo {' .
                        'color: #3b3b3b;' .
                        'font-weight: bold;' .
                    '}' .
                
                    'span.valor-campo {' .
                        'color: #464646;' .
                    '}' .
                '</style>';

        /* INFORMACIÓN BÁSICA: */
        $documentoPdf->SetCreator(PDF_CREATOR);
        $documentoPdf->SetKeywords("Colibrí, PDF, respuesta");
        $documentoPdf->SetTitle("Nueva respuesta en \"" . htmlspecialchars($tituloFormulario_) . "\" (" . date("d/m/Y") . ")");
        
        /* PROPIEDADES: */
        $documentoPdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $documentoPdf->SetFont(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN);
        $documentoPdf->setFooterData(array(70, 70, 70), array(225, 225, 225));
        $documentoPdf->setFooterFont(array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
        $documentoPdf->setFooterMargin(PDF_MARGIN_FOOTER);
        $documentoPdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Formulario \"" . $tituloFormulario_ . "\"", "Respuesta registrada el " . date("d/m/Y"), array(70, 70, 70), array(225, 225, 225));
        $documentoPdf->setHeaderFont(array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
        $documentoPdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $documentoPdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP);
        $documentoPdf->Footer();
        $documentoPdf->AddPage();
        
        $cuerpoHtmlPagina = $estiloCssContenido . '<p class="numero-respuesta">Nueva respuesta<br/><span class="marca-temporal"><strong>Fecha de envío:</strong> ' . date("d/m/Y") . '</span></p><br/>';
        
        foreach ($arregloCamposFormulario_ as $tituloCampo => $valorCampo) {
            $cuerpoHtmlPagina .= '<span class="titulo-campo">' . htmlspecialchars($tituloCampo) . ':</span><br/>';
            
            if (trim($valorCampo) != "") {
                $cuerpoHtmlPagina .= '<span class="valor-campo">' . htmlspecialchars($valorCampo) . '</span><br/><br/>';
            } else {
                $cuerpoHtmlPagina .= '<span class="valor-campo" style="color: #777777; font-style: italic;">No completado</span><br/><br/>';
            }
        }
        
        $documentoPdf->writeHTML($cuerpoHtmlPagina);
        
        ob_end_clean();
        
        return $documentoPdf->Output($nombreArchivo_, "S");
    }
    
    /**
     * Genera un documento PDF con todas las respuestas de un formulario y,
     * posteriormente, fuerza su descarga.
     * 
     * @author Ariel Machini <arielmachini@pm.me>
     * @param string $tituloFormulario_ El título del formulario para el cual
     * se genera el documento PDF.
     * @param array $titulosCampos_ Los títulos de los campos del formulario.
     * @param array $respuestasFormulario_ Las respuestas que registra el
     * formulario. <b>IMPORTANTE:</b> ¡Las respuestas deben estar en formato
     * CSV!
     */
    public static function generarPdfRespuestas($tituloFormulario_, $titulosCampos_, $respuestasFormulario_) {
        $documentoPdf = new TCPDF(PDF_PAGE_FORMAT, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);
        $estiloCssContenido = '' .
                '<style>' .
                    'p.numero-respuesta {' .
                        'color: #303030;' .
                        'font-size: 15px;' .
                        'font-weight: bold;' .
                        'line-height: 18px;' .
                    '}' .
                
                    'span.marca-temporal {' .
                        'font-size: 10px !important;' .
                        'font-weight: normal !important;' .
                    '}' .
                
                    'span.titulo-campo {' .
                        'color: #3b3b3b;' .
                        'font-weight: bold;' .
                    '}' .
                
                    'span.valor-campo {' .
                        'color: #464646;' .
                    '}' .
                '</style>';

        /* INFORMACIÓN BÁSICA: */
        $documentoPdf->SetCreator(PDF_CREATOR);
        $documentoPdf->SetKeywords("Colibrí, PDF, respuestas");
        $documentoPdf->SetTitle("Respuestas en \"" . htmlspecialchars($tituloFormulario_) . "\" (" . date("d/m/Y") . ")");
        
        /* PROPIEDADES: */
        $documentoPdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $documentoPdf->SetFont(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN);
        $documentoPdf->setFooterData(array(70, 70, 70), array(225, 225, 225));
        $documentoPdf->setFooterFont(array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
        $documentoPdf->setFooterMargin(PDF_MARGIN_FOOTER);
        $documentoPdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Formulario \"" . $tituloFormulario_ . "\"", "Respuestas registradas", array(70, 70, 70), array(225, 225, 225));
        $documentoPdf->setHeaderFont(array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
        $documentoPdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $documentoPdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP);
        $documentoPdf->Footer();
        
        for ($i = 0; $i < count($respuestasFormulario_); $i++) {
            $respuesta = str_getcsv($respuestasFormulario_[$i]);
            $cuerpoHtmlPagina = $estiloCssContenido . '<p class="numero-respuesta">Respuesta #' . ($i + 1) . '<br/><span class="marca-temporal"><strong>Fecha y hora de envío:</strong> ' . $respuesta[0] . '</span></p><br/>';
            
            $documentoPdf->AddPage();
            
            for ($j = 0; $j < count($titulosCampos_); $j++) {
                $cuerpoHtmlPagina .= '<span class="titulo-campo">' . htmlspecialchars($titulosCampos_[$j]) . ':</span><br/>';
                
                if (trim($respuesta[($j + 1)]) != "") {
                    $cuerpoHtmlPagina .= '<span class="valor-campo">' . htmlspecialchars($respuesta[($j + 1)]) . '</span><br/><br/>';
                } else {
                    $cuerpoHtmlPagina .= '<span class="valor-campo" style="color: #777777; font-style: italic;">No completado</span><br/><br/>';
                }
            }
            
            $documentoPdf->writeHTML($cuerpoHtmlPagina);
        }
        
        $nombreArchivo = "Respuestas_" . preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), str_replace(" ", "-", $tituloFormulario_)) . "_" . date("d-m-Y") . ".pdf";
        
        ob_end_clean();
        
        $documentoPdf->Output($nombreArchivo, "D");
    }

}

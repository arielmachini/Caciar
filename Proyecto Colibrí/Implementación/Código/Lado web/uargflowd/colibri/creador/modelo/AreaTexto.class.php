<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Esta clase abstrae a campos <textarea></textarea>.
 *
 * @author Ariel Machini
 * @version 1.0
 */ 
class AreaTexto {
    private $limiteCaracteres;
    
    function getCodigo() {
        $codigoGenerado = parent::getCodigo();
        
        $codigoGenerado = $codigoGenerado .
                "<textarea";
        
        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado . " autofocus=\"true\"";
        }
        
        $codigoGenerado = $codigoGenerado . " maxlength=\"" . $this->getLimiteCaracteres() . "\"";
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado . " required=\"true\"";
        }
        
        $codigoGenerado = $codigoGenerado . "></textarea><br/><br/>";
    }
            
    function getLimiteCaracteres() {
        return $this->limiteCaracteres;
    }
    
    function setLimiteCaracteres($limiteCaracteres_) {
        $this->limiteCaracteres = $limiteCaracteres_;
    }
}

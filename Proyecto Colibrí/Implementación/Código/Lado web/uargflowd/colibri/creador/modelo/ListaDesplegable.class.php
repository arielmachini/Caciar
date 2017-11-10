<?php

/**
 * Esta clase abstrae a campos <select/><option></option> ... <option></option></select>
 *
 * @author Ariel Machini
 * @version 1.0
 */
class ListaDesplegable extends Campo {
    private $opciones = array();
    private $opcionInicial;
    
    function __construct() {
        $this->opcionInicial = null;
    }
    
    function getCodigo() {
        $codigoGenerado = parent::getCodigo();
        
        $codigoGenerado = $codigoGenerado .
                "<select";
        
        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado . " autofocus=\"true\"";
        }
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado . " required=\"true\"";
        }
        
        $codigoGenerado = $codigoGenerado . ">";
        
        for ($i = 0; $i < count($this->opciones); $i++) {
            if ($i === $this->opcionInicial) {
                $codigoGenerado = $codigoGenerado .
                        "<option selected value=\"" . $this->opciones[$i] . "\">" . $this->opciones[$i] . "</option>";
            } else {
                $codigoGenerado = $codigoGenerado .
                        "<option value=\"" . $this->opciones[$i] . "\">" . $this->opciones[$i] . "</option>";
            }
        }
        
        $codigoGenerado = $codigoGenerado .
                "</select><br/><br/>";
    }
    
    function getOpciones() {
        return $this->opciones;
    }
    
    function getOpcionInicial() {
        return $this->opcionInicial;
    }
    
    function setOpciones($opciones_) {
        if (gettype($opciones_) === "array") {
            $this->opciones = $opciones_;
        } else {
            throw new InvalidArgumentException("Error: Este método sólo acepta parámetros de tipo 'array'.");
        }
    }
    
    function setOpcionInicial($opcionInicial_) {
        $this->opcionInicial = $opcionInicial_;
    }
}

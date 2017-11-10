<?php

/**
 * Esta clase abstrae a campos <input/> de tipo "text".
 *
 * @author Ariel Machini
 * @version 1.0
 */
class CampoTexto extends Campo {

    private $pista;

    function __construct() {
        $this->pista = null;
    }

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<input ";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado . "autofocus=\"true\" ";
        }

        $codigoGenerado = $codigoGenerado . "id=\"" . $this->getId() . "\" name=\"" . $this->getNombre() . "\" ";

        if ($this->getPista() !== null) {
            $codigoGenerado = $codigoGenerado . "placeholder=\"" . $this->getPista() . "\" ";
        }
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado . "required=\"true\" ";
        }

        $codigoGenerado = $codigoGenerado . "type=\"text\"/><br/><br/>";


        return $codigoGenerado;
    }

    function getPista() {
        return $this->pista;
    }

    function setPista($pista_) {
        $this->pista = $pista_;
    }

}

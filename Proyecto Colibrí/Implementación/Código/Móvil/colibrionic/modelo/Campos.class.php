<?php

/**
 * Esta clase no se puede instanciar y abstrae los atributos y funciones más
 * generales de los campos de un formulario.
 *
 * @author Ariel Machini
 * @version 1.0
 */
abstract class Campo {
    
    /*
     * DEFINICIÓN DE LOS ATRIBUTOS DE LA CLASE:
     *
     * $descripcion: Línea de texto que se muestra bajo el título del campo.

     * $obligatorio: Define si el rellenado del campo es o no obligatorio para
     * poder enviar el formulario.
     *
     * $posicion: Define la posición que tiene el campo en el formulario. <--- REVISAR SI ES NECESARIO!!!!
     *
     * $titulo: Cabecera que va antes del campo y la descripción. En resumidas
     * palabras, es el "nombre" del campo.
     */

    private $descripcion;
    private $obligatorio;
    private $posicion;
    private $titulo;

    function __construct() {
        $this->descripcion = null;
        $this->obligatorio = false;
    }

    abstract function getCodigo();
    
    abstract function getCodigoIonic();

    function esObligatorio() {
        return $this->obligatorio;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getPosicion() {
        return $this->posicion;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function setDescripcion($descripcion_) {
        $this->descripcion = $descripcion_;
    }

    function setEsObligatorio($obligatorio_) {
        $this->obligatorio = $obligatorio_;
    }
    
    function setPosicion($posicion_) {
        $this->posicion = $posicion_;
    }

    function setTitulo($titulo_) {
        $this->titulo = $titulo_;
    }

}

/**
 * Esta clase abstrae a campos <textarea></textarea>.
 *
 * @author Ariel Machini
 * @version 1.0
 */ 
class AreaTexto extends Campo {
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
            $codigoGenerado = $codigoGenerado . " required";
        }
        
        $codigoGenerado = $codigoGenerado . "></textarea><br/><br/>";
    }
    
    function getCodigoIonic() {
        //
    }
            
    function getLimiteCaracteres() {
        return $this->limiteCaracteres;
    }
    
    function setLimiteCaracteres($limiteCaracteres_) {
        $this->limiteCaracteres = $limiteCaracteres_;
    }
}

/**
 * Esta clase abstrae a campos <input/> de tipo "text".
 *
 * @author Ariel Machini
 * @version 1.0
 */
class CampoTexto extends Campo {

    private $pista;

    function __construct() {
        parent::__construct();
        
        $this->pista = null;
    }

    function getCodigo() {
        $codigoGenerado = "<h4>" . $this->getTitulo() . "</h4>";
        
        if ($this->getDescripcion() !== null) {
            $codigoGenerado = $codigoGenerado .
                    "<p class=\"Descripcion\">" . $this->getDescripcion() . "</p>";
        }

        $codigoGenerado = $codigoGenerado .
                "<input ";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado . "autofocus=\"true\" ";
        }

        $codigoGenerado = $codigoGenerado . "id=\"id_" . $this->getTitulo() . "\" name=\"nombre_" . $this->getTitulo() . "\" ";

        if (!empty($this->getPista())) {
            $codigoGenerado = $codigoGenerado . "placeholder=\"" . $this->getPista() . "\" ";
        }
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado . "required ";
        }

        $codigoGenerado = $codigoGenerado . "type=\"text\"><br/><br/>";


        return $codigoGenerado;
    }
    
    function getCodigoIonic() {
        /*$codigoGenerado = "<h5>" . $this->getTitulo() . "</h5>";
        
        if ($this->getDescripcion() !== null) {
            $codigoGenerado = $codigoGenerado .
                    "<p>" . $this->getDescripcion() . "</p>";
        }*/

        $codigoGenerado = "<ion-input id=\"id_" . $this->getTitulo() . "\" name=\"nombre_" . $this->getTitulo() . "\" ";

        if (!empty($this->getPista())) {
            $codigoGenerado = $codigoGenerado . "placeholder=\"" . $this->getPista() . "\" ";
        }
        
        /*if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado . "required ";
        }*/

        $codigoGenerado = $codigoGenerado . "type=\"text\"></ion-input><br/><br/>";


        return $codigoGenerado;
    }

    function getPista() {
        return $this->pista;
    }

    function setPista($pista_) {
        $this->pista = $pista_;
    }

}

/**
 * Esta clase abstrae a campos <select/><option></option> ... <option></option></select>
 *
 * @author Ariel Machini
 * @version 1.0
 */
class ListaDesplegable extends Campo {
    
    private $opciones = array();
    
    function __construct() {
        parent::__construct();
    }
    
    function agregarOpcion($opcion_) {
        array_push($this->opciones, $opcion_);
    }
    
    function getCodigo() {
        $codigoGenerado = parent::getCodigo();
        
        $codigoGenerado = $codigoGenerado .
                "<select";
        
        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado . " autofocus=\"true\"";
        }
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado . " required";
        }
        
        $codigoGenerado = $codigoGenerado . ">";
        
        for ($i = 0; $i < count($this->opciones); $i++) {
            $codigoGenerado = $codigoGenerado .
                        "<option value=\"" . $this->opciones[$i] . "\">" . $this->opciones[$i] . "</option>";
        }
        
        $codigoGenerado = $codigoGenerado .
                "</select><br/><br/>";
    }
    
    function getCodigoIonic() {
        //;
    }
    
    function getOpciones() {
        return $this->opciones;
    }
    
    function setOpciones($opciones_) {
        if (gettype($opciones_) === "array") {
            $this->opciones = $opciones_;
        } else {
            throw new InvalidArgumentException("Error: Este método sólo acepta parámetros de tipo 'array'.");
        }
    }
    
}

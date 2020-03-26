<?php

/**
 * Esta clase no se puede instanciar y abstrae los atributos y funciones más
 * generales de los campos de un formulario.
 *
 * @author Ariel Machini <arielmachini@pm.me>
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
     * $posicion: Define la posición que tiene el campo en el formulario.
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

    function getCodigo() {
        if ($this->esObligatorio()) {
            $codigoGenerado = "<p class=\"campo-cabecera\">" . $this->getTitulo() . "<span style=\"color: red; font-weight: bold;\">*</span></p>";
        } else {
            $codigoGenerado = "<p class=\"campo-cabecera\">" . $this->getTitulo() . "</p>";
        }

        if ($this->getDescripcion() !== null) {
            $codigoGenerado = $codigoGenerado .
                    "<p class=\"campo-descripcion\">" . $this->getDescripcion() . "</p>";
        }

        return $codigoGenerado;
    }

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
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
class AreaTexto extends Campo {

    private $limiteCaracteres;

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<textarea";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado .
                    " autofocus=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                " class=\"form-control\" maxlength=\"" . $this->getLimiteCaracteres() . "\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\"";

        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"required\"";
        }

        $codigoGenerado = $codigoGenerado .
                "></textarea><br/>";

        return $codigoGenerado;
    }

    function getCodigoIonic() {
        $codigoGenerado = "<ion-item>" . parent::getCodigo();
        
        $codigoGenerado = $codigoGenerado .
                "<ion-textarea maxlength=\"" . $this->getLimiteCaracteres() . "\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\"";
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"true\"";
        }
        
        $codigoGenerado = $codigoGenerado .
                "></ion-textarea></ion-item>";
        
        return $codigoGenerado;
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
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
class CampoTexto extends Campo {

    private $pista;
    private $subtipo;
    public static $CAMPO_TEXTO = 0;
    public static $CAMPO_NUMERICO = 1;
    public static $CAMPO_EMAIL = 2;

    function __construct() {
        parent::__construct();

        $this->pista = null;
        $this->subtipo = CampoTexto::$CAMPO_TEXTO;
    }

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<input";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado .
                    " autofocus=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                " class=\"form-control\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\"";

        if (!empty($this->getPista())) {
            $codigoGenerado = $codigoGenerado .
                    " placeholder=\"" . $this->getPista() . "\"";
        }

        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"required\"";
        }

        if ($this->getSubtipo() == CampoTexto::$CAMPO_TEXTO) {
            $codigoGenerado = $codigoGenerado .
                    " type=\"text\"><br/>";
        } else if ($this->getSubtipo() == CampoTexto::$CAMPO_NUMERICO) {
            $codigoGenerado = $codigoGenerado .
                    " min=\"0\" type=\"number\"><br/>";
        } else { // Por descarte, se asume que es un campo para direcciones de e-mail.
            $codigoGenerado = $codigoGenerado .
                    " type=\"email\"><br/>";
        }

        return $codigoGenerado;
    }

    function getCodigoIonic() {
        $codigoGenerado = "<ion-item>" . parent::getCodigo();
        
        $codigoGenerado = $codigoGenerado .
                "<ion-input name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\" ";

        if (!empty($this->getPista())) {
            $codigoGenerado = $codigoGenerado .
                    "placeholder=\"" . $this->getPista() . "\" ";
        }

        if($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    "required=\"true\" ";
        }

        if ($this->getSubtipo() == CampoTexto::$CAMPO_TEXTO) {
            $codigoGenerado = $codigoGenerado .
                    " type=\"text\"></ion-input>";
        } else if ($this->getSubtipo() == CampoTexto::$CAMPO_NUMERICO) {
            $codigoGenerado = $codigoGenerado .
                    " min=\"0\" type=\"number\"></ion-input>";
        } else { // Por descarte, se asume que es un campo para direcciones de e-mail.
            $codigoGenerado = $codigoGenerado .
                    " type=\"email\"></ion-input>";
        }
        
        $codigoGenerado = $codigoGenerado .
                "</ion-item>";

        return $codigoGenerado;
    }

    function getPista() {
        return $this->pista;
    }

    function setPista($pista_) {
        $this->pista = $pista_;
    }

    function getSubtipo() {
        return $this->subtipo;
    }

    function setSubtipo($subtipo_) {
        if ($subtipo_ != CampoTexto::$CAMPO_EMAIL && $subtipo_ != CampoTexto::$CAMPO_NUMERICO && $subtipo_ != CampoTexto::$CAMPO_TEXTO) {
            throw new InvalidArgumentException("Error: No se proporcionó un número de subtipo válido.");
        } else {
            $this->subtipo = $subtipo_;
        }
    }

}

/**
 * Esta clase abstrae a campos <input/> de tipo "date".
 * 
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
class Fecha extends Campo {

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<button type=\"button\" class=\"btn btn-outline-danger\" onclick=\"limpiarCampoFecha('nombre_" . str_replace(" ", "_", $this->getTitulo()) . "')\" style=\"float: right;\" title=\"Borrar fecha ingresada\">" .
                "<span class=\"oi oi-delete\"></span>" .
                "</button>" .
                "<div style=\"overflow: hidden; padding-right: 5px;\">" .
                "<input autocomplete=\"off\"";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado .
                    " autofocus=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                " class=\"form-control\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\"";

        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"required\"";
        }

        $codigoGenerado = $codigoGenerado .
                " type=\"date\"></div><br/>";

        return $codigoGenerado;
    }

    function getCodigoIonic() {
        $codigoGenerado = "<ion-item>" . parent::getCodigo();
        
        $codigoGenerado = $codigoGenerado .
                "<ion-datetime cancelText=\"Cancelar\" displayFormat=\"DD/MM/YYYY\" doneText=\"Aceptar\" placeholder=\"Toque aquí para elegir una fecha\"";
        
        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"true\"";
        }
        
        $codigoGenerado = $codigoGenerado .
                "></ion-datetime></ion-item>";
        
        return $codigoGenerado;
    }

}

/**
 * Esta clase no se puede instanciar y abstrae los atributos y funciones
 * generales de todos los tipos de lista que se pueden instanciar.
 *
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
abstract class Lista extends Campo {

    private $elementos = array();

    function agregarElemento($elemento_) {
        array_push($this->elementos, $elemento_);
    }

    function getElementos() {
        return $this->elementos;
    }

    function setElementos($elementos_) {
        if (gettype($elementos_) === "array") {
            $this->elementos = $elementos_;
        } else {
            throw new InvalidArgumentException("Error: Este método sólo acepta parámetros de tipo 'array'.");
        }
    }

}

/**
 * Esta clase abstrae a conjuntos de campos <input/> de tipo "checkbox".
 * 
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
class ListaCheckbox extends Lista {

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<label for=\"checkboxID0_" . $this->getPosicion() . "\">";

        $codigoGenerado = $codigoGenerado .
                "<input";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado .
                    " autofocus=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                " class=\"campo-opcion\" id=\"checkboxID0_" . $this->getPosicion() . "\" name=\"" . str_replace(" ", "_", $this->getTitulo()) . "[]\"";

        $codigoGenerado = $codigoGenerado .
                " type=\"checkbox\" value=\"" . $this->getElementos()[0] . "\"> ";

        $codigoGenerado = $codigoGenerado .
                $this->getElementos()[0];

        $codigoGenerado = $codigoGenerado .
                "</label>";

        for ($i = 1; $i < count($this->getElementos()); $i++) {
            $codigoGenerado = $codigoGenerado .
                    "<label for=\"checkboxID" . $i . "_" . $this->getPosicion() . "\">";

            $codigoGenerado = $codigoGenerado .
                    "<input class=\"campo-opcion\" id=\"checkboxID" . $i . "_" . $this->getPosicion() . "\" name=\"" . str_replace(" ", "_", $this->getTitulo()) . "[]\" type=\"checkbox\" value=\"" . $this->getElementos()[$i] . "\"> ";

            $codigoGenerado = $codigoGenerado .
                    $this->getElementos()[$i];

            $codigoGenerado = $codigoGenerado .
                    "</label>";
        }

        $codigoGenerado = $codigoGenerado .
                "<br/>";

        return $codigoGenerado;
    }

    function getCodigoIonic() {
        $codigoGenerado = "<ion-item>" . parent::getCodigo() . "</ion-item>";
        
        $codigoGenerado = $codigoGenerado .
                "<ion-list>";
        
        for ($i = 1; $i < count($this->getElementos()); $i++) {
            $codigoGenerado = $codigoGenerado .
                    "<ion-item><ion-label>" . $this->getElementos()[$i] . "</ion-label><ion-checkbox name=\"" . str_replace(" ", "_", $this->getTitulo()) . "[]\" value=\"" . $this->getElementos()[$i] . "\"></ion-checkbox></ion-item>";
        }
        
        $codigoGenerado = $codigoGenerado .
                "</ion-list>";
        
        return $codigoGenerado;
    }

}

/**
 * Esta clase abstrae a campos <select/><option></option> ... <option></option></select>
 *
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
class ListaDesplegable extends Lista {

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<select";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado .
                    " autofocus=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                " class=\"form-control\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\"";

        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"required\"";
        }

        $codigoGenerado = $codigoGenerado .
                "><option disabled=\"disabled\" selected=\"true\" value=\"\">Seleccione una opción</option>";

        for ($i = 0; $i < count($this->getElementos()); $i++) {
            $codigoGenerado = $codigoGenerado .
                    "<option value=\"" . $this->getElementos()[$i] . "\">" . $this->getElementos()[$i] . "</option>";
        }

        $codigoGenerado = $codigoGenerado .
                "</select><br/>";

        return $codigoGenerado;
    }

    function getCodigoIonic() {
        $codigoGenerado = "<ion-item>" . parent::getCodigo() . "</ion-item>";

        $codigoGenerado = $codigoGenerado .
                "<ion-item><ion-label>Elija una opción</ion-label><ion-select cancelText=\"Cancelar\" name=\"" . str_replace(" ", "_", $this->getTitulo()) . "\" okText=\"Aceptar\"";

        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                ">";

        for ($i = 1; $i < count($this->getElementos()); $i++) {
            $codigoGenerado = $codigoGenerado .
                    "<ion-option value=\"" . $this->getElementos()[$i] . "\">" . $this->getElementos()[$i] . "</ion-option>";
        }

        $codigoGenerado = $codigoGenerado .
                "</ion-select></ion-item>";
        
        return $codigoGenerado;
    }

}

/**
 * Esta clase abstrae a conjuntos de campos <input/> de tipo "radio".
 * 
 * @author Ariel Machini <arielmachini@pm.me>
 * @version 1.0
 */
class ListaRadio extends Lista {

    function getCodigo() {
        $codigoGenerado = parent::getCodigo();

        $codigoGenerado = $codigoGenerado .
                "<label for=\"botonRadioID0_" . $this->getPosicion() . "\">";

        $codigoGenerado = $codigoGenerado .
                "<input";

        if ($this->getPosicion() === 1) {
            $codigoGenerado = $codigoGenerado .
                    " autofocus=\"true\"";
        }

        $codigoGenerado = $codigoGenerado .
                " class=\"campo-opcion\" id=\"botonRadioID0_" . $this->getPosicion() . "\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\"";

        if ($this->esObligatorio()) {
            $codigoGenerado = $codigoGenerado .
                    " required=\"required\"";
        }

        $codigoGenerado = $codigoGenerado .
                " type=\"radio\" value=\"" . $this->getElementos()[0] . "\"> ";

        $codigoGenerado = $codigoGenerado .
                $this->getElementos()[0];

        $codigoGenerado = $codigoGenerado .
                "</label>";

        for ($i = 1; $i < count($this->getElementos()); $i++) {
            $codigoGenerado = $codigoGenerado .
                    "<label for=\"botonRadioID" . $i . "_" . $this->getPosicion() . "\">";

            $codigoGenerado = $codigoGenerado .
                    "<input class=\"campo-opcion\" id=\"botonRadioID" . $i . "_" . $this->getPosicion() . "\" name=\"nombre_" . str_replace(" ", "_", $this->getTitulo()) . "\" type=\"radio\" value=\"" . $this->getElementos()[$i] . "\"> ";

            $codigoGenerado = $codigoGenerado .
                    $this->getElementos()[$i];

            $codigoGenerado = $codigoGenerado .
                    "</label>";
        }

        $codigoGenerado = $codigoGenerado .
                "<br/>";

        return $codigoGenerado;
    }

    function getCodigoIonic() {
        $codigoGenerado = "<ion-item>" . parent::getCodigo() . "</ion-item>";

        $codigoGenerado = $codigoGenerado .
                "<ion-list radio-group>";

        if ($this->esObligatorio()) {
            for ($i = 1; $i < count($this->getElementos()); $i++) {
                $codigoGenerado = $codigoGenerado .
                        "<ion-item><ion-label>" . $this->getElementos()[$i] . "</ion-label><ion-radio name=\"" . str_replace(" ", "_", $this->getTitulo()) . "[]\" required=\"true\" value=\"" . $this->getElementos()[$i] . "\"></ion-radio></ion-item>";
            }
        } else {
            for ($i = 1; $i < count($this->getElementos()); $i++) {
                $codigoGenerado = $codigoGenerado .
                        "<ion-item><ion-label>" . $this->getElementos()[$i] . "</ion-label><ion-radio name=\"" . str_replace(" ", "_", $this->getTitulo()) . "[]\" value=\"" . $this->getElementos()[$i] . "\"></ion-radio></ion-item>";
            }
        }

        $codigoGenerado = $codigoGenerado .
                "</ion-list>";
        
        return $codigoGenerado;
    }

}

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
     * TÍTULO *
     * Descripción
     * [Pista          ]
     *
     * Nota: El * representa a un campo obligatorio.
     * Esa representación gráfica muestra sólo los atributos del campo que son
     * visibles al usuario. El resto son propiedades que se definen en el
     * código.
     *
     * $descripcion: Línea de texto que se muestra bajo el título del campo.
     *
     * $id: Identificador (único) que diferencia al campo de los demás en el
     * formulario. Se utiliza principalmente para recuperar el campo si se
     * quieren ejecutar funciones sobre este.
     *
     * $nombre: Nombre único que representa al campo. Se utiliza principalmente
     * para obtener los valores del formulario del array $_POST o $_GET.
     *
     * $obligatorio: Define si el rellenado del campo es o no obligatorio para
     * poder enviar el formulario.
     *
     * $posicion: Define la posición que tiene el campo en el formulario.
     *
     * $titulo: Cabecera que va antes del campo y la descripción. En resumidas
     * palabras, es el "nombre" del campo.
     */

    private $descripcion;
    private $id;
    private $nombre;
    private $obligatorio;
    private $posicion;
    private $titulo;

    function __construct() {
        $this->descripcion = null;
        $this->obligatorio = false;
    }

    function getCodigo() {
        $codigoGenerado = "<h4>" . $this->getTitulo() . "</h4><br/>";
        
        if ($this->getDescripcion() !== null) {
            $codigoGenerado = $codigoGenerado .
                    "<p class=\"Descripcion\">" . $this->getDescripcion() . "</p><br/>";
        }
    }

    function esObligatorio() {
        return $this->obligatorio;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getId() {
        return $this->id;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getPosicion() {
        return $this->posicion;
    }

    function getTitulo() {
        return $this->posicion;
    }

    function setDescripcion($descripcion_) {
        $this->descripcion = $descripcion_;
    }

    function setId($id_) {
        $this->id = $id_;
    }

    function setNombre($nombre_) {
        $this->nombre = $nombre_;
    }

    function setEsObligatorio($obligatorio_) {
        $this->obligatorio = $obligatorio_;
    }

    function setTitulo($titulo_) {
        $this->titulo = $titulo_;
    }

}

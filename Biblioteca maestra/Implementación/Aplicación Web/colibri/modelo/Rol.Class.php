<?php

include_once '../lib/BDCatalogoTablas.Class.php';
include_once 'BDObjetoGenerico.Class.php';
include_once 'Permiso.Class.php';

class Rol extends BDObjetoGenerico {

    /**
     *
     * @var Permiso[]
     */
    private $permisos;

    function __construct($id = null) {
        parent::__construct($id, BDCatalogoTablas::BD_TABLA_ROL);
        $this->setPermisos(BDCatalogoTablas::BD_TABLA_ROL_PERMISO, BDCatalogoTablas::BD_TABLA_PERMISO, "id_rol", "id_permiso", "Permiso");
    }

    function getPermisos() {
        return $this->permisos;
    }

    /**
     * 
     * @param type $tablaVinculacion
     * @param type $tablaElementos
     * @param type $idObjetoContenedor
     * @param type $atributoFKElementoColeccion
     * @param type $claseElementoColeccion
     * 
     * @see BDObjetoGenerico::setColeccionElementos($tablaVinculacion, $tablaElementos, $idObjetoContenedor, $atributoFKElementoColeccion, $claseElementoColeccion) 
     */
    function setPermisos($tablaVinculacion, $tablaElementos, $idObjetoContenedor, $atributoFKElementoColeccion, $claseElementoColeccion) {

        $this->setColeccionElementos($tablaVinculacion, $tablaElementos, $idObjetoContenedor, $atributoFKElementoColeccion, $claseElementoColeccion);
        $this->permisos = $this->getColeccionElementos();
    }

    function buscarPermisoPorId($id) {
        foreach ($this->getPermisos() as $PermisoRol) {
            if ($id == $PermisoRol->getId()) {
                return true;
            }
        }
        return false;
    }

}

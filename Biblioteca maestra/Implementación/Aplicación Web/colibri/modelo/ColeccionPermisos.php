<?php

include_once '../lib/BDCatalogoTablas.Class.php';
include_once 'BDColeccionGenerica.Class.php';
include_once 'Permiso.Class.php';

class ColeccionPermisos extends BDColeccionGenerica {

    /**
     *
     * @var Permiso[]
     */
    private $permisos;

    function __construct() {
        parent::__construct();
        $this->setColeccion(BDCatalogoTablas::BD_TABLA_PERMISO, "Permiso");
        $this->permisos = $this->coleccion;
    }

    /**
     * 
     * @return array()
     */
    function getPermisos() {
        return $this->permisos;
    }

}

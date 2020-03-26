<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_STRING) . '/colibri/lib/BDCatalogoTablas.Class.php';
include_once 'BDColeccionGenerica.Class.php';
include_once 'Rol.Class.php';

class ColeccionRoles extends BDColeccionGenerica {

    /**
     *
     * @var Rol[]
     */
    private $roles;

    function __construct() {
        parent::__construct();
        $this->setColeccion(BDCatalogoTablas::BD_TABLA_ROL, "Rol");
        $this->roles = $this->coleccion;
    }

    /**
     * 
     * @return array()
     */
    function getRoles() {
        return $this->roles;
    }

}

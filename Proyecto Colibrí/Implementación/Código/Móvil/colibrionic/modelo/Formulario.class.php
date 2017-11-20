<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/colibrionic/modelo/Workflow.class.php";

/**
 * Esta clase tiene como objetivo abstraer un formulario junto con sus
 * particularidades para, posteriormente, permitir almacenar toda su
 * información de manera ágil.
 *
 * @author Ariel Machini
 * @version 1.0
 */
class Formulario {
    
    /*
     * DEFINICIÓN DE LOS ATRIBUTOS DE LA CLASE:
     * 
     * $camposFormulario: Arreglo que contiene a todos los campos que contiene
     * el formulario.
     * 
     * $cantidadRespuestas: Esta variable no debería ser modificada
     * manualmente. Almacena el número de respuestas que tiene el formulario
     * abstraído en cada instancia de esta clase y se incrementa
     * automáticamente.
     * 
     * $emailReceptor: Dirección de correo electrónico a la que se van a enviar
     * las solicitudes.
     * 
     * $estaHabilitado: Define si el formulario está actualmente publicado o
     * no. Este valor se puede alternar siempre que sea necesario.
     * 
     * $fechaInicio: Atributo opcional. Define cuándo el formulario comenzará a
     * aceptar solicitudes (DD/MM/AAAA).
     * 
     * $fechaFin: Atributo opcional. Define cuándo el formulario dejará de
     * aceptar solicitudes (DD/MM/AAAA).
     * 
     * $rolesDestino: Arreglo que contiene los roles del sistema a los que
     * está dirigido el formulario.
     */
    
    /**
     *
     * @var Campo[]
     */
    private $camposFormulario = array();
    private $cantidadRespuestas;
    private $descripcion;
    private $emailReceptor;
    private $estaHabilitado;
    private $fechaInicio;
    private $fechaFin;
    private $fechaCreacion;
    private $rolesDestino = array();
    private $titulo;
    
    /* Esta variable sólo tiene el fin de comprobar que se agreguen roles
       válidos a la lista de destinatarios (ver función agregarDestinatario) */
    private $WorkflowRoles;
    
    
    function __construct($fechaCreacion_) {
        $this->cantidadRespuestas = 0;
        $this->descripcion = null;
        $this->estaHabilitado = false;
        $this->fechaCreacion = $fechaCreacion_;
        $this->WorkflowRoles = new WorkflowRoles();
    }
    
    function agregarCampo($campo_) {
        $this->camposFormulario[] = $campo_;
    }
    
    function agregarDestinatario($idrol_) {
        foreach ($this->WorkflowRoles as $WorkflowRol) {
            if ($WorkflowRol->getIdRol() === $idrol_) {
                array_push($this->rolesDestino, $idrol_);
            }
            
            break;
        }
    }
    
    function alternarHabilitado() {
        $this->estaHabilitado = !$this->estaHabilitado;
    }
    
    
    function estaHabilitado() {
        return $this->estaHabilitado;
    }
    
    function getCampos() {
        return $this->camposFormulario;
    }
    
    function getDescripcion() {
        return $this->descripcion;
    }
    
    function getCantidadRespuestas() {
        return $this->cantidadRespuestas;
    }
    
    function getEmailReceptor() {
        return $this->emailReceptor;
    }
    
    function getFechaInicio() {
        return $this->fechaInicio;
    }
    
    function getFechaFin() {
        return $this->fechaFin;
    }
    
    function getDestinatarios() {
        return $this->rolesDestino;
    }
    
    function getFechaCreacion() {
        return $this->fechaCreacion;
    }
    
    function getTitulo() {
        return $this->titulo;
    }
    
    function incrementarRespuestas() {
        $this->respuestas++;
    }
    
    function setDescripcion($descripcion_) {
        $this->descripcion = $descripcion_;
    }
    
    function setEmailReceptor($emailReceptor_) {
        $this->emailReceptor = $emailReceptor_;
    }
    
    function setFechaInicio($fechaInicio_) {
        $this->fechaInicio = $fechaInicio_;
    }
    
    function setFechaFin($fechaFin_) {
        $this->fechaFin = $fechaFin_;
    }
    
    function setTitulo($titulo_) {
        $this->titulo = $titulo_;
    }
}

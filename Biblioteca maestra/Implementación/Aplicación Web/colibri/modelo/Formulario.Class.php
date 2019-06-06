<?php

include_once 'ColeccionRoles.php';
include_once 'Campos.Class.php';

/**
 * Esta clase tiene como objetivo abstraer un formulario junto con sus
 * particularidades para, posteriormente, permitir almacenar toda su
 * información de manera ágil.
 *
 * @author Ariel Machini <arielmachini@pm.me>
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
     * $descripcion: Atributo opcional. Se muestra bajo el título del
     * formulario, es una breve introducción a la temática del formulario.
     * 
     * $emailReceptor: Dirección de correo electrónico a la que se van a enviar
     * las solicitudes.
     * 
     * $estaHabilitado: Define si el formulario está actualmente publicado o
     * no. Este valor se puede alternar siempre que sea necesario.
     * 
     * $fechaApertura: Atributo opcional. Define cuándo el formulario comenzará a
     * aceptar solicitudes (AAAA-MM-DD).
     * 
     * $fechaCierre: Atributo opcional. Define cuándo el formulario dejará de
     * aceptar solicitudes (AAAA-MM-DD).
     * 
     * $fechaCreacion: Es la fecha (AAAA-MM-DD) en la que fue creada el
     * formulario.
     * 
     * $id: Atributo opcional. La ID que le corresponde al formulario en la base
     * de datos. La única utilidad de este atributo es la de pasar la ID de un
     * formulario a través de un objeto de este tipo (tipo «Formulario»). Un
     * ejemplo de esto puede verse en las páginas «formulario.ver.php» y
     * «formulario.enviar.php».
     * 
     * $rolesDestino: Arreglo que contiene los roles del sistema a los que
     * está dirigido el formulario.
     * 
     * $titulo: El título (dicho de otra forma, el nombre) del formulario.
     * Es lo primero que ve el usuario y se muestra en la lista de formularios
     * disponibles.
     */

    private $camposFormulario = array();
    private $cantidadRespuestas;
    private $descripcion;
    private $emailReceptor;
    private $estaHabilitado;
    private $fechaApertura;
    private $fechaCierre;
    private $fechaCreacion;
    private $id;
    private $rolesDestino = array();
    private $titulo;
    
    /* Esta variable sólo tiene el fin de comprobar que se agreguen roles
       válidos a la lista de destinatarios (ver función agregarDestinatario) */
    private $ColeccionRoles;
    
    
    function __construct($fechaCreacion_ = null) {
        $this->cantidadRespuestas = 0;
        $this->descripcion = null;
        $this->estaHabilitado = false;
        $this->fechaCreacion = $fechaCreacion_;
        $this->ColeccionRoles = new ColeccionRoles();
    }
    
    function agregarCampo($campo_) {
        $this->camposFormulario[] = $campo_;
    }
    
    function agregarDestinatario($idrol_) {
        foreach ($this->ColeccionRoles->getRoles() as $Rol) {
            if ($Rol->getId() == $idrol_) {
                array_push($this->rolesDestino, $idrol_);

                break;
            }
        }
    }

    function estaHabilitado() {
        return $this->estaHabilitado;
    }
    
    function getCampos() {
        return $this->camposFormulario;
    }
    
    function getCodigo() {
        $codigoGenerado = "<form action=\"formulario.enviar.php\" id=\"formulario\" method=\"post\">";
        
        foreach ($this->camposFormulario as $campo) {
            $codigoGenerado = $codigoGenerado .
                    $campo->getCodigo();
        }
        
        $codigoGenerado = $codigoGenerado .
                "<input name=\"g-recaptcha-response\" type=\"hidden\" value=\"\">";
        
        $codigoGenerado = $codigoGenerado .
                "<button class=\"btn btn-success\" type=\"submit\" value=\"Enviar\"><span class=\"oi oi-check\" style=\"margin-right: 5px;\"></span>Enviar</button>";
        
        $codigoGenerado = $codigoGenerado .
                "</form>";
        
        return $codigoGenerado;
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
    
    function getFechaApertura() {
        return $this->fechaApertura;
    }
    
    function getFechaCierre() {
        return $this->fechaCierre;
    }
    
    function getDestinatarios() {
        return $this->rolesDestino;
    }
    
    function getFechaCreacion() {
        return $this->fechaCreacion;
    }
    
    function getID() {
        return $this->id;
    }
    
    function getTitulo() {
        return $this->titulo;
    }
    
    function incrementarRespuestas() {
        $this->cantidadRespuestas++;
        
        return $this->cantidadRespuestas;
    }
    
    function setCantidadRespuestas($cantidadRespuestas_) {
        $this->cantidadRespuestas = $cantidadRespuestas_;
    }
    
    function setDescripcion($descripcion_) {
        $this->descripcion = $descripcion_;
    }
    
    function setEmailReceptor($emailReceptor_) {
        $this->emailReceptor = $emailReceptor_;
    }
    
    function setFechaApertura($fechaApertura_) {
        $this->fechaApertura = $fechaApertura_;
    }
    
    function setFechaCierre($fechaCierre_) {
        $this->fechaCierre = $fechaCierre_;
    }
    
    function setID($id_) {
        $this->id = $id_;
    }
    
    function setTitulo($titulo_) {
        $this->titulo = $titulo_;
    }
}
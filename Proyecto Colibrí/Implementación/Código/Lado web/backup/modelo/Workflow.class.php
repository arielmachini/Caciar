<?php

class WorkflowPermiso {

    private $idpermiso;
    private $nombre;
    private $datos;

    function __construct($idpermiso_ = null) {
        if ($idpermiso_) {
            $this->idpermiso = $idpermiso_;
            $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                    . "SELECT * "
                    . "FROM " . Constantes::BD_USERS . ".PERMISO "
                    . "WHERE idpermiso = " . $this->idpermiso);
            foreach ($this->datos->fetch_assoc() as $atributo => $valor) {
                $this->{$atributo} = $valor;
            }
            $this->datos = null;
        }
    }

    function getIdPermiso() {
        return $this->idpermiso;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setIdPermiso($idpermiso) {
        $this->idpermiso = $idpermiso;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

}

class WorkflowRol {

    /**
     * @var int
     */
    private $idrol;
    private $nombre;
    private $datos;

    /**
     * @var WorkflowPermiso[]
     */
    private $permisos = array();

    public function __construct($idrol_ = null) {

        if ($idrol_) {
            $this->idrol = $idrol_;
            $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                    . "SELECT * "
                    . "FROM " . Constantes::BD_USERS . ".ROL "
                    . "WHERE idrol = " . $this->idrol);
            foreach ($this->datos->fetch_assoc() as $atributo => $valor) {
                $this->{$atributo} = $valor;
            }
            $this->datos = null;

            $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                    . "SELECT rp.idpermiso, p.nombre "
                    . "FROM " . Constantes::BD_USERS . ".ROL_PERMISO rp "
                    . "INNER JOIN " . Constantes::BD_USERS . ".PERMISO p ON (p.idpermiso=rp.idpermiso) "
                    . "WHERE rp.idrol = " . $this->idrol);

            for ($x = 0; $x < $this->datos->num_rows; $x++) {
                $this->addPermiso($this->datos->fetch_object("WorkflowPermiso"));
            }
        }
    }

    function getIdRol() {
        return $this->idrol;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setIdRol($idrol) {
        $this->idrol = $idrol;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function addPermiso($permiso) {
        $this->permisos[] = $permiso;
    }

    function getPermisos($idrol_) {
        return $this->permisos;
    }

    function poseePermiso($idPermiso_) {
        foreach ($this->permisos as $Permiso)
            if ($idPermiso_ == $Permiso->getIdPermiso())
                return true;
        return false;
    }

}

class WorkflowRoles {

    /**
     * @var WorkflowRol[]
     */
    private $roles;
    private $datos;

    function __construct() {

        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT * "
                . "FROM " . Constantes::BD_USERS . ".ROL ");
        for ($x = 0; $x < $this->datos->num_rows; $x++) {
            $this->roles[] = $this->datos->fetch_object("WorkflowRol");
        }
    }

    /**
     * @return WorkflowRol[]
     */
    public function getRoles() {
        return $this->roles;
    }

}

class WorkflowPermisos {

    private $datos;

    /**
     * @var WorkflowPermiso[]
     */
    private $permisos = array();

    function __construct() {
        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT * "
                . "FROM " . Constantes::BD_USERS . ".PERMISO ");

        for ($x = 0; $x < $this->datos->num_rows; $x++) {
            $this->addPermiso($this->datos->fetch_object("WorkflowPermiso"));
        }
    }

    function addPermiso($permiso) {
        $this->permisos[] = $permiso;
    }

    /**
     * @return WorkflowPermiso[]
     */
    public function getPermisos() {
        return $this->permisos;
    }

}

class WorkflowUsuarios {

    private $datos;

    /**
     * @var WorkflowUsuario[]
     */
    private $usuarios = array();

    function __construct() {
        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT * "
                . "FROM " . Constantes::BD_USERS . ".USUARIO "
                . "ORDER BY nombre");

        for ($x = 0; $x < $this->datos->num_rows; $x++) {
            $this->addUsuario($this->datos->fetch_object("WorkflowUsuario"));
        }
    }

    function addUsuario($usuario_) {
        $this->usuarios[] = $usuario_;
    }

    public function getUsuario($idusuario_) {
        foreach ($this->usuarios as $WorkflowUsuario) {
            if ($WorkflowUsuario->getIdUsuario() == $idusuario_) {
                return $WorkflowUsuario;
            }
        }
    }

    /**
     * @return WorkflowUsuario[]
     */
    public function getUsuarios() {
        return $this->usuarios;
    }

    public function getUsuariosComunes($email_) {
        $arrayUsuariosComunes = array();
        $usuariosComunes = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT * "
                . "FROM " . Constantes::BD_USERS . ".USUARIO NATURAL JOIN " . Constantes::BD_USERS . ".USUARIO_ROL "
                . "WHERE `email` LIKE '%" . $email_ . "%' AND `idrol` <> 5");

        for ($x = 0; $x < $usuariosComunes->num_rows; $x++) {
            $usuarioComun = $usuariosComunes->fetch_object("WorkflowUsuario");
            $arrayUsuariosComunes[] = $usuarioComun;
        }

        return $arrayUsuariosComunes;
    }

    public function altaGestor($idusuario_) {
        
    }

}

class WorkflowUsuario {

    /**
     * @var int
     */
    private $idusuario;
    private $email;
    private $nombre;
    private $metodoLogin;
    private $estado;
    private $idSec;
    private $datos;

    /**
     * @var WorkflowRol[]
     */
    private $roles = array();

    public function __construct($idusuario_ = null) {

        if ($idusuario_) {
            $this->idusuario = $idusuario_;
        }

        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT * "
                . "FROM " . Constantes::BD_USERS . ".USUARIO "
                . "WHERE idusuario = " . $this->idusuario);
        foreach ($this->datos->fetch_assoc() as $atributo => $valor) {
            $this->{$atributo} = $valor;
        }
        $this->datos = null;

        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT ur.idrol, r.nombre "
                . "FROM " . Constantes::BD_USERS . ".USUARIO_ROL ur "
                . "INNER JOIN " . Constantes::BD_USERS . ".ROL r ON (r.idrol=ur.idrol) "
                . "WHERE ur.idusuario = " . $this->idusuario);

        for ($x = 0; $x < $this->datos->num_rows; $x++) {
            $this->addRol($this->datos->fetch_object("WorkflowRol"));
        }
    }

    /**
     * 
     * @param Int $idRol_
     * @return boolean
     */
    function poseeRol($idRol_) {
        foreach ($this->roles as $Rol) {
            if ($idRol_ == $Rol->getIdRol()) {
                return true;
            }
        }
        return false;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setIdUsuario($idusuario_) {
        $this->idusuario = $idusuario_;
    }

    function getIdUsuario() {
        return $this->idusuario;
    }

    function addRol($rol_) {
        $this->roles[] = $rol_;
    }

    function getEmail() {
        return $this->email;
    }

    function getMetodoLogin() {
        return $this->metodoLogin;
    }

    function setEmail($email_) {
        $this->email = $email_;
    }

    function setMetodoLogin($metodoLogin_) {
        $this->metodoLogin = $metodoLogin_;
    }

    function getRoles() {
        return $this->roles;
    }

    function getEstado() {
        return $this->estado;
    }

    function getIdSec() {
        return $this->idSec;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setIdSec($idSec) {
        $this->idSec = $idSec;
    }

}

class GestorFormularios extends WorkflowUsuario {

    private $limite;
    private $libertad;

    public function __construct($idusuario_ = null) {
        parent::__construct($idusuario_);

        $datosGestion = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT * "
                . "FROM " . Constantes::BD_USERS . ".GESTOR_FORMULARIOS "
                . "WHERE idusuario = " . $idusuario_);

        foreach ($datosGestion->fetch_assoc() as $atributo => $valor) {
            $this->{$atributo} = $valor;
        }
    }

    /**
     * Alterna el valor de la variable libertad entre los valores "Sí" y "No".
     * Se hizo esto en vez de un método setter debido a que esta variable
     * representa a un booleano. Es más simple así que tomar un valor por
     * parámetros sabiendo que siempre va a ser el opuesto.
     * 
     * @author Ariel Machini
     */
    function alternarLibertad() {
        if ($this->libertad == 1) {
            $this->libertad = 0;
        } else { // Si $this->libertad == 0...
            $this->libertad = 1;
        }
    }

    function getLimite() {
        return ($this->limite > -1) ? $this->limite : "Sin límite";
    }

    function getLibertad() {
        return ($this->libertad == 1) ? "Sí" : "No";
    }

    /**
     * Reduce el límite de creación de formularios en 1.
     * Este método fue pensado para ser utilizado cuando el gestor de formularios
     * crea un nuevo formulario y, por supuesto, tiene un límite de creación de
     * formularios asignado.
     * 
     * @author Ariel Machini
     * @return boolean Verdadero si se pudo reducir el límite o falso de lo
     * contrario. Sólo retorna falso si el límite ya no se puede reducir más
     * (si es 0 o si no tiene límite de creación de formularios, en cuyo caso
     * el valor de la variable sería -1).
     */
    function reducirLimite() {
        if ($this->limite > 0) {
            $this->limite -= 1;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Asigna un nuevo límite al gestor de formularios.
     * Este método también asegura que se cumplan con las restricciones de rango
     * pensadas para este atributo.
     * 
     * @author Ariel Machini
     * @param type $limite_ El nuevo límite que se quiere asignar.
     * @return boolean Verdadero si el límite pudo ser cambiado o falso si el
     * valor recibido por parámetros está fuera del rango permitido para la
     * variable límite.
     */
    function setLimite($limite_) {
        if ($limite_ >= -1 && $limite_ <= 32767) {
            $this->limite = $limite_;

            return true;
        } else {
            return false;
        }
    }

}

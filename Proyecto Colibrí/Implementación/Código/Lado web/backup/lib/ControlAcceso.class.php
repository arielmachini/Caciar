<?php

session_start();
include_once 'Constantes.class.php';
include_once 'ObjetoDatos.class.php';

/**
 * Clase de constantes del sistema de Roles y Permisos
 * 
 * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
 */
class PermisosSistema {

    const PERMISO_SALIR = "Salir";
    const PERMISO_LOGIN = "Ingresar";
    const PERMISO_CONSULTAR = "Consultar";
    
    const PERMISO_DOC_ALTA = "Alta Documento";
    const PERMISO_DOC_BAJA = "Baja Documento";    
    const PERMISO_DOC_MOD = "Modificacion Documento";
    
    const PERMISO_MOV = "Movimientos";
    const PERMISO_MOV_MOD = "Modificacion Movimiento";
    
    const PERMISO_REPORTES = "Reportes";
    
    const PERMISO_PARAMETROS = "Parametros";   
    const PERMISO_USUARIOS = "Usuarios";
    const PERMISO_GESTOR = "Gestion formularios";

    /**
     * Administrador del Sistema.
     */
    const ROL_ADMIN = "Administrador";

    /**
     * Usuario de las Áreas. Realiza consultas, registra movimientos.
     */
    const ROL_AGENTE = "Usuario Sector";

    /**
     * Mesa de Entradas. Realiza consultas, registra documentos y movimientos.
     */
    const ROL_MESA_ENTRADA = "Mesa de Entrada";

    /**
     * Usuario de consulta.
     */
    const ROL_CONSULTA = "Usuario Consulta";

    /**
     * Invitado. Usuario no registrado.
     */
    const ROL_INVITADO = "Invitado";

}

class Permiso {

    public $idpermiso;
    public $nombre;

    /**
     *
     * @var mysqli_result 
     */
    protected $datos;

}

class Rol {

    public $idrol;
    public $nombre;

    /**
     *
     * @var Permiso[]
     */
    public $permisos = array();

    /**
     *
     * @var mysqli_result 
     */
    protected $datos;

    /**
     * @todo [12/07/2017] Capturar excepciones de BD para la llamada a setPermisos.
     */
    public function __construct() {
        $this->setPermisos();
    }

    public function setPermisos() {


        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT p.idpermiso, nombre "
                . "FROM " . Constantes::BD_USERS . ".PERMISO p "
                . "JOIN " . Constantes::BD_USERS . ".ROL_PERMISO rp ON p.idpermiso = rp.idpermiso "
                . "WHERE rp.idrol = {$this->idrol} ");

        if (!$this->datos) {
            throw new Exception(ObjetoDatos::getInstancia()->errno, ObjetoDatos::getInstancia()->error);
        }

        for ($x = 0; $x < $this->datos->num_rows; $x++) {
            $this->permisos[] = $this->datos->fetch_object("Permiso");
        }
    }

}

class Usuario {

    const METODO_MANUAL = "Manual";
    const METODO_GOOGLE = "Google";

    public $idusuario;
    public $email;
    public $nombre;
    public $metodologin;

    /**
     *
     * @var mysqli_result 
     */
    protected $datos;

    /**
     *
     * @var Rol[] 
     */
    public $roles;

    /**
     * 
     * @param String $email_
     * @param String $metodo_
     * 
     * @throws Exception
     * @since v2.0 2017-08-14 - Desactiva el autoregistro de usuarios.
     * 
     */
    function __construct($email_, $metodo_ = self::METODO_MANUAL) {

        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT idusuario, nombre "
                . "FROM " . Constantes::BD_USERS . ".USUARIO "
                . "WHERE email = '{$email_}' "
                . "AND metodologin = '{$metodo_}'");

        if (!$this->datos) {
            throw new Exception(ObjetoDatos::getInstancia()->error, ObjetoDatos::getInstancia()->errno);
        }
        if (!$this->datos->num_rows) {
            return null;
        }

        /*
         * 2. Datos Encontrados. OK
         */
        foreach ($this->datos->fetch_assoc() as $atributo => $valor) {
            $this->{$atributo} = $valor;
        }

        $this->setRoles();

        /*
         * 
         */
    }

    public function setRoles() {

        $this->datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "SELECT r.idrol, r.nombre "
                . "FROM " . Constantes::BD_USERS . ".USUARIO u "
                . "JOIN " . Constantes::BD_USERS . ".USUARIO_ROL ur ON (u.idusuario = ur.idusuario) "
                . "JOIN " . Constantes::BD_USERS . ".ROL r ON (r.idrol = ur.idrol) "
                . "WHERE u.idusuario = {$this->idusuario} ");

        for ($x = 0; $x < $this->datos->num_rows; $x++) {
            $this->roles[] = $this->datos->fetch_object("Rol");
        }
    }

}

class UsuarioGoogle extends Usuario {

    public $googleid;
    public $imagen;

    /**
     * 
     * @param String $email_
     * @param String $googleid_
     * @param String $imagen_
     * @param String $nombre_
     * @throws Exception
     * 
     * @since v2.0 2017-08-14
     * Desactiva el autoregistro de usuarios.
     * 
     */
    function __construct($email_, $googleid_, $imagen_, $nombre_) {

        try {
            parent::__construct($email_, Usuario::METODO_GOOGLE);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->email = $email_;
        $this->googleid = $googleid_;
        $this->imagen = $imagen_;
        $this->nombre = $nombre_;
        $this->metodologin = Usuario::METODO_GOOGLE;

        if ($this->idusuario == null) {
            $this->registraUsuarioGoogle();
        }
    }

    /**
     * 
     * Crear un usuario estándar a partir del login con una cuenta Google.
     * Registra los datos en la base de datos. Tablas: usuario, usuario_google, usuario_rol.
     * 
     * @return Int id del usuario creado en la base de datos.
     * 
     */
    function registraUsuarioGoogle() {

        
        ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "INSERT INTO " . Constantes::BD_USERS . ".USUARIO "
                . "VALUES (NULL, '{$this->email}', '{$this->nombre}', '" . Usuario::METODO_GOOGLE . "', 'Activo' )");

        $this->idusuario = (Int) ObjetoDatos::getInstancia()->insert_id;

        ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "INSERT INTO " . Constantes::BD_USERS . ".USUARIO_GOOGLE "
                . "VALUES ({$this->idusuario}, {$this->googleid}, '{$this->imagen}')");


        ObjetoDatos::getInstancia()->ejecutarQuery(""
                . "INSERT INTO " . Constantes::BD_USERS . ".USUARIO_ROL "
                . "SELECT idrol, {$this->idusuario} "
                . "FROM " . Constantes::BD_USERS . ".ROL "
                . "WHERE nombre = '" . PermisosSistema::ROL_CONSULTA . "'");
    }

}

/**
 * 
 * Clase para mantener control de acceso al sistema.
 * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
 * 
 */
class ControlAcceso {

    public $datos;
    public $ubicacion;
    static $clasesMetodo = array("manual" => "Usuario", "google" => "UsuarioGoogle");

    /**
     * 
     * Verifica si el usuario posee un permiso y en caso contrario lo redirecciona a la Home.
     * 
     * @param String $permiso_
     * @return void 
     * 
     * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
     * 
     */
    static function requierePermiso($permiso_) {
        if (!self::verificaPermiso($permiso_, $_SESSION['usuario'])) {
            header("Location: " . Constantes::HOMEURL);
        }
    }

    /**
     * 
     * Verifica si un usuario posee un permiso específico.
     * @static
     * 
     * @param String $permiso_
     * @param Usuario $Usuario Obtenido de la Sesion
     * @return Boolean Description
     * 
     */
    static function verificaPermiso($permiso_) {
        $Usuario = $_SESSION['usuario'];
        if ($Usuario == null) {
            return false;
        }
        foreach ($Usuario->roles as $Rol) {
            foreach ($Rol->permisos as $Permiso) {
                if ($permiso_ == $Permiso->nombre) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Verifica si el usuario está logueado en el sistema (cargado en la sesión)
     * @static
     * 
     * @return mixed Redirecciona a la Home del sistema caso el usuario no esté logueado.
     */
    static function verificaLogin() {
        if (!isset($_SESSION['usuario']) || (!is_a($_SESSION['usuario'], "Usuario"))) {
            header("Location: " . Constantes::HOMEURL);
        }
        return null;
    }

    /**
     * 
     * @param type $email_
     * @param type $metodo_
     * @static
     * 
     * @todo [15/06/2017] El método está pensado para instanciar usuarios Google. Se debe generalizar.
     * @since v. 2.0 2017-08-14 - El método deja de ser estático. Autoregistro desactivado.
     * 
     * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
     * 
     */
    function creaSesion($email_, $metodo_ = Usuario::METODO_MANUAL, $googleid_ = null, $imagen_ = null, $nombre_ = null) {
        try {
            $Usuario = new UsuarioGoogle($email_, $googleid_, $imagen_, $nombre_);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $_SESSION['usuario'] = $Usuario;
    }

    /**
     * 
     * @since v2.0 2017-08-14
     * Desactiva el autoregistro de usuarios.
     * 
     * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
     * @todo [14/08/2017] Terminar el tratamiento de error caso el Usuario no exista.
     * 
     */
    function __construct() {

        $this->ubicacion = Constantes::SERVER . $_SERVER["PHP_SELF"];

        /**
         * Verificación Inicial del Usuario caso la página no sea el index.
         */
        if ($this->ubicacion != Constantes::HOMEURL) {
            unset($_SESSION["HTTP_REFERER"]);
            self::verificaLogin();
        } else {
            $_SESSION["HTTP_REFERER"] = Constantes::HOMEURL;
        }

        /**
         * Crea la sesión del Usuario caso la página de origen de los datos pasados por formulario sea el index.
         */
        if (isset($_SESSION["HTTP_REFERER"]) && $_SESSION["HTTP_REFERER"] == Constantes::HOMEURL && isset($_POST['email'])) {
            try {
                $this->creaSesion($_POST['email'], Usuario::METODO_GOOGLE, $_POST['googleid'], $_POST['imagen'], $_POST['nombre']);
            } catch (Exception $e) {
                echo "<script>alert('{$e->getMessage()}');</script>";
                die($e->getMessage());
            }
            $this->redireccionaIndex();
        }


        /**
         * Luego de loguear, redireccion al index correspondiente a cada usuario.
         */
        if (
                ($this->ubicacion == Constantes::HOMEURL) &&
                (isset($_SESSION['usuario'])) &&
                (is_a($_SESSION['usuario'], 'Usuario'))
        ) {
            $this->redireccionaIndex();
        }
    }

    /**
     * 
     */
    function redireccionaIndex() {
        $this->ubicacion = Constantes::HOMEAUTH;
        header("Location: {$this->ubicacion}");
    }

}

$ControlAcceso = new ControlAcceso();

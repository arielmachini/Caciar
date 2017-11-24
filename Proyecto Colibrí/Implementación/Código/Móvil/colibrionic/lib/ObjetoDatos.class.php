<?php

class ConfiguracionesBD {

    const BD_HOST = "10.0.0.121";
    const BD_USUARIO = "root";
    const BD_CLAVE = "colibri";
    const BD_SCHEMA = "uargflow";

}

/**
 * Description of ObjetoDatos
 *
 * @author esantos
 * 
 */
class ObjetoDatos extends mysqli {

    protected $datos;
    protected $consulta;
    public static $instancia;

    function __construct() {
        parent::__construct(ConfiguracionesBD::BD_HOST, ConfiguracionesBD::BD_USUARIO, ConfiguracionesBD:: BD_CLAVE, ConfiguracionesBD::BD_SCHEMA);
        if ($this->connect_error) {
            throw new Exception($this->connect_error, $this->connect_errno);
        }
    }

    function getQuery() {
        return $this->consulta;
    }

    function setQuery($query) {
        $this->consulta = $query;
    }

    function setDatos($consulta_ = null) {
        $this->datos = $this->query(isset($consulta_) ? $consulta_ : $this->consulta);
    }

    /**
     * 
     * @param type $consulta_
     * @return mysqli_result
     */
    function ejecutarQuery($consulta_ = null) {
        return $this->query(isset($consulta_) ? $consulta_ : $this->consulta);
    }

    /**
     * 
     * @return mysqli_result
     */
    static function getDatos() {
        return $this->datos;
    }

    /**
     * 
     * @return ObjetoDatos
     */
    public static function getInstancia() {
        if (!self::$instancia instanceof self) {
            try {
                self::$instancia = new self;
            } catch (Exception $e) {
                die("Error de Conexion a la BD: " . $e->getCode() . ".");
            }
        }
        return self::$instancia;
    }

}

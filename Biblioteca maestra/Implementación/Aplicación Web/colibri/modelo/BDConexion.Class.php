<?php

/**
 * Esta clase implementa la conexión a una base de datos mediante el patrón
 * Singleton.
 * Fue modificada por Ariel Machini <arielmachini@pm.me> para que funcione con
 * el sistema Colibrí.
 *
 * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
 * 
 * @uses mysqli Libería estándar de PHP para acceder a bases de datos MySQL
 * @see https://es.wikipedia.org/wiki/Singleton
 */
class BDConexion extends mysqli {

    private $host, $usuario, $contrasenia;
    public static $instancia;

    function __construct() {
        $this->host = "localhost";
        $this->usuario = "root";
        $this->contrasenia = "titpfa312";

        parent::__construct($this->host, $this->usuario, $this->contrasenia);

        if ($this->connect_errno) {
            throw new Exception("Error de Conexion a la Base de Datos", $this->connect_errno);
        }
    }

    /**
     * 
     * @return BDConexion
     */
    public static function getInstancia() {
        if (!self::$instancia instanceof self) {
            try {
                self::$instancia = new self;
            } catch (Exception $e) {
                die("Error de Conexion a la Base de Datos: " . $e->getCode() . ".");
            }
        }
        
        /*
         * La siguiente consulta es necesaria para que los valores con caracteres
         * "foráneos al inglés" (como "á" o "ñ") se muestren correctamente.
         */
        self::$instancia->query("SET CHARACTER SET 'utf8'");

        return self::$instancia;
    }

}

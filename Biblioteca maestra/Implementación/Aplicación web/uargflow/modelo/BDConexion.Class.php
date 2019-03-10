<?php

/**
 * Esta clase implementa la conexión a una base de datos mediante el patrón
 * Singleton.
 * Fue modificada por Ariel Machini <arielmachini@pm.me> para incluir
 * funcionalidades requeridas para el correcto funcionamiento del sistema
 * Colibrí.
 *
 * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
 * 
 * @uses mysqli Libería estándar de PHP para acceder a bases de datos MySQL
 * @see https://es.wikipedia.org/wiki/Singleton
 */
class BDConexion extends mysqli {

    private $host, $usuario, $contrasenia, $schema;
    public static $instancia;
    
    /**
     * El constructor de esta clase fue modificado para que pueda aceptar otros
     * nombres de esquema.
     * 
     * @param type $schema_ Nombre del esquema (opcional). Si este parámetro no
     * se completa, entonces toma el valor por defecto "bdUsuarios".
     * @throws Exception
     */
    function __construct($schema_) {
        $this->host = "localhost";
        $this->usuario = "root";
        $this->contrasenia = "titpfa312";
        
        $this->schema = $schema_;

        parent::__construct($this->host, $this->usuario, $this->contrasenia, $this->schema);

        if ($this->connect_errno) {
            throw new Exception("Error de Conexion a la Base de Datos", $this->connect_errno);
        }
    }
    
    /**
     * Este método destruye cualquier instancia de la clase que exista. Esto se
     * hace por si es necesario cambiar de esquema.
     * 
     * @author Ariel Machini.
     */
    public static function destruirInstancia() {
        self::$instancia = null;
    }
    
    /** 
     * @return BDConexion
     */
    public static function getInstancia($schema_ = null) {
        if (null === $schema_) {
            $schema_ = "bdUsuarios";
        }

        if (!self::$instancia instanceof self) {
            try {
                self::$instancia = new self($schema_);
            } catch (Exception $e) {
                die("Error de Conexion a la Base de Datos: " . $e->getCode() . ".");
            }
        }
        return self::$instancia;
    }

}

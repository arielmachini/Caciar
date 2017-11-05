<?php

include_once '../gui/GUI.class.php';
setlocale(LC_TIME, 'es_AR.utf8');

/**
 * 
 * Clase para mantener las constantes de sistema
 * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
 * 
 */
class Constantes {

    
    const NOMBRE_SISTEMA = "UARGFlow";
    
    const WEBROOT = "/var/www/html/uargflow/";
    const APPDIR = "uargflow";
        
    const SERVER = "http://localhost";
    const APPURL = "http://localhost/uargflow";
    const HOMEURL = "http://localhost/uargflow/app/index.php";
    const HOMEAUTH = "http://localhost/uargflow/app/workflow.usuarios.ver.php";
    
    const BD_SCHEMA = "uargflow";
    const BD_USERS = "uargflow";
    
}

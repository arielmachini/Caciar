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

    
    const NOMBRE_SISTEMA = "Colibri";
    
    const WEBROOT = "/var/www/html/";
    const APPDIR = "colibri";
        
    const SERVER = "http://localhost";
    const APPURL = "http://localhost/colibri";
    const HOMEURL = "http://localhost/colibri/workflow/index.php";
    const HOMEAUTH = "http://localhost/colibri/workflow/workflow.usuarios.ver.php";
    
    const BD_SCHEMA = "uargflow";
    const BD_USERS = "uargflow";
    
}

<?php

include_once '../gui/GUI.class.php';
setlocale(LC_TIME, 'es_AR.utf8');

/**
 * Esta clase almacena las constantes utilizadas dentro del sistema.
 * @author Ariel Machini <arielmachini@protonmail.com>
 */
class Constantes {

    
    const NOMBRE_SISTEMA = "Sistema Colibrí";
    
    const WEBROOT = "/var/www/html/colibri/";
    const APPDIR = "colibri";
        
    const SERVER = "http://localhost";
    const APPURL = "http://localhost/colibri";
    const HOMEURL = "http://localhost/colibri/app/index.php";
    const HOMEAUTH = "http://localhost/colibri/app/workflow.usuarios.ver.php";
    
    const BD_SCHEMA = "uargflow";
    const BD_USERS = "uargflow";
    
}

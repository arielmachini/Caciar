<?php

setlocale(LC_TIME, 'es_AR.utf8');

/**
 * 
 * Clase para mantener las directivas de sistema.
 * Deben coincidir con las configuraciones del proyecto.
 * 
 * @author Eder dos Santos <esantos@uarg.unpa.edu.ar>
 * 
 */
class Constantes {

    const NOMBRE_SISTEMA = "Colibrí";
    const WEBROOT = "/var/www/html/colibri/";
    const APPDIR = "colibri";
    const SERVER = "http://localhost";
    const APPURL = "http://localhost/colibri";
    const HOMEURL = "http://localhost/colibri/app/index.php";
    const HOMEAUTH = "http://localhost/colibri/app/formularios.php";

}

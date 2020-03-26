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
    
    /* Constantes propias del sistema Colibrí */
    const FORMSURL = self::APPURL . "/app/formularios.php";
    const VERFORMURL = self::APPURL . "/app/formulario.ver.php";
    const ENVIARFORMURL = self::APPURL . "/app/formulario.enviar.php";
    const NOSCRIPTURL = self::APPURL . "/app/noscript.php";

    const NOMBRE_SISTEMA = "Colibrí";
    const WEBROOT = "/var/www/html/colibri/";
    const APPDIR = "colibri";
    const SERVER = "http://localhost";
    const APPURL = "http://localhost/colibri";
    const HOMEURL = self::APPURL . "/app/index.php";
    const HOMEAUTH = Constantes::FORMSURL;
    
    /* Constantes utilizadas por la aplicación móvil del sistema Colibrí */
    const LLAVE = '8c8b6c186ad4940044533c4303a0ab5c';

}

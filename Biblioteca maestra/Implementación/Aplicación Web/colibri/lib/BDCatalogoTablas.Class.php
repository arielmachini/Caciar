<?php

include_once 'BDCatalogoEsquemas.Class.php';

/**
 * Esta clase contiene todos los nombres de las tablas utilizadas en el sistema
 * Colibrí.
 *
 * @author Ariel Machini <arielmachini@pm.me>
 */
class BDCatalogoTablas {
    
    /* Tablas correspondientes a bdUsuarios. */
    const BD_TABLA_PERMISO = BDCatalogoEsquemas::BD_ESQUEMA_USUARIOS . ".permiso";
    const BD_TABLA_ROL = BDCatalogoEsquemas::BD_ESQUEMA_USUARIOS . ".rol";
    const BD_TABLA_ROL_PERMISO = BDCatalogoEsquemas::BD_ESQUEMA_USUARIOS . ".rol_permiso";
    const BD_TABLA_USUARIO = BDCatalogoEsquemas::BD_ESQUEMA_USUARIOS . ".usuario";
    const BD_TABLA_USUARIO_ROL = BDCatalogoEsquemas::BD_ESQUEMA_USUARIOS . ".usuario_rol";

    /* Tablas correspondientes a bdFormularios. */
    const BD_TABLA_AREA_TEXTO = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".area_texto";
    const BD_TABLA_BOTON_RADIO = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".boton_radio";
    const BD_TABLA_CAMPO = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".campo";
    const BD_TABLA_CAMPO_TEXTO = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".campo_texto";
    const BD_TABLA_CHECKBOX = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".checkbox";
    const BD_TABLA_FECHA = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".fecha";
    const BD_TABLA_FORMULARIO = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".formulario";
    const BD_TABLA_FORMULARIO_ROL = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".formulario_rol";
    const BD_TABLA_LISTA_BOTON_RADIO = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".lista_boton_radio";
    const BD_TABLA_LISTA_CHECKBOX = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".lista_checkbox";
    const BD_TABLA_LISTA_DESPLEGABLE = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".lista_desplegable";
    const BD_TABLA_OPCION = BDCatalogoEsquemas::BD_ESQUEMA_FORMULARIOS . ".opcion";

}

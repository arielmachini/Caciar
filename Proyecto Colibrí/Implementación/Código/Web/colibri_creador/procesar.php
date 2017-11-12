<?php

require_once realpath("./Campos.class.php");
require_once realpath("./Formulario.class.php");

if (isset(filter_input(INPUT_POST, "formulario"))) {
    ObjetoDatos::getInstancia()->autocommit(false);
    ObjetoDatos::getInstancia()->begin_transaction();
    
    /* Con esta variable se monitorea el éxito o el fallo de la operación */
    $transaccionRealizada;
    
    $Formulario = new Formulario(getdate()['year'] . "/" . getdate()['mon'] . "/" . getdate()['mday']);
    
    $formularioRecibido = json_decode(filter_input(INPUT_POST, "formulario"));
    $rolesDestinatarios = filter_input(INPUT_POST, "rolesDestinatarios");
    
    $i = 0;
    
    foreach ($formularioRecibido as $campoFormulario) {
        $i++;
        
        /* Se comprueba qué tipo de campo es y después se asignan las propiedades */
        $tipoCampoActual = $campoFormulario->tipoCampo;
        
        if ($tipoCampoActual === "CampoTexto") {
            $CampoTexto = new CampoTexto();
            
            $CampoTexto->setDescripcion($campoFormulario->descripcion);
            $CampoTexto->setEsObligatorio($campoFormulario->obligatorio);
            $CampoTexto->setPista($campoFormulario->pista);
            $CampoTexto->setPosicion($i);
            $CampoTexto->setTitulo($campoFormulario->titulo);
            
            $Formulario->agregarCampo($CampoTexto);
        } else if ($tipoCampoActual === "AreaTexto") {
            $AreaTexto = new AreaTexto();
            
            $AreaTexto->setDescripcion($campoFormulario->descripcion);
            $AreaTexto->setEsObligatorio($campoFormulario->obligatorio);
            $AreaTexto->setLimiteCaracteres($campoFormulario->limiteCaracteres);
            $AreaTexto->setPosicion($i);
            $AreaTexto->setTitulo($campoFormulario->titulo);
        } else if ($tipoCampoActual === "ListaDesplegable") {
            $ListaDesplegable = new ListaDesplegable();
            
            $ListaDesplegable->setDescripcion($campoFormulario->descripcion);
            $ListaDesplegable->setEsObligatorio($campoFormulario->obligatorio);
            $ListaDesplegable->setPosicion($i);
            $ListaDesplegable->setTitulo($campoFormulario->titulo);
            
            foreach($campoFormulario->opciones as $opcion) {
                $ListaDesplegable->agregarOpcion($opcion);
            }
            
            $Formulario->agregarCampo($ListaDesplegable);
        }
    }
    
    $Formulario.setDescripcion(filter_input(INPUT_POST, "descripcion"));
    $Formulario.setEmailReceptor(filter_input(INPUT_POST, "destinatario"));
    $Formulario.setFechaInicio(filter_input(INPUT_POST, "fechaApertura"));
    $Formulario.setFechaFin(filter_input(INPUT_POST, "fechaCierre"));
    $Formulario.setTitulo(filter_input(INPUT_POST, "titulo"));
    
    foreach ($rolesDestinatarios as $idrol) {
        $Formulario.agregarDestinatario($idrol);
    }
    
    $transaccionRealizada = ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "")
}
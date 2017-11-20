<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';

$WorkflowRoles = new WorkflowRoles();
ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();

/* Se guardan las variables del array POST y SESSION en variables locales: */
$idrol;
$idusuario = filter_input(INPUT_GET, idusuario);

foreach ($WorkflowRoles->getRoles() as $Rol) {
    if ($Rol->getNombre() == "Usuario Consulta") {
        $idrol = $Rol->getIdRol();
    }
}

/* Primero se cambia el rol del usuario en el sistema por Usuario Consulta. */
ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "UPDATE  " . Constantes::BD_USERS . ".USUARIO_ROL " .
        "SET `idrol` = " . $idrol . " " .
        "WHERE `idusuario` = " . $idusuario);

/* Después se elimina la tupla correspondiente al usuario de la tabla GESTOR_FORMULARIOS */
ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "DELETE FROM " . Constantes::BD_USERS . ".GESTOR_FORMULARIOS " .
        "WHERE `idusuario` <=> " . $idusuario);

ObjetoDatos::getInstancia()->commit();
?>
<script type="text/javascript">
    window.open("gestion_gestores.php", "_self");
</script>
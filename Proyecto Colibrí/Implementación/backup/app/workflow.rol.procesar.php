<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';

$WorkflowPermisos = new WorkflowPermisos();
ObjetoDatos::getInstancia()->autocommit(false); 
ObjetoDatos::getInstancia()->begin_transaction();

ObjetoDatos::getInstancia()->ejecutarQuery("".
        "UPDATE  " . Constantes::BD_USERS . ".ROL " .
        "SET nombre = '" .$_POST['nombreRol'] . "' " .
        "WHERE idrol = " . $_POST['idRol']);


ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "DELETE FROM " . Constantes::BD_USERS . ".ROL_PERMISO " .
        "WHERE idrol = " . $_POST['idRol']);


foreach ($WorkflowPermisos->getPermisos() as $WorkflowPermiso) {
    if (isset($_POST["permiso_" . $WorkflowPermiso->getIdPermiso()])) {
        ObjetoDatos::getInstancia()->ejecutarQuery("" .
                "INSERT " .
                "INTO " . Constantes::BD_USERS . ".ROL_PERMISO ".
                "VALUES " .
                "({$_POST['idRol']} ,{$WorkflowPermiso->getIdPermiso()})");
    }
}
ObjetoDatos::getInstancia()->commit();
?>
<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>El rol "<?php echo $_POST['nombreRol']; ?>" ha sido actualizado con &eacute;xito.</h3>
                     <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.roles.ver.php">
                            <input type="button" value="Ver Roles" />
                        </a>
                    </fieldset>   
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>


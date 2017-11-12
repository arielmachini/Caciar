<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';

$WorkflowRoles = new WorkflowRoles();
ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();

ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "UPDATE  " . Constantes::BD_USERS . ".USUARIO " .
        "SET nombre = '{$_POST['nombre']}', "
        . "email = '{$_POST['email']}', "
        . "estado = '{$_POST['estado']}', "
        . "idSec = {$_POST['idSec']} " .
        "WHERE idusuario = " . $_POST['idUsuario']);

ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "DELETE FROM " . Constantes::BD_USERS . ".USUARIO_ROL " .
        "WHERE idusuario = " . $_POST['idUsuario']);

if (isset($_POST["idrol"])) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT " .
            "INTO " . Constantes::BD_USERS . ".USUARIO_ROL " .
            "VALUES " .
            "({$_POST["idrol"]}, {$_POST['idUsuario']})");
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
                    <h3>El usuario ha sido actualizado con &eacute;xito</h3>
                     <fieldset>
                        <legend>Opciones</legend>
                        <a href="workflow.usuarios.ver.php">
                            <input type="button" value="Ver Usuarios" />
                        </a>
                    </fieldset>   
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>


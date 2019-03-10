<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

include_once '../modelo/Usuario.Class.php';

/*
 * ID del usuario al cual se le revocarán los permisos de gestión de
 * formularios.
 */
$idUsuario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

$Usuario = new Usuario($idUsuario);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Dar de baja a un gestor de formularios</title>

    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>
        <div class="container">
            <form action="gestor.baja.procesar.php" method="post">
                <div class="card">
                    <div class="card-header">
                        <h3>Dar de baja a un gestor de formularios</h3>
                    </div>
                    <div class="card-body">
                        <p>¿Confirma que desea dar de baja como gestor de formularios a <b><?= $Usuario->getNombre(); ?></b> (<?= $Usuario->getEmail(); ?>)? Tenga en cuenta que los formularios que ya haya creado en el sistema no se eliminarán, pero si continúa dichos formularios sólo podrán ser gestionados por un administrador.</p>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="idUsuario" class="form-control" value="<?= $idUsuario ?>" >
                        <button type="submit" class="btn btn-outline-success">
                            <span class="oi oi-ban"></span> Confirmar (dar de baja)
                        </button>
                        <a href="gestores.php">
                            <button type="button" class="btn btn-outline-danger">
                                <span class="oi oi-x"></span> Cancelar
                            </button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <?php include_once '../gui/footer.php'; ?>
    </body>
    
</html>
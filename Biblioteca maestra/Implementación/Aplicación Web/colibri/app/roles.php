<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ROLES);

include_once '../modelo/ColeccionRoles.php';

$ColeccionRoles = new ColeccionRoles();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>        
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Roles</title>
    </head>
    <body>

        <?php include_once '../gui/navbar.php'; ?>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Roles</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-sm">
                        <p>
                            <a class="btn btn-success" href="rol.crear.php">
                                <span class="oi oi-plus"></span> Nuevo Rol
                            </a>
                        </p>
                        <tr class="table-info" style="color: #0c5460;">
                            <th>Nombre</th>
                            <th>Opciones</th>
                        </tr>

                        <?php foreach ($ColeccionRoles->getRoles() as $Rol) {
                            if ($Rol->getId() != PermisosSistema::IDROL_PUBLICO_GENERAL && $Rol->getNombre() != PermisosSistema::ROL_ESTANDAR && $Rol->getNombre() != PermisosSistema::ROL_GESTOR) { ?>
                            <tr>
                                <td><?= $Rol->getNombre(); ?></td>
                                <td>
                                    <a class="btn btn-outline-info" title="Ver detalle" href="rol.ver.php?id=<?= $Rol->getId(); ?>">
                                        <span class="oi oi-zoom-in"></span>
                                    </a>
                                    <a class="btn btn-outline-warning" title="Modificar" href="rol.modificar.php?id=<?= $Rol->getId(); ?>">
                                        <span class="oi oi-pencil"></span>
                                    </a>
                                    <a class="btn btn-outline-danger" title="Eliminar" href="rol.eliminar.php?id=<?= $Rol->getId(); ?>">
                                        <span class="oi oi-trash"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                    </table>
                </div>
            </div>
        </div>
        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>


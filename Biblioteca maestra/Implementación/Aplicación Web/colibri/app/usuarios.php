<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);

include_once '../modelo/ColeccionUsuarios.php';

$ColeccionUsuarios = new ColeccionUsuarios();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>        
        <title><?= Constantes::NOMBRE_SISTEMA; ?> - Usuarios</title>
    </head>
    <body>

        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">

            <div class="card">
                <div class="card-header">
                    <h3>Usuarios</h3>
                </div>
                <div class="card-body">
                    <p>
                        <a class="btn btn-success" href="usuario.crear.php">
                            <span class="oi oi-plus"></span> Nuevo Usuario
                        </a>
                    </p>
                    <table class="table table-hover table-sm">
                        <tr class="table-info" style="color: #0c5460;">
                            <th>Usuario</th>
                            <th>Opciones</th>
                        </tr>
                        <tr>
                            <?php foreach ($ColeccionUsuarios->getUsuarios() as $Usuario) {
                                ?>
                                <td><?= $Usuario->getNombre(); ?><br /><?= $Usuario->getEmail(); ?></td>
                                <td>
                                    <a class="btn btn-outline-info" title="Ver detalle" href="usuario.ver.php?id=<?= $Usuario->getId(); ?>">
                                        <span class="oi oi-zoom-in"></span>
                                    </a>
                                    <a class="btn btn-outline-warning" title="Modificar" href="usuario.modificar.php?id=<?= $Usuario->getId(); ?>">
                                        <span class="oi oi-pencil"></span>
                                    </a>
                                    <a class="btn btn-outline-danger" title="Eliminar" href="usuario.eliminar.php?id=<?= $Usuario->getId(); ?>">
                                        <span class="oi oi-trash"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>


<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

include_once '../modelo/ColeccionRoles.php';
include_once '../modelo/ColeccionUsuarios.php';

$ColeccionRoles = new ColeccionRoles();
$ColeccionUsuarios = new ColeccionUsuarios();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Administrar gestores de formularios</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Administrar gestores de formularios</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="table-info">
                                <th scope="col">Usuario</th>
                                <th scope="col">Dirección de e-mail</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $idRolGestorFormularios;

                            foreach ($ColeccionRoles->getRoles() as $Rol) {
                                if ($Rol->getNombre() === "Gestor de formularios") {
                                    $idRolGestorFormularios = $Rol->getId();

                                    break 1;
                                }
                            }

                            foreach ($ColeccionUsuarios->getUsuarios() as $Usuario) {
                                if ($Usuario->buscarRolPorId($idRolGestorFormularios)) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $Usuario->getNombre(); ?>
                                        </td>
                                        <td>
                                            <?= $Usuario->getEmail(); ?>
                                        </td>
                                        <td>
                                            <a href="gestor.baja.php?id=<?= $Usuario->getId(); ?>">
                                                <button type="button" class="btn btn-outline-danger">
                                                    <span class="oi oi-ban"></span> Dar de baja
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="gestor.alta.php">
                        <button type="button" class="btn btn-success">
                            <span class="oi oi-plus"></span> Nuevo gestor de formularios
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>


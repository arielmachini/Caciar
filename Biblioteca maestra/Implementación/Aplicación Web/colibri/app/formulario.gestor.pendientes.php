<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

include_once '../modelo/Usuario.Class.php';
require_once '../modelo/BDConexion.Class.php';

$formulariosPendientes = BDConexion::getInstancia()->query("" .
        "SELECT * " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `estaHabilitado` = 0");
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        
        <!-- Scripts requeridos por el sistema Colibrí -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Gestionar formularios pendientes</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Gestionar formularios pendientes</h3>
                </div>
                <div class="card-body">
                    <p>Desde aquí podrá ver y aprobar/eliminar aquellos formularios que estén deshabilitados.</p>

                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="oi oi-magnifying-glass"></span></div>
                        </div>

                        <input class="form-control" id="filtrarFormularios" placeholder="Puede filtrar formularios por su título">
                    </div>
                    <br/>

                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="table-info" style="color: #0c5460;">
                                <th scope="col">Título del formulario</th>
                                <th scope="col">Fecha de creación</th>
                                <th scope="col">Creado por</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>

                        <tbody id="listaFormularios">
                            <?php while ($formulario = $formulariosPendientes->fetch_assoc()) { ?>

                                <tr>
                                    <td style="vertical-align: middle;">
                                        <?php
                                        $tituloFormulario= $formulario['titulo'];
                                        
                                        echo $tituloFormulario;
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle;"><?= $formulario['fechaCreacion']; ?></td>
                                    <td style="vertical-align: middle;">
                                        <?php
                                        $nombreCreador = BDConexion::getInstancia()->query("" .
                                                        "SELECT `nombre` " .
                                                        "FROM " . BDCatalogoTablas::BD_TABLA_USUARIO . " " .
                                                        "WHERE `id` = {$formulario['idCreador']}")->fetch_assoc()['nombre'];

                                        echo $nombreCreador;
                                        ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <?php $idFormulario = $formulario['idFormulario']; ?>
                                        <a class="btn btn-light" href="formulario.ver.php?id=<?= $idFormulario; ?>" style="margin-bottom: 2px;" title="Haga clic aquí para abrir una nueva ventana con una vista previa de este formulario." target="_blank">
                                            <span class="oi oi-eye"></span> Vista previa
                                        </a>
                                        
                                        <button class="btn btn-outline-success" style="margin-bottom: 2px;" onclick="aprobarFormulario('<?= $idFormulario; ?>', '<?= $tituloFormulario; ?>', '<?= $nombreCreador; ?>')" title="Habilitar este formulario." type="button">
                                            <span class="oi oi-check"></span> Aprobar
                                        </button>
                                        
                                        <a class="btn btn-outline-danger" href="formulario.eliminar.php?id=<?= $idFormulario; ?>" style="margin-bottom: 2px;" title="Eliminar este formulario.">
                                            <span class="oi oi-trash"></span> Eliminar
                                        </a>
                                    </td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="formulario.gestor.php">
                        <span class="oi oi-account-logout"></span> Volver
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

    <script type="text/javascript">
        $("#filtrarFormularios").keyup(function () {
            var palabraClave, listaFormularios;
            palabraClave = this.value.toUpperCase();
            listaFormularios = $("#listaFormularios").find("tr");

            for (var i = 0; i < listaFormularios.length; i++) {
                var tituloFormulario = listaFormularios[i].getElementsByTagName("td")[0].textContent;

                if (tituloFormulario.toUpperCase().indexOf(palabraClave) > -1) {
                    listaFormularios[i].style.display = "";
                } else {
                    listaFormularios[i].style.display = "none";
                }
            }
        });

        /* Se habilitan los tooltips de jQuery. */
        $(document).tooltip({
            show: {
                effect: "fade",
                delay: 0,
                duration: 275
            }
        });
        
        function aprobarFormulario(idFormulario, tituloFormulario, nombreCreador) {
            $.confirm({
                icon: 'oi oi-signpost',
                title: 'Aprobar formulario',
                content: '¿Está seguro de que desea aprobar el formulario «<b>' + tituloFormulario + '</b>», creado por ' + nombreCreador + '?',
                animation: 'none',
                closeAnimation: 'none',
                theme: 'material',
                type: 'green',
                useBootstrap: false,
                buttons: {
                    confirm: {
                        btnClass: 'btn-green',
                        text: 'Aprobar',
                        action: function () {
                            window.location.href = 'formulario.modificar.estado.php?id=' + idFormulario + '&estado=1';
                        }
                    },
                    cancelar: {
                        text: 'Cancelar'
                    }
                }
            });
        }
    </script>

</html>

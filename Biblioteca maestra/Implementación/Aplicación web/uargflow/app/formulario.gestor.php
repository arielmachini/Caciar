<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

include_once '../modelo/ColeccionRoles.php';
require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

$ColeccionRoles = new ColeccionRoles();
$idAdministradorGestores = null;
$usuario = new Usuario($_SESSION['usuario']->id);

foreach ($ColeccionRoles->getRoles() as $Rol) {
    if ($Rol->getNombre() === "Administrador de gestores de formularios") {
        $idAdministradorGestores = $Rol->getId();
        
        break;
    }
}

BDConexion::destruirInstancia();

if ($usuario->buscarRolPorId($idAdministradorGestores)) {
    $query = "" .
            "SELECT * " .
            "FROM `formulario`";
} else {
    $query = "" .
            "SELECT * " .
            "FROM `formulario` " .
            "WHERE `idCreador` = {$_SESSION['usuario']->id}";
}

$formularios = BDConexion::getInstancia("bdFormularios")->query($query);
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Gestor de formularios</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Gestor de formularios</h3>
                </div>
                <div class="card-body">
                    <p>Aquí podrá ver y gestionar todos los formularios en el sistema que estén bajo su responsabilidad.</p>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="oi oi-magnifying-glass"></span></div>
                        </div>

                        <input class="form-control" id="filtrarFormularios" placeholder="Puede filtrar formularios por su título">
                    </div>
                    <br/>

                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="table-info">
                                <th scope="col">Título del formulario</th>
                                <th scope="col">Fecha de creación</th>
                                <th scope="col">Estado</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>

                        <tbody id="listaFormularios">
                            <?php while ($formulario = $formularios->fetch_assoc()) { ?>
                            <tr>
                                <td style="vertical-align: middle;"><?= $formulario['titulo']; ?></td>
                                <td style="vertical-align: middle;"><?= $formulario['fechaCreacion']; ?></td>
                                <td style="vertical-align: middle;">
                                    <?php
                                    $estaHabilitado = $formulario['estaHabilitado'];
                                    $fechaApertura = $formulario['fechaInicio'];
                                    $fechaCierre = $formulario['fechaFin'];
                                    
                                    if ($estaHabilitado == 0) {
                                    ?>
                                    <span class="estado-deshabilitado">DESHABILITADO</span>
                                    <?php } else if (($fechaApertura != "" && $fechaApertura > date("Y-m-d")) || ($fechaCierre != "" && $fechaCierre < date("Y-m-d"))) { ?>
                                    <span class="estado-habilitado">HABILITADO</span><span class="estado-oculto" title="Esto significa que, si bien este formulario está habilitado, su fecha de apertura/cierre impide el acceso al mismo.">OCULTO</span>
                                    <?php } else { ?>
                                    <span class="estado-habilitado">HABILITADO</span>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <a href="formulario.ver.php?id=<?= $formulario['idFormulario']; ?>" title="Visitar este formulario."><button class="btn btn-light" style="margin-bottom: 2px;" type="button"><span class="oi oi-eye"></span></button></a>
                                    <a href="formulario.ver.detalles.php?id=<?= $formulario['idFormulario']; ?>" title="Ver más detalles acerca de este formulario."><button class="btn btn-outline-info" style="margin-bottom: 2px;" type="button"><span class="oi oi-zoom-in"></span></button></a>
                                    
                                    <?php if ($estaHabilitado == 0) { ?>
                                    <a href="formulario.modificar.estado.php?id=<?= $formulario['idFormulario']; ?>&estado=1" title="Habilitar este formulario."><button class="btn btn-outline-success" style="margin-bottom: 2px;" type="button"><span class="oi oi-check"></span></button></a>
                                    <?php } else { ?>
                                    <a href="formulario.modificar.estado.php?id=<?= $formulario['idFormulario']; ?>&estado=0" title="Deshabilitar este formulario."><button class="btn btn-outline-dark" style="margin-bottom: 2px;" type="button"><span class="oi oi-x"></span></button></a>
                                    <?php } ?>
                                    
                                    <a href="formulario.modificar.php?id=<?= $formulario['idFormulario']; ?>" title="Modificar este formulario."><button class="btn btn-outline-warning" style="margin-bottom: 2px;" type="button"><span class="oi oi-pencil"></span></button></a>
                                    
                                    <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ELIMINAR_FORMULARIOS)) { ?>
                                    <a title="Eliminar este formulario." href="formulario.eliminar.php?id=<?= $formulario['idFormulario']; ?>"><button class="btn btn-outline-danger" style="margin-bottom: 2px;" type="button"><span class="oi oi-trash"></span></button></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="formulario.crear.php">
                        <button type="button" class="btn btn-success">
                            <span class="oi oi-plus"></span> Nuevo formulario
                        </button>
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
    </script>

</html>


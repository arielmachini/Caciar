<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
include_once '../lib/BDCatalogoTablas.Class.php';
require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

if (isset($_SESSION['usuario']->id)) {
    $formularios = BDConexion::getInstancia()->query("" .
            "SELECT `idFormulario`, `titulo`, `fechaApertura`, `fechaCierre` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `estaHabilitado` = 1 " .
            "ORDER BY `titulo` ASC");
} else {
    $formularios = BDConexion::getInstancia()->query("" .
            "SELECT `idFormulario`, `titulo`, `fechaApertura`, `fechaCierre` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
            "WHERE `estaHabilitado` = 1 AND `idRol` = " . PermisosSistema::IDROL_PUBLICO_GENERAL . " " .
            "ORDER BY `titulo` ASC");
}
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

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Formularios</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container" style="min-width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h3>Formularios</h3>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="oi oi-magnifying-glass"></span></div>
                        </div>

                        <input class="form-control" id="filtrarFormularios" placeholder="Puede filtrar formularios por su título">
                    </div>
                    <br/>

                    <div id="listaFormularios">
                        <?php while ($formulario = $formularios->fetch_assoc()) { ?>
                            <?php
                            /*
                             * NOTA: Sí, todas estas variables no son necesarias,
                             * pero están puestas para mejorar la legibilidad
                             * del código. De otro modo, se tendría una única
                             * línea con un condicional IF larguísimo.
                             */
                            $fechaApertura = $formulario['fechaApertura'];
                            $fechaCierre = $formulario['fechaCierre'];
                            $estaOculto = (($fechaApertura != "" && date("Y-m-d") < $fechaApertura) || ($fechaCierre != "" && date("Y-m-d") > $fechaCierre));
                            $idFormulario = $formulario['idFormulario'];

                            if (isset($_SESSION['usuario']->id)) {
                                $rolesDestino = BDConexion::getInstancia()->query("" .
                                        "SELECT `idRol` " .
                                        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
                                        "WHERE `idFormulario` = {$idFormulario}");

                                $idRol;
                                $tienePermiso = false;
                                $usuario = new Usuario($_SESSION['usuario']->id);

                                while ($idRol = $rolesDestino->fetch_assoc()['idRol']) {
                                    if ($usuario->buscarRolPorId($idRol)) {
                                        $tienePermiso = true;

                                        break;
                                    }
                                }
                                
                                $condicion = !$estaOculto && $tienePermiso;
                            } else {
                                $condicion = !$estaOculto;
                            }

                            if ($condicion) {
                            ?>
                                <div class="formulario-recuadro">
                                    <div class="formulario-titulo" title="<?= $formulario['titulo']; ?>">
                                        <span class="oi oi-document" style="padding-right: 5px;"></span><?= $formulario['titulo']; ?>
                                    </div>

                                    <a class="btn btn-sm btn-dark" href="formulario.ver.php?id=<?= $idFormulario; ?>">
                                        <span class="oi oi-eye" style="padding-right: 5px;"></span>Ver formulario
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

    <script type="text/javascript">
        $("#filtrarFormularios").keyup(function () {
            var palabraClave, listaFormularios;
            palabraClave = this.value.toUpperCase();
            listaFormularios = $("#listaFormularios").find("div[class=formulario-recuadro]");

            for (var i = 0; i < listaFormularios.length; i++) {
                var tituloFormulario = listaFormularios[i].getElementsByTagName("div")[0].textContent;

                if (tituloFormulario.toUpperCase().indexOf(palabraClave) > -1) {
                    listaFormularios[i].style.display = "";
                } else {
                    listaFormularios[i].style.display = "none";
                }
            }
        });
    </script>

</html>

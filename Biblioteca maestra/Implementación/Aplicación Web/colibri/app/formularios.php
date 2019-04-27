<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
include_once '../lib/BDCatalogoTablas.Class.php';
require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

$formularios = BDConexion::getInstancia()->query("" .
        "SELECT `idFormulario`, `titulo`, `fechaApertura`, `fechaCierre` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `estaHabilitado` = 1");
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
                            /**
                             * NOTA: Sí, todas estas variables no son necesarias,
                             * pero están puestas para mejorar la legibilidad
                             * del código. De otro modo, se tendría una única
                             * línea con un condicional IF larguísimo.
                             */
                            
                            $fechaApertura = $formulario['fechaApertura'];
                            $fechaCierre = $formulario['fechaCierre'];
                            $estaOculto = (($fechaApertura != "" && date("Y-m-d") < $fechaApertura) || ($fechaCierre != "" && date("Y-m-d") > $fechaCierre));
                            
                            if (!$estaOculto) {
                            ?>
                                <div class="formulario-recuadro">
                                    <div class="formulario-titulo" title="<?= $formulario['titulo']; ?>">
                                        <span class="oi oi-document" style="padding-right: 5px;"></span><?= $formulario['titulo']; ?>
                                    </div>

                                    <a href="formulario.ver.php?id=<?= $formulario['idFormulario']; ?>">
                                        <button class="btn btn-sm btn-dark">
                                            <span class="oi oi-eye" style="padding-right: 5px;"></span>Ver formulario
                                        </button>
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

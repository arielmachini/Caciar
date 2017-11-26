<!DOCTYPE html>
<?php
include_once 'lib/ControlAcceso.class.php';
error_reporting(E_ALL);
//ini_set("display_errors", 1);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= Constantes::NOMBRE_SISTEMA ?> ~ Formularios disponibles</title>
        <link href="gui/estilo.css" type="text/css" rel="stylesheet"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" type="text/css" rel="stylesheet"/>
        <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>

        <script>
            $(document).ready(function () {
                $('#formularios').DataTable({
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ formularios por página",
                        "zeroRecords": "No hay resultados",
                        "info": "Página _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay formularios disponibles",
                        "infoFiltered": "(filtrado de _MAX_ formularios totales)",
                        "sFirst": "Primera",
                        "sLast": "Última",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior",
                        "sSearch": "Buscar:"
                    }
                });
            });
        </script>
    </head>
    <body>
        <?php include_once 'gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h4>Formularios disponibles</h4>
                    <p>Estos son los formularios que están disponibles para su rellenado. Seleccione uno para comenzar.</p>

                    <?php
                    $formulariosEncontrados = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO");

                    if ($formulariosEncontrados === false || $formulariosEncontrados->num_rows === 0) {
                        ?>
                        <br/><div style="text-align: center; width: 100%"><span style="font-size: 14px; width: 70%">Todavía no se cargó ningún formulario en el sistema Colibrí.</span></div>
                        <?php
                    } else {
                        ?>
                        <table id="formularios">
                            <thead>
                                <tr>
                                    <th>Nombre del formulario</th>
                                    <th>Abierto hasta</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($formulario = $formulariosEncontrados->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $formulario['titulo']; ?></td>
                                        <td>
                                            <?php
                                            $fechaFin = $formulario['fechaFin'];
                                            if (!isset($fechaFin) || $fechaFin === "") {
                                                ?>
                                                Indefinido
                                                <?php
                                            } else {
                                                ?>
                                                <?= $fechaFin ?>
                                                <?php
                                            }

                                            $id = $formulario['idFormulario'];
                                            ?>
                                        </td>
                                        <td>
                                            <a href="colibri_creador/ver_formulario.php?id=<?= $id ?>" title="Haga clic aquí para ver este formulario"><img src="imagenes/seleccionar.png"></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php } ?>

                    <br><strong>Buscar formularios</strong><br>
                    <form action="?" autocomplete="on" method="get">
                        <input type="text" name="titulo" placeholder="Ingrese el título del formulario que desea encontrar..." style="vertical-align:top; width:60%" value="<?= $busqueda ?>"/><button type="submit" style='height: 29px; width: 8%'><img src="imagenes/buscar.png"/></button>
                    </form>
                </div>
            </article>
        </section>
        <?php include_once 'gui/GUIfooter.php'; ?>
    </body>
</html>

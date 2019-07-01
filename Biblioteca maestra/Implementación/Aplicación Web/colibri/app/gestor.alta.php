<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

require_once '../modelo/ColeccionUsuarios.php';

$ColeccionUsuarios = new ColeccionUsuarios();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        
        <!-- Scripts requeridos por el sistema Colibrí -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Alta de gestor de formularios</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Alta de gestor de formularios</h3>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="oi oi-magnifying-glass"></span></div>
                        </div>

                        <input class="form-control" id="filtrarUsuarios" placeholder="Puede filtrar usuarios por su nombre o dirección de e-mail">
                    </div>
                    <br/>

                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="table-info">
                                <th scope="col">Nombre</th>
                                <th scope="col">Dirección de e-mail</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>

                        <tbody id="listaUsuarios">
                            <?php
                            foreach ($ColeccionUsuarios->getUsuarios() as $Usuario) {
                                if (!$Usuario->esGestorDeFormularios()) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $Usuario->getNombre(); ?>
                                        </td>
                                        <td>
                                            <?= $Usuario->getEmail(); ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a class="btn btn-outline-success" href="gestor.alta.parametros.php?id=<?= $Usuario->getId(); ?>">
                                                <span class="oi oi-external-link"></span> Dar de alta
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
                    <a href="gestores.php">
                        <button type="button" class="btn btn-primary">
                            <span class="oi oi-account-logout"></span> Volver
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

    <script type="text/javascript">
        $("#filtrarUsuarios").keyup(function () {
            var palabraClave, listaUsuarios;
            palabraClave = this.value.toUpperCase();
            listaUsuarios = $("#listaUsuarios").find("tr");

            for (var i = 0; i < listaUsuarios.length; i++) {
                var nombreUsuario = listaUsuarios[i].getElementsByTagName("td")[0];
                var emailUsuario = listaUsuarios[i].getElementsByTagName("td")[1];

                if (nombreUsuario || emailUsuario) {
                    var textoNombreUsuario = nombreUsuario.textContent || nombreUsuario.innerText;
                    var textoEmailUsuario = emailUsuario.textContent || emailUsuario.innerText;

                    if (textoNombreUsuario.toUpperCase().indexOf(palabraClave) > -1 || textoEmailUsuario.toUpperCase().indexOf(palabraClave) > -1) {
                        listaUsuarios[i].style.display = "";
                    } else {
                        listaUsuarios[i].style.display = "none";
                    }
                }
            }
        });
        
//        function altaGestorFormularios(idUsuario, nombreUsuario, emailUsuario) {
//            $.confirm({
//                icon: 'oi oi-signpost',
//                title: 'Dar de alta',
//                content: '¿Desea dar de alta como gestor de formularios a <b>' + nombreUsuario + '</b> (' + emailUsuario + ')?',
//                animation: 'none',
//                closeAnimation: 'none',
//                theme: 'material',
//                type: 'green',
//                useBootstrap: false,
//                buttons: {
//                    confirm: {
//                        btnClass: 'btn-green',
//                        text: 'Dar de alta',
//                        action: function () {
//                            window.location.href = 'gestor.alta.procesar.php?id=' + idUsuario;
//                        }
//                    },
//                    cancelar: {
//                        text: 'Cancelar'
//                    }
//                }
//            });
//        }
    </script>

</html>


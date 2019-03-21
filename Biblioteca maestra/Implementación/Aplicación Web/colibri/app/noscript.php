<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);
?>

<html>    
    <head>
        <script type="text/javascript">
            /**
             * Si el usuario habilita JavaScript, se lo redirecciona al creador
             * de formularios.
             */
            window.location.replace('formulario.crear.php');
        </script>

        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Crear formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Crear formulario</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <span class="oi oi-warning"></span> Debe habilitar JavaScript para poder utilizar el creador de formularios. Para aprender cómo, <a class="alert-link" href="https://www.enable-javascript.com/es" target="_blank">haga clic aquí</a>.
                    </div>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
?>

<html>    
    <head>
        <script type="text/javascript">
            /*
             * Si el usuario habilita JavaScript, se lo redirecciona a la página
             * de la cual vino.
             */
            window.history.back();
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
                    <h3>JavaScript deshabilitado</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <span class="oi oi-warning"></span> Debe habilitar JavaScript para poder visualizar la página a la que intenta acceder. Para aprender cómo, <a class="alert-link" href="https://www.enable-javascript.com/es" target="_blank">haga clic aquí</a>.
                    </div>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>


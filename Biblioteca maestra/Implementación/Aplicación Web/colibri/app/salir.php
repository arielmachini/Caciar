<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';

session_destroy();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>        
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Salir</title>
    </head>
    <body>

        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Salir</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        Acaba de cerrar sesión en el sistema <?php echo Constantes::NOMBRE_SISTEMA; ?>.
                    </div>
                    <p><strong>¿Qué desea hacer a continuación?</strong></p>
                    <a class="btn btn-outline-primary" href="index.php">
                        <span class="oi oi-account-logout"></span> Volver a la página de inicio de <?php echo Constantes::NOMBRE_SISTEMA; ?>
                    </a>
                    <a class="btn btn-outline-secondary" href="https://www.gmail.com/">
                        <span class="oi oi-inbox"></span> Ir a Gmail
                    </a>
                    <a class="btn btn-outline-secondary" href="http://www.uarg.unpa.edu.ar" target="_blank">
                        <span class="oi oi-globe"></span> Ir al portal UARG
                    </a>
                </div>
            </div>
        </div>
        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>


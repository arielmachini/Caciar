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
                    <p><b>¿Qué desea hacer a continuación?</b></p>
                    <a href="index.php">
                        <button type="button" class="btn btn-outline-primary">
                            <span class="oi oi-account-logout"></span> Volver a la página de inicio de <?php echo Constantes::NOMBRE_SISTEMA; ?>
                        </button></a>
                    <a href="https://www.gmail.com/">
                        <button type="button" class="btn btn-outline-secondary">
                            <span class="oi oi-inbox"></span> Ir a Gmail
                        </button></a>
                    <a href="http://www.uarg.unpa.edu.ar" target="_blank">
                        <button type="button" class="btn btn-outline-secondary">
                            <span class="oi oi-globe"></span> Ir al portal UARG
                        </button></a>
                </div>
            </div>
        </div>
        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>


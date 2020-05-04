<!DOCTYPE html>

<?php include_once '../lib/ControlAcceso.Class.php'; ?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/uargflow_footer.css" />
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>        
        <meta name="google-signin-client_id" content="356408280239-7airslbg59lt2nped9l4dtqm2rf25aii.apps.googleusercontent.com" />
        <script type="text/javascript" src="https://apis.google.com/js/platform.js" async defer></script>
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/login.js"></script>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
    </head>
    <body>

        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <section id="main-content">
                <article>
                    <div class="card">
                        <div class="card-header">
                            <h3>Bienvenido</h3>
                        </div>
                        <div class="card-body">
                            <h5>Iniciar sesión</h5>
                            <p>Puede iniciar sesión en el sistema Colibrí con su dirección de correo institucional (terminada en @uarg.unpa.edu.ar).</p>
                            <div id="okgoogle" class="g-signin2" data-onsuccess="onSignIn"></div>

                            <hr/>

                            <h5>Acceso para invitados</h5>
                            <p>Si no dispone de una dirección de correo institucional, podrá acceder al sistema como invitado.</p>
                            <a class="btn btn-outline-success" href="formularios.php" title="Ver formularios disponibles para invitados">
                                <span class="oi oi-document"></span> Ver formularios
                            </a>
                        </div>
                    </div>
                </article>
            </section>
        </div>
        
        <?php include_once '../gui/footer.php'; ?>
    </body>
</html>


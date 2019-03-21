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

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom-color: #f7ce3e;">
            <div class="container navbar-dark bg-dark">
                <a class="navbar-brand" href="#">
                    <img src="../lib/img/logo_colibri.png" class="d-inline-block align-top" alt="Colibrí">
                </a>
            </div>
        </nav>

        <div class="container">
            <section id="main-content">
                <article>
                    <div class="card">
                        <div class="card-header">
                            <h3>Bienvenido al sistema <?php echo Constantes::NOMBRE_SISTEMA; ?></h3>
                        </div>
                        <div class="card-body">
                            <p>Puede <b>iniciar sesión con su dirección de correo institucional</b> o puede <a href="formularios.php"><b>ver los formularios disponibles para invitados</b></a>.</p>

                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-danger" role="alert">
                                        <div class="row vertical-align">
                                            <div class="col-1 text-center">
                                                <span class="oi oi-info"></span> 
                                            </div>
                                            <div class="col-11">
                                                <b>Importante:</b> Para acceder al sistema es necesario disponer de un correo de <a href="https://www.gmail.com/" target="_blank">Gmail</a>.
                                            </div>
                                        </div>
                                    </div>      
                                </div>
                            </div>

                            <hr/>
                            <h5>Iniciar sesión</h5>
                            <p>Podrá iniciar sesión si accede a Gmail con una dirección de correo institucional (terminada en @uarg.unpa.edu.ar). Si dispone de una, puede iniciar sesión haciendo clic en el siguiente botón:</p>
                            <div id="okgoogle" class="g-signin2" data-onsuccess="onSignIn"></div>
                        </div>
                    </div>
                </article>
            </section>
        </div>
        <footer class="footer" style="border-top-color: #f7ce3e;">
            <?php echo Constantes::NOMBRE_SISTEMA; ?>
            <span class="oi oi-globe"></span> 
            UNPA-UARG
        </footer>
    </body>
</html>


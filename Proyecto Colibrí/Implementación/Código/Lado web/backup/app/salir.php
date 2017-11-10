<?php include_once '../lib/ControlAcceso.class.php'; ?>
<?php
session_start();
session_unset();
$_SESSION['usuario'] = null;
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta name="google-signin-client_id" content="356408280239-7airslbg59lt2nped9l4dtqm2rf25aii.apps.googleusercontent.com" />
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="https://apis.google.com/js/platform.js" async defer></script>
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3><?php echo Constantes::NOMBRE_SISTEMA; ?></h3>
                    <div>
                        <h4>Ha salido del sistema.</h4>
                        <p><span style="color: red">Ud. sigue conectado a su e-mail institucional.</span></p>
                        <fieldset>
                            <legend>Opciones</legend>
                            <p>
                                <a href="../app/index.php">
                                    <input type="button" value="Ingresar al Sistema" />
                                </a>
                                <a href="http://www.gmail.com">
                                    <input type="button" value="Cerrar" />
                                </a>
                            </p>
                        </fieldset>
                    </div>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
<?php session_unset(); ?>
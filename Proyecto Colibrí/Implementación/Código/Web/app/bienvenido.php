<?php include_once '../lib/ControlAcceso.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CONSULTAR);
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta name="google-signin-client_id" content="356408280239-7airslbg59lt2nped9l4dtqm2rf25aii.apps.googleusercontent.com" />
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="https://apis.google.com/js/platform.js" async defer></script>
        <script type="text/javascript" src="../lib/jQuery/jquery.redirect.js"></script>
        <script type="text/javascript" src="../lib/login.js"></script>
    </head>
    <body>
<?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3><?php echo Constantes::NOMBRE_SISTEMA; ?> - Bienvenido</h3>
                    <div>
                        <h4>¡Bienvenido!</h4>
                        <p>Acaba de iniciar sesión en el sistema Colibrí. ¡Sea bienvenido!</p>
                        <div class="botonGoogle" onclick="window.open('../Instructivo.pdf', '_blank');" title="Ver Manual de Uso">
                            <div class="abcRioButtonIcon" style="padding:8px">
                                <div style="width:18px;height:18px;" class="abcRioButtonSvgImageWithFallback abcRioButtonIconImage abcRioButtonIconImage18">
                                    <img src="../imagenes/pdf.png" style="width: 18px; height: 18px" />
                                </div>
                            </div>
                            <span style="font-size:13px;line-height:34px;" class="abcRioButtonContents">
                                <span id="not_signed_in9kbu5ybb006p">Manual</span>
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        </section>
<?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>

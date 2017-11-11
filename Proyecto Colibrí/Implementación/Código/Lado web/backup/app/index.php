<?php include_once '../lib/ControlAcceso.class.php'; ?>

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
                    <h3><?php echo Constantes::NOMBRE_SISTEMA; ?>: Inicio</h3>
                    <div>
                        <img alt="Logotipo de Colibr&iacute;" src='../imagenes/logoColibri.png'/><br/>
                        <h4>Bienvenido</h4>
                        <p>¡Bienvenido al sistema Colibr&iacute;! A trav&eacute;s de este usted podr&aacute; acceder y rellenar distintos formularios de su inter&eacute;s.</p>
                        <p><span style="color: red"><strong>Importante:</strong> Recuerde que iniciando sesi&oacute;n en el sistema con su correo electr&oacute;nico institucional podr&aacute; acceder a un mayor n&uacute;mero de formularios.</span></p>
                        <h4>Iniciar sesi&oacute;n</h4>
                        <p>Ud. puede consultar el sistema si est&aacute; conectado a su e-mail Institucional. Por favor haga click en el bot&oacute;n a continuaci&oacute;n y elija su cuenta institucional.</p>
                        <div class="botonGoogle" onclick="window.open('../Instructivo.pdf', '_blank');" title="Leer manual de uso">
                            <div class="abcRioButtonIcon" style="padding:8px">
                                <div style="width:18px;height:18px;" class="abcRioButtonSvgImageWithFallback abcRioButtonIconImage abcRioButtonIconImage18">
                                    <img src="../imagenes/pdf.png" style="width: 18px; height: 18px" />
                                </div>
                            </div>
                            <span style="font-size:13px;line-height:34px;" class="abcRioButtonContents">
                                <span id="not_signed_in9kbu5ybb006p">Leer manual de uso</span>
                            </span>
                        </div>
                        <div id="okgoogle" class="g-signin2" data-onsuccess="onSignIn" title="Iniciar sesi&oacute;n en Colibr&iacute;"></div>
                    </div>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>

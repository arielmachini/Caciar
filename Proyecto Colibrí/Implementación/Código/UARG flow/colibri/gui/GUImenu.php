<link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
<link href="../gui/responsivo.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../gui/menu/dist/css/slimmenu.min.css">

<header id="main-header">

    <a id="logo-header" href="../app/index.php">
        <span class="site-name"><?php echo Constantes::NOMBRE_SISTEMA; ?></span>
        <span class="site-desc">SIT UNPA-UARG</span>
    </a>


    <div>
        <nav>

            <ul class="slimmenu">
                <li><a href="../app/panel.php" title="Panel de Control personal">Home</a></li>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_PARAMETROS)) { ?>
                    <li>
                        <a href="#">DropDown 1</a>
                        <ul>
                            <li><a href="#">Opcion 1</a></li>
                            <li><a href="#">Opcion 2</a></li>
                            <li><a href="#">Opcion 3</a></li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_USUARIOS)) { ?>
                    <li>
                        <a href="../workflow/workflow.usuarios.ver.php" title="Gesti&oacute;n de Usuarios" 
                        <?php echo GUI::generaMenuActive("workflow/workflow.usuarios.ver.php"); ?>
                        <?php echo GUI::generaMenuActive("workflow/workflow.roles.ver.php"); ?>
                        <?php echo GUI::generaMenuActive("workflow/workflow.permisos.ver.php"); ?>
                           >Usuarios</a>
                        <ul>
                            <li><a href="../workflow/workflow.usuarios.ver.php" title="Gesti&oacute;n de Usuarios" <?php echo GUI::generaMenuActive("workflow/workflow.usuarios.ver.php"); ?>>Usuarios</a></li>
                            <li><a href="../workflow/workflow.roles.ver.php" title="Tipos de Usuario" <?php echo GUI::generaMenuActive("workflow/workflow.roles.ver.php"); ?>>Roles</a></li>
                            <li><a href="../workflow/workflow.permisos.ver.php" title="Permisos del sistema" <?php echo GUI::generaMenuActive("workflow/workflow.permisos.ver.php"); ?>>Permisos</a></li>
                        </ul>
                    </li>
                <?php } ?>

            </ul>

            <?php if (isset($_SESSION['usuario'])) { ?>
                <table>
                    <tr>
                        <td>
                            Usuario: <?php echo $_SESSION['usuario']->nombre; ?><br />
                            (<?php echo $_SESSION['usuario']->email; ?>) :: 
                            <a href="../app/salir.php">Salir</a>
                        </td>
                        <td><img src="<?php echo $_SESSION['usuario']->imagen; ?>" style="height:30px; padding: 0; margin: 0" /></td>
                    </tr>
                </table>
            <?php } ?>

        </nav>
    </div>

    <script src="../gui/menu/src/js/jquery.slimmenu.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script>
        $('.slimmenu').slimmenu(
                {
                    resizeWidth: '800',
                    collapserTitle: 'Main Menu',
                    animSpeed: 'medium',
                    indentChildren: true,
                    childrenIndenter: '&raquo;'
                });
    </script>

</header>

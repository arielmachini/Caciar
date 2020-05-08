<link href="https://fonts.googleapis.com/css?family=Merienda&display=swap" rel="stylesheet">

<!-- Los estilos de navbar son definidos en la libreria css de Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom-color: #449c73; padding: 20px 5%;">
    <a class="navbar-brand" href="formularios.php" style="display: flex; align-items: center;">
        <img class="d-inline-block align-top" height="50px" src="../lib/img/colibri.svg" style="margin-right: 10px;">
        <span class="navbar-brand" style="font-family: 'Merienda', sans-serif; font-size: 1.6rem !important;">Colibrí</span>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="toggle navigation">
        <span class="navbar-toggler-icon"></span>   
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php $estaIdentificado = isset($_SESSION['usuario']); ?>

            <?php if (!$estaIdentificado) { ?>
                <li class="nav-item" style="margin: 5px;">
                    <a class="nav-link" href="../app/index.php" style="padding: 10px;">
                        <span class="oi oi-account-login" />
                        Iniciar sesión
                    </a>
                </li>
            <?php } ?>

            <li class="nav-item" style="margin: 5px;">
                <a class="nav-link" href="../app/formularios.php" style="padding: 10px;">
                    <span class="oi oi-document" />
                    Formularios
                </a>
            </li>

            <?php if ($estaIdentificado) { ?>
                <?php $tieneAccesoAdministrativo = ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_PERMISOS) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ROLES) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_USUARIOS) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);
                if ($tieneAccesoAdministrativo) { ?>
                    <li class="nav-item dropright" style="margin: 5px;">
                        <a class="nav-link dropdown-toggle" href="#" id="menuDesplegableAdministracion" style="padding: 10px;" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" role="button">
                            <span class="oi oi-wrench" />
                            Administración
                        </a>
                        <div class="dropdown-menu" aria-labelledby="menuDesplegableAdministracion">
                            <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_USUARIOS)) { ?>
                                <a class="dropdown-item" href="../app/usuarios.php">
                                    <span class="oi oi-person" />
                                    Usuarios
                                </a>
                            <?php } ?>

                            <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ROLES)) { ?>
                                <a class = "dropdown-item" href = "../app/roles.php">
                                    <span class = "oi oi-graph" />
                                    Roles
                                </a>
                            <?php } ?>

                            <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_PERMISOS)) { ?>
                                <a class="dropdown-item" href="../app/permisos.php">
                                    <span class="oi oi-lock-locked" />
                                    Permisos
                                </a>
                            <?php } ?>

                            <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES)) { ?>
                                <h6 class="dropdown-header">Sistema Colibrí</h6>
                                <a class="dropdown-item" href="../app/formulario.gestor.pendientes.php">
                                    <span class="oi oi-task" />
                                    Gestionar formularios pendientes
                                </a>
                                
                                <a class="dropdown-item" href="../app/gestores.php">
                                    <span class="oi oi-people" />
                                    Gestores de formularios
                                </a>
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES)) { ?>
                    <li class="nav-item dropright" style="margin: 5px;">
                        <a class="nav-link dropdown-toggle" href="#" id="menuDesplegableGestionFormularios" style="padding: 10px;" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" role="button">
                            <span class="oi oi-dashboard" />
                            Gestión de formularios
                        </a>

                        <div class="dropdown-menu" aria-labelledby="menuDesplegableGestionFormularios">
                            <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS)) { ?>
                                <a class="dropdown-item" href="../app/formulario.crear.php">
                                    <span class="oi oi-plus" />
                                    Nuevo formulario
                                </a>
                            <?php } ?>

                            <a class="dropdown-item" href="../app/formulario.gestor.php">
                                <span class="oi oi-spreadsheet" />
                                Gestor de formularios
                            </a>
                        </div>
                    </li>
                <?php } ?>

                <li class="nav-item" style="margin: 5px;">
                    <a class="nav-link" href="../app/salir.php" style="padding: 10px;">
                        <span class="oi oi-account-logout" />
                        Salir
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>
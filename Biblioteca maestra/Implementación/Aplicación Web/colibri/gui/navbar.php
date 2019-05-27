<!-- Los estilos de navbar son definidos en la libreria css de Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom-color: #449c73; padding: 20px 5%;">
    <a class="navbar-brand" href="formularios.php" style="display: flex; align-items: center;">
        <img class="d-inline-block align-top" height="50px" src="../lib/img/colibri.svg" style="margin-right: 10px;">
        <span class="navbar-brand">Colibrí</span>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="toggle navigation">
        <span class="navbar-toggler-icon"></span>   
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            
            <?php $estaIdentificado = isset($_SESSION['usuario']); ?>

            <?php if (!$estaIdentificado) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="../app/index.php">
                        <span class="oi oi-account-login" />
                        Iniciar sesión
                    </a>
                </li>
            <?php } ?>

            <li class="nav-item">
                <a class="nav-link" href="../app/formularios.php">
                    <span class="oi oi-document" />
                    Formularios
                </a>
            </li>

            <?php if ($estaIdentificado) { ?>
                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_USUARIOS)) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../app/usuarios.php">
                            <span class="oi oi-person" />
                            Usuarios
                        </a>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ROLES)) { ?>
                    <li class = "nav-item">
                        <a class = "nav-link" href = "../app/roles.php">
                            <span class = "oi oi-graph" />
                            Roles
                        </a>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_PERMISOS)) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../app/permisos.php">
                            <span class="oi oi-lock-locked" />
                            Permisos
                        </a>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS)) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../app/formulario.crear.php">
                            <span class="oi oi-plus" />
                            Crear formulario
                        </a>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS)) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../app/formulario.gestor.php">
                            <span class="oi oi-dashboard" />
                            Gestor de formularios
                        </a>
                    </li>
                <?php } ?>

                <?php if (ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES)) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../app/gestores.php">
                            <span class="oi oi-people" />
                            Administrar gestores de formularios
                        </a>
                    </li>
                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link" href="../app/salir.php">
                        <span class="oi oi-account-logout" />
                        Salir
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>

<?php if ($estaIdentificado) { ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="margin: 5px;">
        <span class="oi oi-info" style="padding-right: 10px;"></span> Usted está identificado como <strong><?= $_SESSION['usuario']->nombre; ?></strong>.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>
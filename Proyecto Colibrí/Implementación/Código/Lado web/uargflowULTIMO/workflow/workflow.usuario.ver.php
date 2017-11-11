<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$UsuarioWorkflow = new WorkflowUsuario($_GET['id']);
$RolesWorkflow = new WorkflowRoles();
?>

<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Propiedades del Usuario "<?= $UsuarioWorkflow->getNombre(); ?>"</h3>
                    <form method="post" action="workflow.usuario.procesar.php" name="formulario" >
                        <input type="hidden" name="idUsuario" value="<?= $_GET['id']; ?>" />
                        <script type="text/javascript" language="javascript">var validador = new Validator("formulario");</script>
                        <p>Complete los datos a continuaci&oacute;n, luego presione el bot&oacute;n <b>Guardar</b>.Los campos marcados con (*) son obligatorios.</p>
                        <fieldset>
                            <legend>Propiedades</legend>      
                            <p>Nombre de usuario (*)<br>
                                <input type="text" name="nombre" id="nombre" title="Nombre del usuario" value="<?= $UsuarioWorkflow->getNombre(); ?>" />
                                <script>validador.addValidation("nombre", "obligatorio");</script>
                                <script>validador.addValidation("nombre", "solotexto");</script>
                            </p>

                            <p>Correo electr&oacute;nico (*)<br>
                                <input type="text" name="email" id="email" title="Correo electronico" value="<?= $UsuarioWorkflow->getEmail(); ?>" />
                                <script>validador.addValidation("email", "email");</script>
                                <script>validador.addValidation("email", "obligatorio");</script>
                            <p/>

                            <p>Estado<br />
                                <input type="radio" name="estado" value="Activo" <?php if ($UsuarioWorkflow->getEstado() == "Activo") echo "checked"; ?> />Activo
                                <input type="radio" name="estado" value="Inactivo" <?php if ($UsuarioWorkflow->getEstado() == "Inactivo") echo "checked"; ?>/>Inactivo
                            </p>

                            <p>Sector (*)<br />

                                <select name="idSec" title="Sector">
                                    <option value="0">Seleccione</option>
                                    <?php
                                    $datos = ObjetoDatos::getInstancia()->ejecutarQuery(""
                                            . "SELECT idSector, nombre "
                                            . "FROM sectoruniversitario "
                                            . "ORDER BY nombre");
                                    for ($x = 0; $x < $datos->num_rows; $x++) {
                                        $sector = $datos->fetch_assoc();
                                        ?>
                                        <option value="<?= $sector['idSector']; ?>" <?php if ($UsuarioWorkflow->getIdSec() == $sector['idSector']) echo "selected"; ?>>
                                            <?= $sector['nombre']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <script>validador.addValidation("idSec", "selectOptions=0");</script>
                            </p>
                        </fieldset>

                        <fieldset>
                            <legend>Roles</legend>
                            <p>

                                <?php foreach($RolesWorkflow->getRoles() as $WorkflowRol) { ?>
                                    <input
                                        <?php echo $UsuarioWorkflow->poseeRol($WorkflowRol->getIdRol()) ? 'checked ' : ''; ?>
                                        type="radio" name="idrol" value="<?= $WorkflowRol->getIdRol(); ?>" />
                                        <?= $WorkflowRol->getNombre(); ?> 
                                    <br />
                                <?php } ?>
                            </p>
                        </fieldset>

                        <fieldset>
                            <legend>Opciones</legend>
                            <input type="submit" value="Guardar Cambios" />
                            <input type="reset" value="Descartar Cambios" />
                            <a href="workflow.usuarios.ver.php">
                                <input type="button" value="Salir" />
                            </a>
                        </fieldset>
                        <p>&nbsp;</p>

                    </form>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
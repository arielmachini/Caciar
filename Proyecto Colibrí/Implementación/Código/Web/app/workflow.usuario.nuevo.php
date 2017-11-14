<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$UsuarioWorkflow = new WorkflowUsuario();
$RolesWorkflow = new WorkflowRoles();
?>
<html>
    <head>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <script src="../lib/validador.js" type="text/javascript"></script>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Alta de Usuario</h3>
                    <p>Por favor complete los datos a continuaci&oacute;n. Los campos marcados con (*) son obligatorios.</p>
                    <form method="post" action="workflow.usuario.nuevo.procesa.php" name="formulario" >
                        <script type="text/javascript" language="javascript">var validador = new Validator("formulario");</script>
                        <fieldset>
                            <legend>Propiedades</legend>      
                            <p>Nombre de usuario (*)<br>
                                <input type="text" name="nombre" id="nombre" title="Nombre del usuario" />
                                <script>validador.addValidation("nombre", "obligatorio");</script>
                                <script>validador.addValidation("nombre", "solotexto");</script>
                            </p>

                            <p>Correo electr&oacute;nico (*)<br>
                                <input type="text" name="email" id="email" title="Correo electronico" />
                                <script>validador.addValidation("email", "obligatorio");</script>
                                <script>validador.addValidation("email", "email");</script>
                            <p/>

                            <p>Estado<br />
                                <input type="radio" name="estado" value="Activo" checked />Activo
                                <input type="radio" name="estado" value="Inactivo" />Inactivo
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
                                        $sector = $datos->fetch_assoc(); ?>
                                        <option value="<?= $sector['idSector']; ?>"><?= $sector['nombre']; ?></option>
                                    <?php } ?>
                                </select>
                                <script>validador.addValidation("idSec", "selectOptions=0");</script>
                            </p>
                        </fieldset>

                        <fieldset>
                            <legend>Roles</legend>
                            <p>
                                <?php
                                foreach ($RolesWorkflow->getRoles() as $WorkflowRol) {
                                    ?>
                                    <input type="radio" name="idrol" value="<?= $WorkflowRol->getIdRol(); ?>"  />
                                    <?php echo $WorkflowRol->getNombre(); ?> 
                                    <br />
                                <?php } ?>
                            </p>
                        </fieldset>

                        <fieldset>
                            <legend>Opciones</legend>
                            <input type="submit" value="Guardar" />
                            <input type="reset" value="Limpiar Campos" />
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
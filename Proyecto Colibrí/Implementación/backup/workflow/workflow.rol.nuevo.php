<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$RolWorkflow = new WorkflowRol();
$PermisosWorkflow = new WorkflowPermisos();
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
                    <h3>Alta de Rol</h3>
                    <p>Por favor complete los datos a continuaci&oacute;n. Los campos marcados con (*) son obligatorios.</p>
                    <form method="post" action="workflow.rol.nuevo.procesa.php" name="formulario" >
                        <script type="text/javascript" language="javascript">var validador = new Validator("formulario");</script>
                        
                        <fieldset>
                            <legend>Propiedades</legend>      
                            <p> Nombre (*)<br>
                                <input type="text" name="nombre" id="nombre" title="Nombre del Rol" />
                                <script>validador.addValidation("nombre", "obligatorio");</script>
                                <script>validador.addValidation("nombre", "solotexto");</script>
                            </p>
                        </fieldset>
                        
                        <fieldset>
                            <legend>Permisos</legend>
                            <?php
                            foreach ($PermisosWorkflow->getPermisos() as $WorkflowPermiso) {
                                ?>
                                <input type="checkbox" name="permiso_<?= $WorkflowPermiso->getIdPermiso(); ?>"  />
                                <?php echo $WorkflowPermiso->getNombre(); ?> 
                                <br />
                            <?php } ?>
                        </fieldset>
                        
                        <fieldset>
                            <legend>Opciones</legend>
                            <input type="submit" value="Guardar" />
                            <input type="reset" value="Limpiar Campos" />
                            <a href="workflow.roles.ver.php">
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
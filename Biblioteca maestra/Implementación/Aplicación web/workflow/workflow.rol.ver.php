<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
$RolWorkflow = new WorkflowRol($_GET['id']);
$PermisosWorkflow = new WorkflowPermisos();
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
                    <h3>Propiedades del Rol "<?= $RolWorkflow->getNombre(); ?>"</h3>
                    <form method="post" name="formulario" enctype="multipart/form-data" action="workflow.rol.procesar.php">
                        
                        <script type="text/javascript" language="javascript">var validador = new Validator("formulario");</script>
                        <input type="hidden" name="idRol" value="<?php echo $RolWorkflow->getIdRol(); ?>" />
                        <p>Complete los datos a continuaci&oacute;n. Los campos marcados con (*) son obligatorios.</p>
                        
                        <fieldset>
                            <legend>Propiedades</legend>                              
                            <p>Nombre (*)<br /><input type="text" name="nombreRol" value="<?= $RolWorkflow->getNombre(); ?>"></p>
                            <script>validador.addValidation("nombreRol", "obligatorio");</script>
                            <script>validador.addValidation("nombreRol", "solotexto");</script>
                        </fieldset>
                        
                        <fieldset>
                            <legend>Permisos</legend>
                            <?php foreach ($PermisosWorkflow->getPermisos() as $WorkflowPermiso) { ?>
                                <input
                                <?php echo $RolWorkflow->poseePermiso($WorkflowPermiso->getIdPermiso()) ? 'checked' : ''; ?>
                                    type="checkbox" name="permiso_<?= $WorkflowPermiso->getIdPermiso(); ?>" />
                                    <?= $WorkflowPermiso->getNombre(); ?>
                                <br />
                            <?php } ?>
                        </fieldset>
                        
                        <fieldset>
                            <legend>Opciones</legend>
                            <input type="submit" value="Guardar Cambios" />
                            <input type="reset" value="Descartar Cambios" />
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
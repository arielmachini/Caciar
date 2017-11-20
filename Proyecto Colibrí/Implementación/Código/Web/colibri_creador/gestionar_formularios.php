<?php
    include_once '../lib/Constantes.class.php';
    require_once '../lib/ControlAcceso.class.php';
    
    ControlAcceso::requierePermiso(PermisosSistema::PERMISO_USUARIOS);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= Constantes::NOMBRE_SISTEMA ?> ~ Gestionar mis formularios</title>
        
        <script src="../colibri/colibri.js" type="text/javascript"></script>
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
    </body>
</html>

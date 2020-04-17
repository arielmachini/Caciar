<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

include_once '../modelo/Usuario.Class.php';

/* ID del usuario que será dado de alta como gestor de formularios. */
$idUsuario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

$Usuario = new Usuario($idUsuario);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />
        
        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />
        
        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Alta de gestor de formularios</title>

    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>
        <div class="container">
            <form action="gestor.alta.procesar.php" method="post">
                <div class="card">
                    <div class="card-header">
                        <h3>Alta de gestor de formularios</h3>
                    </div>
                    <div class="card-body">
                        <p>Por favor, especifique la cantidad total de formularios que <strong><?= $Usuario->getNombre(); ?></strong> (<?= $Usuario->getEmail(); ?>) podrá crear y si tendrá el permiso para publicarlos sin requerir de un administrador.</p>
                        
                        <div class="form-group">
                            <p class="campo-cabecera" for="cuotaCreacion">Cuota de creación</p>
                            <p class="campo-descripcion">Defina cuántos formularios podrá crear <strong><?= $Usuario->getNombre(); ?></strong>.</p>
                            <input autocomplete="off" autofocus class="form-control" id="cuotaCreacion" min="0" max="100" name="cuotaCreacion" style="margin-bottom: 7.5px;" type="number" value="1">
                            
                            <label for="cuotaIlimitada" title="Si marca esta casilla, <?= $Usuario->getNombre(); ?> podrá crear una cantidad ilimitada de formularios.">
                                <input class="campo-opcion" id="cuotaIlimitada" name="cuotaIlimitada" type="checkbox">
                                Ilimitada
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <p class="campo-cabecera" for="puedePublicar">Permiso para publicar</p>
                            <p class="campo-descripcion">Defina si <strong><?= $Usuario->getNombre(); ?></strong> podrá habilitar/deshabilitar sus propios formularios sin requerir de un administrador. En otras palabras, si no le concede este permiso al gestor de formularios, entonces un administrador deberá habilitar/deshabilitar aquellos formularios que cree.</p>
                            <label for="puedePublicar">
                                <input class="campo-opcion" id="puedePublicar" name="puedePublicar" type="checkbox">
                                Sí, <strong><?= $Usuario->getNombre(); ?></strong> (<?= $Usuario->getEmail(); ?>) podrá habilitar/deshabilitar sus propios formularios
                            </label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="idUsuario" class="form-control" value="<?= $idUsuario; ?>" >
                        <button type="submit" class="btn btn-outline-success">
                            <span class="oi oi-plus"></span> Confirmar (dar de alta)
                        </button>
                        <a class="btn btn-outline-danger" href="gestor.alta.php">
                            <span class="oi oi-x"></span> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>
    
    <script type="text/javascript">
        $('#cuotaIlimitada').change(function(){
            if ($('#cuotaIlimitada').is(':checked') == true){
                $('#cuotaCreacion').val('0').prop('disabled', true);
            } else {
                $('#cuotaCreacion').val('1').prop('disabled', false);
            }
        });
    </script>

</html>
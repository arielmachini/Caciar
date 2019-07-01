<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES);

include_once '../modelo/Usuario.Class.php';
require_once '../modelo/BDConexion.Class.php';

/* ID del usuario que será dado de alta como gestor de formularios. */
$idUsuario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

$Usuario = new Usuario($idUsuario);

$consulta = BDConexion::getInstancia()->query("" .
                "SELECT `cuotaCreacion`, `puedePublicar` " .
                "FROM " . BDCatalogoTablas::BD_TABLA_GESTOR_FORMULARIOS . " " .
                "WHERE `idUsuario` = {$idUsuario}")->fetch_assoc();

$parametroCuotaCreacion = $consulta['cuotaCreacion'];
$parametroPuedePublicar = $consulta['puedePublicar'];
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
        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Modificar parámetros de un gestor de formularios</title>

    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>
        <div class="container">
            <form action="gestor.modificar.procesar.php" method="post">
                <div class="card">
                    <div class="card-header">
                        <h3>Modificar parámetros de un gestor de formularios</h3>
                    </div>
                    <div class="card-body">
                        <p>Está modificando los parámetros de gestión de formularios para <strong><?= $Usuario->getNombre(); ?></strong> (<?= $Usuario->getEmail(); ?>).</p>
                        
                        <div class="form-group">
                            <p class="campo-cabecera" for="cuotaCreacion">Cuota de creación</p>
                            <p class="campo-descripcion">Defina cuántos formularios podrá crear <strong><?= $Usuario->getNombre(); ?></strong>.</p>
                            
                            <?php if ($parametroCuotaCreacion != -1) { ?>
                            
                                <input autocomplete="off" autofocus class="form-control" id="cuotaCreacion" min="0" max="100" name="cuotaCreacion" style="margin-bottom: 7.5px;" type="number" value="<?= $parametroCuotaCreacion; ?>">

                                <label for="cuotaIlimitada" title="Si marca esta casilla, <?= $Usuario->getNombre(); ?> podrá crear una cantidad ilimitada de formularios.">
                                    <input class="campo-opcion" id="cuotaIlimitada" name="cuotaIlimitada" type="checkbox">
                                    Ilimitada
                                </label>
                            
                            <?php } else { ?>
                                
                                <input autocomplete="off" autofocus class="form-control" disabled id="cuotaCreacion" min="0" max="100" name="cuotaCreacion" style="margin-bottom: 7.5px;" type="number" value="0">

                                <label for="cuotaIlimitada" title="Si marca esta casilla, <?= $Usuario->getNombre(); ?> podrá crear una cantidad ilimitada de formularios.">
                                    <input checked class="campo-opcion" id="cuotaIlimitada" name="cuotaIlimitada" type="checkbox">
                                    Ilimitada
                                </label>
                            
                            <?php } ?>
                        </div>
                        
                        <div class="form-group">
                            <p class="campo-cabecera" for="puedePublicar">Permiso para publicar</p>
                            <p class="campo-descripcion">Defina si <strong><?= $Usuario->getNombre(); ?></strong> podrá habilitar/deshabilitar sus propios formularios sin requerir de un administrador. En otras palabras, si no le concede este permiso al gestor de formularios, entonces un administrador deberá habilitar/deshabilitar aquellos formularios que cree.</p>
                            
                            <?php if ($parametroPuedePublicar == 0) { ?>
                            
                                <label for="puedePublicar">
                                    <input class="campo-opcion" id="puedePublicar" name="puedePublicar" type="checkbox">
                                    Sí, <strong><?= $Usuario->getNombre(); ?></strong> (<?= $Usuario->getEmail(); ?>) podrá habilitar/deshabilitar sus propios formularios
                                </label>
                            
                            <?php } else { ?>
                            
                                <label for="puedePublicar">
                                    <input checked class="campo-opcion" id="puedePublicar" name="puedePublicar" type="checkbox">
                                    Sí, <strong><?= $Usuario->getNombre(); ?></strong> (<?= $Usuario->getEmail(); ?>) podrá habilitar/deshabilitar sus propios formularios
                                </label>
                            
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="hidden" name="idUsuario" class="form-control" value="<?= $idUsuario; ?>" >
                        <button type="submit" class="btn btn-outline-success">
                            <span class="oi oi-check"></span> Guardar
                        </button>
                        <a class="btn btn-outline-danger" href="gestores.php">
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
<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

if (preg_match('/MSIE\s(?P<v>\d+)/i', filter_input(INPUT_SERVER, "HTTP_USER_AGENT"), $B) && $B['v'] <= 8) {
    echo "Tiene que actualizar su navegador para poder acceder a esta página. Disculpe las molestias.";

    exit();
}

include_once '../modelo/ColeccionRoles.php';
require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Formulario.Class.php';

BDConexion::getInstancia()->autocommit(true);

/* Se sanitiza la variable recibida por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

$consulta = BDConexion::getInstancia()->query("" .
        "SELECT * " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
        "WHERE `idFormulario` = " . $idFormulario)->fetch_assoc();

if (!$consulta) {
    /* No existe formulario con la ID recibida por GET. */
    ControlAcceso::redireccionar('formulario.gestor.php');
    
    exit();
}

if ($formulario['estaHabilitado'] == 1) {
    /* El formulario está habilitado, por lo tanto no se puede modificar. */
    ControlAcceso::redireccionar('formulario.gestor.php');
    
    exit();
}

$formulario = new Formulario();

$formulario->setID($idFormulario);
$formulario->setDescripcion($consulta['descripcion']);
$formulario->setEmailReceptor($consulta['emailReceptor']);
$formulario->setFechaApertura($consulta['fechaApertura']);
$formulario->setFechaCierre($consulta['fechaCierre']);
$formulario->setTitulo($consulta['titulo']);

$consulta = BDConexion::getInstancia()->query("" .
        "SELECT `idRol` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
        "WHERE `idFormulario` = {$idFormulario}");

while ($idRol = $consulta->fetch_assoc()['idRol']) {
    $formulario->agregarDestinatario($idRol);
}

/* Se guarda la ID del formulario en el arreglo SESSION para poder acceder a
 * este más adelante.
 */
$_SESSION['idFormulario'] = $formulario->getID();

$ColeccionRoles = new ColeccionRoles();
?>

<html>
    <head>
        <noscript>
            <style>
                body {
                    display: none;
                }
            </style>

            <meta http-equiv="refresh" content="0; url=noscript.php">
        </noscript>

        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

        <!-- Scripts requeridos por el sistema Colibrí -->
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Modificar formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container" style="min-width: 800px;">
            <div class="card">
                <div class="card-header">
                    <h3>Modificar formulario</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Aviso:</strong> No puede modificar el título, la descripción ni los campos de este formulario porque ya registra respuestas.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Atención:</strong> Todos los campos acompañados por un asterisco (<span style="color: red; font-weight: bold;">*</span>) son obligatorios. Si no los rellena no podrá crear el formulario.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="formulario.modificar.basico.procesar.php" id="crearFormulario" method="post" novalidate>
                        <p for="destinatarioFormulario" class="campo-cabecera">Dirección de e-mail que recibirá las respuestas<span style="color: red; font-weight: bold;">*</span></p>
                        <div>
                            <p class="campo-descripcion">¿Qué dirección de e-mail debería recibir las respuestas al formulario que está modificando?</p>
                            <input autocomplete="on" class="form-control form-control-lg" id="destinatarioFormulario" maxlength="200" name="destinatarioFormulario" required type="email" value="<?= $formulario->getEmailReceptor(); ?>"/>
                            <div class="invalid-feedback">
                                <span class="oi oi-circle-x"></span> La dirección de e-mail que ingresó no es válida.
                            </div>
                        </div>
                        <br/>

                        <div>
                            <p class="campo-cabecera" for="rolesDestinoFormulario">Destinatarios del formulario<span style="color: red; font-weight: bold;">*</span></p>
                            <p class="campo-descripcion">Seleccione uno o más roles a los que estará dirigido su formulario. Aquellos usuarios con roles que no seleccione no podrán acceder al formulario.</p>
                            <label for="rolId<?= PermisosSistema::IDROL_PUBLICO_GENERAL; ?>" title="Comprende estudiantes y a otras personas sin correo institucional.">
                                <?php if (in_array(PermisosSistema::IDROL_PUBLICO_GENERAL, $formulario->getDestinatarios())) { ?>
                                    <input checked class="campo-opcion" id="rolId<?= PermisosSistema::IDROL_PUBLICO_GENERAL; ?>" name="rolesDestinoFormulario[]" type="checkbox" value="<?= PermisosSistema::IDROL_PUBLICO_GENERAL; ?>">
                                <?php } else { ?>
                                    <input class="campo-opcion" id="rolId<?= PermisosSistema::IDROL_PUBLICO_GENERAL; ?>" name="rolesDestinoFormulario[]" type="checkbox" value="<?= PermisosSistema::IDROL_PUBLICO_GENERAL; ?>">
                                <?php } ?>
                                Público general
                            </label>
                            
                            <?php foreach ($ColeccionRoles->getRoles() as $Rol) {
                                if ($Rol->getNombre() !== PermisosSistema::ROL_GESTOR && $Rol->getNombre() !== PermisosSistema::ROL_ADMINISTRADOR_GESTORES && $Rol->getNombre() !== PermisosSistema::ROL_ADMINISTRADOR && $Rol->getId() != PermisosSistema::IDROL_PUBLICO_GENERAL) { // Los roles administrativos no son incumbencia del gestor de formularios. El rol de invitado también se omite ya que se insertó manualmente en el código. ?>
                                    <label for="rolId<?= $Rol->getId(); ?>">
                                        <?php if (in_array($Rol->getId(), $formulario->getDestinatarios())) {?>
                                            <input checked class="campo-opcion" id="rolId<?= $Rol->getId(); ?>" name="rolesDestinoFormulario[]" type="checkbox" value="<?= $Rol->getId(); ?>">
                                        <?php } else { ?>
                                            <input class="campo-opcion" id="rolId<?= $Rol->getId(); ?>" name="rolesDestinoFormulario[]" type="checkbox" value="<?= $Rol->getId(); ?>">
                                        <?php } ?>
                                        <?= $Rol->getNombre(); ?>
                                    </label>
                                <?php }
                            } ?>
                            
                            <div class="invalid-feedback" id="errorSinDestinatarios">
                                <span class="oi oi-circle-x"></span> No seleccionó ningún destinatario.
                            </div>
                        </div>
                        <br/>

                        <p class="campo-cabecera">Fechas límite</p>
                        <p class="campo-descripcion">Si lo desea, puede definir una fecha a partir de la cual el formulario comenzará a aceptar respuestas, así como también puede definir una fecha en la que el formulario dejará de estar disponible. <strong>Estos campos son opcionales y, si no los rellena, el formulario que cree estará disponible hasta que lo deshabilite manualmente desde el gestor de formularios</strong>.</p>
                        <p class="campo-descripcion" style="font-style: italic;">Abierto desde:</p>
                        <button type="button" class="btn btn-outline-danger" id="borrarFechaApertura" style="float: right;" title="Haga clic aquí para borrar la fecha de apertura del formulario.">
                            <span class="oi oi-delete"></span>
                        </button>
                        <div style="overflow: hidden; padding-right: 5px;">
                            <input autocomplete="off" class="form-control" id="fechaApertura" name="fechaAperturaFormulario" placeholder="Haga clic aquí para abrir el calendario" readonly style="background-color: white; cursor: pointer;" type="date" value="<?= $formulario->getFechaApertura(); ?>"/>
                        </div>
                        <br/>

                        <p class="campo-descripcion" style="font-style: italic;">Abierto hasta:</p>
                        <button type="button" class="btn btn-outline-danger" id="borrarFechaCierre" style="float: right;" title="Haga clic aquí para borrar la fecha de cierre del formulario.">
                            <span class="oi oi-delete"></span>
                        </button>
                        <div style="overflow: hidden; padding-right: 5px;">
                            <input autocomplete="off" class="form-control" id="fechaCierre" name="fechaCierreFormulario" placeholder="Haga clic aquí para abrir el calendario" readonly style="background-color: white; cursor: pointer;" type="date" value="<?= $formulario->getFechaCierre(); ?>"/>
                        </div>
                        
                        <hr/>

                        <fieldset id="camposCreados" style="display: none;">
                            <input name="campoImaginario" type="hidden" value="&quot;obligatorio&quot;:1">
                        </fieldset>

                        <button class="btn btn-success" id="enviarFormulario" type="submit" value="Guardar cambios">
                            <span class="oi oi-check"></span>
                            Guardar cambios
                        </button>
                        
                        <button class="btn btn-warning" id="edicionCancelar" type="button" value="Cancelar edición">
                            <span class="oi oi-x"></span>
                            Cancelar edición
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

    <script type="text/javascript" src="../lib/colibri.creador.js"></script>
    <script type="text/javascript" src="../lib/colibri.formularios.js"></script>
</html>


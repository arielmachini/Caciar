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

$cantidadRespuestas = BDConexion::getInstancia()->query("" .
                "SELECT COUNT(`csv`) " .
                "FROM " . BDCatalogoTablas::BD_TABLA_RESPUESTA . " " .
                "WHERE `idFormulario` = {$idFormulario}")->fetch_array()[0];

if ($formulario['estaHabilitado'] == 1 || $cantidadRespuestas > 0) {
    /* El formulario está habilitado o registra respuestas, por lo tanto no se puede modificar. */
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

$consulta = BDConexion::getInstancia()->query("" .
        "SELECT * " .
        "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " " .
        "WHERE `idFormulario` = {$formulario->getID()} " .
        "ORDER BY `posicion` ASC");

$arregloCamposJson = array();

while ($campo = $consulta->fetch_assoc()) {
    /* Se guardan los atributos generales del campo. */
    $idCampo = $campo['idCampo'];
    $titulo = $campo['titulo'];
    $descripcion = $campo['descripcion'];
    $esObligatorio = $campo['esObligatorio'];

    /* ¿Se trata de un CAMPO DE TEXTO? */
    $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
            "SELECT `pista`, `subtipo` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_CAMPO_TEXTO . " " .
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $consultaPorSubtipo = $consultaPorSubtipo->fetch_assoc();
        $pista = $consultaPorSubtipo['pista'];
        $subtipo = $consultaPorSubtipo['subtipo'];

        if ($subtipo == CampoTexto::$CAMPO_TEXTO) {
            $jsonCampo = '{"tipoCampo": "CampoTexto", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": ' . $esObligatorio . ', "pista": "' . $pista . '"}';
        } else if ($subtipo == CampoTexto::$CAMPO_EMAIL) {
            $jsonCampo = '{"tipoCampo": "CampoEmail", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": ' . $esObligatorio . ', "pista": "' . $pista . '"}';
        } else {
            $jsonCampo = '{"tipoCampo": "CampoNumerico", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": ' . $esObligatorio . ', "pista": "' . $pista . '"}';
        }

        array_push($arregloCamposJson, $jsonCampo);

        continue;
    }

    /* Falso. ¿Se trata de un ÁREA DE TEXTO? */
    $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
            "SELECT `limiteCaracteres` " .
            "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_AREA_TEXTO . " " .
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $limiteCaracteres = $consultaPorSubtipo->fetch_assoc()['limiteCaracteres'];

        $jsonCampo = '{"tipoCampo": "AreaTexto", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": ' . $esObligatorio . ', "limite": ' . $limiteCaracteres . '}';

        array_push($arregloCamposJson, $jsonCampo);

        continue;
    }

    /* Falso. ¿Se trata de un SELECTOR DE FECHAS? */
    $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_FECHA . " " .
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $jsonCampo = '{"tipoCampo": "Fecha", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '" , "obligatorio": ' . $esObligatorio . '}';

        array_push($arregloCamposJson, $jsonCampo);

        continue;
    }

    /* Falso. ¿Se trata de una LISTA DESPLEGABLE? */
    $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_LISTA_DESPLEGABLE . " " .
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $jsonCampo = '{"tipoCampo": "ListaDesplegable", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": ' . $esObligatorio . ', "opciones": [';

        $elementos = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                "WHERE `idLista` = {$idCampo}");

        while ($elemento = $elementos->fetch_assoc()) {
            $valorElemento = $elemento['textoOpcion'];

            $jsonCampo .= '"' . $valorElemento . '",';
        }

        $jsonCampo = substr($jsonCampo, 0, -1);
        $jsonCampo .= ']}';

        array_push($arregloCamposJson, $jsonCampo);

        continue;
    }

    /* Falso. ¿Se trata de una LISTA DE CASILLAS DE VERIFICACIÓN? */
    $consultaPorSubtipo = BDConexion::getInstancia()->query("" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " NATURAL JOIN " . BDCatalogoTablas::BD_TABLA_LISTA_CHECKBOX . " " .
            "WHERE `idFormulario` = {$formulario->getID()} AND `idCampo` = {$idCampo}");

    if (mysqli_num_rows($consultaPorSubtipo) != 0) {
        $jsonCampo = '{"tipoCampo": "ListaCheckbox", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": 0, "opciones": [';

        $elementos = BDConexion::getInstancia()->query("" .
                "SELECT * " .
                "FROM " . BDCatalogoTablas::BD_TABLA_CHECKBOX . " " .
                "WHERE `idLista` = {$idCampo}");

        while ($elemento = $elementos->fetch_assoc()) {
            $valorElemento = $elemento['textoOpcion'];

            $jsonCampo .= '"' . $valorElemento . '",';
        }

        $jsonCampo = substr($jsonCampo, 0, -1);
        $jsonCampo .= ']}';

        array_push($arregloCamposJson, $jsonCampo);

        continue;
    }

    /* Falso. Se trata de una LISTA DE BOTONES DE RADIO. */
    $jsonCampo = '{"tipoCampo": "ListaBotonRadio", "titulo": "' . $titulo . '", "descripcion": "' . $descripcion . '", "obligatorio": ' . $esObligatorio . ', "opciones": [';

    $elementos = BDConexion::getInstancia()->query("" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_BOTON_RADIO . " " .
            "WHERE `idLista` = {$idCampo}");

    while ($elemento = $elementos->fetch_assoc()) {
        $valorElemento = $elemento['textoOpcion'];

        $jsonCampo .= '"' . $valorElemento . '",';
    }

    $jsonCampo = substr($jsonCampo, 0, -1);
    $jsonCampo .= ']}';

    array_push($arregloCamposJson, $jsonCampo);
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
        <script type="text/javascript">
            /* Se borra cualquier progreso que haya quedado guardado. */
            sessionStorage.clear();
        </script>
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
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Atención:</strong> Todos los campos acompañados por un asterisco (<span style="color: red; font-weight: bold;">*</span>) son obligatorios. Si no los rellena no podrá crear el formulario.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="alert alert-danger fade show" id="errorSinCampos" role="alert" style="display: none;">
                        <strong>Error:</strong> El formulario debe tener al menos un campo.
                    </div>
                    
                    <div class="alert alert-danger fade show" id="errorSinCamposObligatorios" role="alert" style="display: none;">
                        <strong>Error:</strong> El formulario debe tener al menos un campo <strong>obligatorio</strong>.
                    </div>

                    <form action="formulario.modificar.procesar.php" id="crearFormulario" method="post" novalidate>
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
                            <p class="campo-cabecera" for="tituloFormulario">Título del formulario<span style="color: red; font-weight: bold;">*</span></p>
                            <p class="campo-descripcion">Ingrese un título corto (pero descriptivo) para el formulario.</p>
                            <input autocomplete="off" class="form-control" id="tituloFormulario" maxlength="75" name="tituloFormulario" required spellcheck="true" type="text" value="<?= $formulario->getTitulo(); ?>"/>
                            <div class="invalid-feedback">
                                <span class="oi oi-circle-x"></span> No escribió un título para el formulario.
                            </div>
                        </div>
                        <br/>

                        <p class="campo-cabecera">Descripción del formulario</p>
                        <p class="campo-descripcion">Una descripción concisa, que facilite la comprensión de su formulario.</p>
                        <textarea class="form-control" id="descripcionFormulario" maxlength="400" name="descripcionFormulario" placeholder="Puede escribir una descripción de hasta 400 caracteres." spellcheck="true" style="max-height: 120px; min-height: 60px;"><?= $formulario->getDescripcion(); ?></textarea>
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
                        <br/>

                        <hr/>

                        <p class="campo-cabecera">Campos de su formulario<span style="color: red; font-weight: bold;">*</span></p>
                        <p class="campo-descripcion">A través de la siguiente herramienta puede agregar y editar los campos que tendrá su formulario.<br/><strong>Consejo:</strong> Si no entiende para qué sirve un determinado campo, sitúe su cursor sobre el <span class="campo-tipo-ayuda oi oi-question-mark"></span> ubicado junto al nombre de dicho campo para visualizar una breve descripción sobre este.</p>
                        
                        <table class="editor" id="editorCamposCrear">
                            <tbody>
                                <tr>
                                    <td style="border-right: 2px solid #ececec; padding-right: 15px;">
                                        <span class="editor-cabecera" style="margin-bottom: 17.5px;">EXPOSITOR DE CAMPOS</span>
                                        <table class="campos-disponibles">
                                            <tbody>
                                                <tr>
                                                    <td class="nuevo-campo" id="nuevoCampoTexto">
                                                        <span class="campo-tipo">Campo de texto <span class="campo-tipo-ayuda oi oi-question-mark" title="Es un campo de texto común. Admite una sola línea."></span></span><br/>
                                                        <img src="../lib/img/creador-formularios/CampoTexto.png">
                                                    </td>

                                                    <td class="nuevo-campo" id="nuevaListaDesplegable">
                                                        <span class="campo-tipo">Lista desplegable <span class="campo-tipo-ayuda oi oi-question-mark" title="Es una lista de opciones que se abre al hacerle clic. Solo se puede seleccionar una opción."></span></span><br/>
                                                        <img src="../lib/img/creador-formularios/ListaDesplegable.png">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="nuevo-campo" id="nuevoCampoFecha">
                                                        <span class="campo-tipo">Selector de fecha <span class="campo-tipo-ayuda oi oi-question-mark" title="Es un campo que solo admite fechas. Al hacer clic sobre este se despliega un calendario."></span></span><br/>
                                                        <img src="../lib/img/creador-formularios/Fecha.png">
                                                    </td>

                                                    <td class="nuevo-campo" id="nuevaListaVerificacion">
                                                        <span class="campo-tipo">Casillas de verificación <span class="campo-tipo-ayuda oi oi-question-mark" title="Es una lista de opciones. Se puede seleccionar más de una opción."></span></span><br/>
                                                        <img src="../lib/img/creador-formularios/ListaCheckbox.png">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="nuevo-campo" id="nuevaAreaTexto">
                                                        <span class="campo-tipo">Área de texto <span class="campo-tipo-ayuda oi oi-question-mark" title="Es similar a un campo de texto, pero admite más de una línea. Útil cuando requiere que el usuario escriba algo extenso."></span></span><br/>
                                                        <img src="../lib/img/creador-formularios/AreaTexto.png">
                                                    </td>

                                                    <td class="nuevo-campo" id="nuevaListaRadio">
                                                        <span class="campo-tipo">Botones de radio <span class="campo-tipo-ayuda oi oi-question-mark" title="Es una lista de opciones. Solo se puede seleccionar una opción."></span></span><br/>
                                                        <img src="../lib/img/creador-formularios/ListaRadio.png">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="min-width: 220px; padding-left: 15px;">
                                        <div class="div-crear" id="editorInicial">
                                            <span class="editor-cabecera">EDITOR DE CAMPOS</span>

                                            <span class="editor-propiedad-pista" style="padding-top: 10px;"><span class="oi oi-info" style="padding-right: 3px;"></span> Seleccione un campo del expositor para comenzar.</span>
                                        </div>

                                        <div class="div-crear oculto" id="editorCampoTexto">
                                            <span class="editor-cabecera" id="cabeceraCampoTexto">CAMPO DE TEXTO</span>

                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloCampoTexto" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloCampoTexto" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionCampoTexto" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioCampoTexto" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioCampoTexto" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <span class="editor-propiedad-cabecera">PISTA <span class="campo-tipo-info oi oi-info" title="Es el texto que se muestra dentro del campo antes de que el usuario escriba algo en él."></span></span>
                                            <input class="campo-editor" id="pistaCampoTexto" maxlength="50" placeholder="Esta es una pista de ejemplo"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 50.</span>

                                            <span class="editor-propiedad-cabecera">SUBTIPO <span class="campo-tipo-info oi oi-info" title="Determina el tipo de información que el usuario deberá ingresar en este campo."></span><div class="campo-error" id="errorSubtipoCampoTexto" style="display: none;" title="Debe especificar un subtipo para el campo de texto."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <label for="campoTextoEmail" style="font-size: 13px;"><input class="campo-opcion" id="campoTextoEmail" name="subtipoCampoTexto" type="radio"/> E-mail</label>
                                            <label for="campoTextoNumerico" style="font-size: 13px;"><input class="campo-opcion" id="campoTextoNumerico" name="subtipoCampoTexto" type="radio"/> Numérico</label>
                                            <label for="campoTextoTexto" style="font-size: 13px;"><input class="campo-opcion" id="campoTextoTexto" name="subtipoCampoTexto" type="radio"/> Texto</label>

                                            <br/>
                                            <button class="btn btn-sm btn-outline-success" id="guardarCampoTexto" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Crear campo
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarCampoTexto" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-crear oculto" id="editorListaDesplegable">
                                            <span class="editor-cabecera" id="cabeceraListaDesplegable">LISTA DESPLEGABLE</span>

                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloListaDesplegable" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloListaDesplegable" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionListaDesplegable" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioListaDesplegable" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioListaDesplegable" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <hr/>
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesListaDesplegable" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <div class="editor-opciones-botones" style="margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarOpcionLista" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarOpcionLista" title="Haga clic aquí para eliminar LA ÚLTIMA opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesListaDesplegable">
                                                <input class="campo-editor" id="opcionNumero1" maxlength="40" placeholder="Opción 1" type="text"/>
                                            </fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 100.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarListaDesplegable" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Crear campo
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarListaDesplegable" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-crear oculto" id="editorCampoFecha">
                                            <span class="editor-cabecera" id="cabeceraCampoFecha">SELECTOR DE FECHA</span>

                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloCampoFecha" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloCampoFecha" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionCampoFecha" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioCampoFecha" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioCampoFecha" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <br/>
                                            <button class="btn btn-sm btn-outline-success" id="guardarCampoFecha" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Crear campo
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarCampoFecha" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-crear oculto" id="editorCasillasVerificacion">
                                            <span class="editor-cabecera" id="cabeceraCasillasVerificacion">CASILLAS DE VERIFICACIÓN</span>

                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloCasillasVerificacion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloCasillasVerificacion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionCasillasVerificacion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <hr/>
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesCasillasVerificacion" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <div class="editor-opciones-botones" style="margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarCasillaVerificacion" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarCasillaVerificacion" title="Haga clic aquí para eliminar LA ÚLTIMA opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesCasillasVerificacion">
                                                <input class="campo-editor" id="opcionNumero1" maxlength="40" placeholder="Casilla de verificación 1" type="text"/>
                                            </fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 50.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarCasillasVerificacion" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Crear campo
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarCasillasVerificacion" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-crear oculto" id="editorAreaTexto">
                                            <span class="editor-cabecera" id="cabeceraAreaTexto">ÁREA DE TEXTO</span>

                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloAreaTexto" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloAreaTexto" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionAreaTexto" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioAreaTexto" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioAreaTexto" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <span class="editor-propiedad-cabecera">LÍMITE <span class="campo-tipo-info oi oi-info" title="Es la cantidad máxima de caracteres que el usuario podrá escribir en el área de texto. El valor mínimo que puede definir es 100 y el máximo 500."></span><div class="campo-error" id="errorLimiteAreaTexto" style="display: none;" title="Debe especificar un límite de caracteres (entre 100 y 500) para el área de texto."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="limiteAreaTexto" max="500" min="100" step="5" type="number"/>

                                            <br/>
                                            <button class="btn btn-sm btn-outline-success" id="guardarAreaTexto" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Crear campo
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarAreaTexto" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-crear oculto" id="editorBotonesRadio">
                                            <span class="editor-cabecera" id="cabeceraBotonesRadio">BOTONES DE RADIO</span>

                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloBotonesRadio" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloBotonesRadio" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionBotonesRadio" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioBotonesRadio" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioBotonesRadio" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <hr/>
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesBotonesRadio" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div><div class="campo-error" id="errorOpcionesBotonesRadioIguales" style="display: none;" title="No puede ingresar el mismo valor para todas las opciones."><span class="oi oi-warning"></span> CAMBIAR</div></span>
                                            <div class="editor-opciones-botones" style="margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarBotonRadio" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarBotonRadio" title="Haga clic aquí para eliminar LA ÚLTIMA opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesBotonesRadio">
                                                <input class="campo-editor" id="opcionNumero1" maxlength="40" placeholder="Botón de radio 1" type="text"/>
                                                <input class="campo-editor" id="opcionNumero2" maxlength="40" placeholder="Botón de radio 2" style="margin-top: 5px;" type="text"/>
                                            </fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 50.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarBotonesRadio" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Crear campo
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarBotonesRadio" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <table class="editor oculto" id="editorCamposEditar">
                            <tbody>
                                <tr>
                                    <td><span class="editor-cabecera" id="cabeceraEdicionCampo" style="margin-bottom: 17.5px;">EDITANDO CAMPO</span>
                                        <input id="idCampoEditado" type="hidden" value="">
                                        
                                        <div class="div-editar oculto" id="edicionCampoTexto">
                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloCampoTextoEdicion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloCampoTextoEdicion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionCampoTextoEdicion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioCampoTextoEdicion" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioCampoTextoEdicion" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <span class="editor-propiedad-cabecera">PISTA <span class="campo-tipo-info oi oi-info" title="Es el texto que se muestra dentro del campo antes de que el usuario escriba algo en él."></span></span>
                                            <input class="campo-editor" id="pistaCampoTextoEdicion" maxlength="50" placeholder="Esta es una pista de ejemplo"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 50.</span>

                                            <span class="editor-propiedad-cabecera">SUBTIPO <span class="campo-tipo-info oi oi-info" title="Determina el tipo de información que el usuario deberá ingresar en este campo."></span><div class="campo-error" id="errorSubtipoCampoTextoEdicion" style="display: none;" title="Debe especificar un subtipo para el campo de texto."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <label for="campoTextoEmailEdicion" style="font-size: 13px;"><input class="campo-opcion" id="campoTextoEmailEdicion" name="subtipoCampoTextoEdicion" type="radio"/> E-mail</label>
                                            <label for="campoTextoNumericoEdicion" style="font-size: 13px;"><input class="campo-opcion" id="campoTextoNumericoEdicion" name="subtipoCampoTextoEdicion" type="radio"/> Numérico</label>
                                            <label for="campoTextoTextoEdicion" style="font-size: 13px;"><input class="campo-opcion" id="campoTextoTextoEdicion" name="subtipoCampoTextoEdicion" type="radio"/> Texto</label>

                                            <br/>
                                            <button class="btn btn-sm btn-outline-success" id="guardarEdicionCampoTexto" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar cambios
                                            </button>
                                            
                                            <button class="btn btn-sm btn-outline-danger" id="descartarEdicionCampoTexto" type="button">
                                                <span class="oi oi-x"></span>
                                                Cancelar
                                            </button>
                                        </div>
                                        
                                        <div class="div-editar oculto" id="edicionListaDesplegable">
                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloListaDesplegableEdicion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloListaDesplegableEdicion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionListaDesplegableEdicion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioListaDesplegableEdicion" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioListaDesplegableEdicion" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <hr/>
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesListaDesplegableEdicion" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <div class="editor-opciones-botones" style="justify-content: flex-start; margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarOpcionListaEdicion" style="margin-right: 5px; width: auto !important;" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarOpcionListaEdicion" style="width: auto !important;" title="Haga clic aquí para eliminar LA ÚLTIMA opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesListaDesplegableEdicion"></fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 100.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarEdicionListaDesplegable" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar cambios
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarEdicionListaDesplegable" type="button">
                                                <span class="oi oi-x"></span>
                                                Cancelar
                                            </button>
                                        </div>
                                        
                                        <div class="div-editar oculto" id="edicionCampoFecha">
                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloCampoFechaEdicion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloCampoFechaEdicion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionCampoFechaEdicion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioCampoFechaEdicion" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioCampoFechaEdicion" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <br/>
                                            <button class="btn btn-sm btn-outline-success" id="guardarEdicionCampoFecha" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar cambios
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarEdicionCampoFecha" type="button">
                                                <span class="oi oi-x"></span>
                                                Cancelar
                                            </button>
                                        </div>
                                        
                                        <div class="div-editar oculto" id="edicionCasillasVerificacion">
                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloCasillasVerificacionEdicion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloCasillasVerificacionEdicion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionCasillasVerificacionEdicion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <hr/>
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesCasillasVerificacionEdicion" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <div class="editor-opciones-botones" style="justify-content: flex-start; margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarCasillaVerificacionEdicion" style="margin-right: 5px; width: auto !important;" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarCasillaVerificacionEdicion" style="width: auto !important;" title="Haga clic aquí para eliminar LA ÚLTIMA opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesCasillasVerificacionEdicion"></fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 50.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarEdicionCasillasVerificacion" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar cambios
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarEdicionCasillasVerificacion" type="button">
                                                <span class="oi oi-x"></span>
                                                Cancelar
                                            </button>
                                        </div>
                                        
                                        <div class="div-editar oculto" id="edicionAreaTexto">
                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloAreaTextoEdicion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloAreaTextoEdicion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionAreaTextoEdicion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioAreaTextoEdicion" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioAreaTextoEdicion" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <span class="editor-propiedad-cabecera">LÍMITE <span class="campo-tipo-info oi oi-info" title="Es la cantidad máxima de caracteres que el usuario podrá escribir en el área de texto. El valor mínimo que puede definir es 100 y el máximo 500."></span><div class="campo-error" id="errorLimiteAreaTextoEdicion" style="display: none;" title="Debe especificar un límite de caracteres (entre 100 y 500) para el área de texto."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="limiteAreaTextoEdicion" max="500" min="100" step="5" type="number"/>

                                            <br/>
                                            <button class="btn btn-sm btn-outline-success" id="guardarEdicionAreaTexto" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar cambios
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarEdicionAreaTexto" type="button">
                                                <span class="oi oi-x"></span>
                                                Cancelar
                                            </button>
                                        </div>

                                        <div class="div-editar oculto" id="edicionBotonesRadio">
                                            <span class="editor-propiedad-cabecera">TÍTULO <div class="campo-error" id="errorTituloBotonesRadioEdicion" style="display: none;" title="Debe especificar un título para el campo."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <input class="campo-editor" id="tituloBotonesRadioEdicion" maxlength="30"/>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 30.</span>

                                            <span class="editor-propiedad-cabecera">DESCRIPCIÓN</span>
                                            <textarea class="campo-editor" id="descripcionBotonesRadioEdicion" maxlength="200" style="max-height: 120px;"></textarea>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de caracteres: 200.</span>

                                            <span class="editor-propiedad-cabecera">¿ES OBLIGATORIO?</span>
                                            <label for="obligatorioBotonesRadioEdicion" style="font-size: 13px;"><input class="campo-opcion" id="obligatorioBotonesRadioEdicion" type="checkbox" value="Obligatorio"/> Es obligatorio</label>

                                            <hr/>
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesBotonesRadioEdicion" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div><div class="campo-error" id="errorOpcionesBotonesRadioIgualesEdicion" style="display: none;" title="No puede ingresar el mismo valor para todas las opciones."><span class="oi oi-warning"></span> CAMBIAR</div></span>
                                            <div class="editor-opciones-botones" style="justify-content: flex-start; margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarBotonRadioEdicion" style="margin-right: 5px; width: auto !important;" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarBotonRadioEdicion" style="width: auto !important;" title="Haga clic aquí para eliminar LA ÚLTIMA opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesBotonesRadioEdicion"></fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 50.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarEdicionBotonesRadio" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar cambios
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarEdicionBotonesRadio" type="button">
                                                <span class="oi oi-x"></span>
                                                Cancelar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <br/>
                        
                        <?php if (!empty($arregloCamposJson)) { ?>
                            <table class="previa-formulario">
                        <?php } else { ?>
                            <table class="previa-formulario oculto">
                        <?php } ?>
                            <tbody>
                                <tr>
                                    <td style="max-width: 300px;">
                                        <span class="editor-cabecera" style="margin-bottom: 17.5px;">VISTA PREVIA DEL FORMULARIO</span>
                                        <div id="vistaPreviaFormulario">
                                            <?php for ($i = 1; $i <= count($arregloCamposJson); $i++) {
                                                $jsonCampo = json_decode($arregloCamposJson[($i - 1)]);
                                                
                                                switch ($jsonCampo->tipoCampo) {
                                                    case "CampoTexto":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled placeholder="<?= $jsonCampo->pista; ?>" type="text"/></div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled placeholder="<?= $jsonCampo->pista; ?>" type="text"/></div>
                                                        <?php }
                                                        
                                                        break;
                                                    case "CampoEmail":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled placeholder="<?= $jsonCampo->pista; ?>" type="email"/></div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled placeholder="<?= $jsonCampo->pista; ?>" type="email"/></div>
                                                        <?php }
                                                        
                                                        break;
                                                    case "CampoNumerico":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled placeholder="<?= $jsonCampo->pista; ?>" type="number"/></div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled placeholder="<?= $jsonCampo->pista; ?>" type="number"/></div>
                                                        <?php }
                                                        
                                                        break;
                                                    case "AreaTexto":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><textarea class="campo-editor" disabled maxlength="<?= $jsonCampo->limite; ?>" style="resize: none;"></textarea></div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><textarea class="campo-editor" disabled maxlength="<?= $jsonCampo->limite; ?>" style="resize: none;"></textarea></div>
                                                        <?php }
                                                        
                                                        break;
                                                    case "Fecha":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled type="date"/></div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p><input class="campo-editor" disabled type="date"/></div>
                                                        <?php }
                                                        
                                                        break;
                                                    case "ListaDesplegable":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p>
                                                                <select class="campo-editor" disabled>
                                                                    <?php foreach ($jsonCampo->opciones as $opcion) { ?>
                                                                        <option value="<?= $opcion; ?>"><?= $opcion; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p>
                                                                <select class="campo-editor" disabled>
                                                                    <?php foreach ($jsonCampo->opciones as $opcion) { ?>
                                                                        <option value="<?= $opcion; ?>"><?= $opcion; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        <?php }
                                                        
                                                        break;
                                                    case "ListaCheckbox": ?>
                                                        <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p>
                                                            <?php foreach ($jsonCampo->opciones as $opcion) { ?>
                                                                <label style="font-size: 13px;"><input class="opcion-editor" disabled type="checkbox" value="<?= $opcion; ?>"/> <?= $opcion; ?></label>
                                                            <?php } ?>
                                                        </div>
                                                        <?php break;
                                                    case "ListaBotonRadio":
                                                        if ($jsonCampo->obligatorio == 1) { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?><span style="color: red; font-weight: bold;">*</span></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p>
                                                                <?php foreach ($jsonCampo->opciones as $opcion) { ?>
                                                                    <label style="font-size: 13px;"><input class="opcion-editor" disabled type="radio" value="<?= $opcion; ?>"/> <?= $opcion; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } else { ?>
                                                            <div id="campoID<?= $i; ?>" style="border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;"><p class="campo-cabecera" style="font-size: 14px !important;"><?= $jsonCampo->titulo; ?></p><p class="campo-descripcion" style="font-size: 13px !important;"><?= $jsonCampo->descripcion; ?></p>
                                                                <?php foreach ($jsonCampo->opciones as $opcion) { ?>
                                                                    <label style="font-size: 13px;"><input class="opcion-editor" disabled type="radio" value="<?= $opcion; ?>"/> <?= $opcion; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        <?php }
                                                        
                                                        break;
                                                }
                                            } ?>
                                        </div>
                                    </td>

                                    <td style="min-width: 220px; padding-left: 15px; text-align: center; width: 220px;">
                                        <span class="editor-cabecera" style="margin-bottom: 17.5px; margin-top: 9px;">ACCIONES</span>
                                        <div id="botonesPreviaFormulario">
                                            <?php for ($i = 1; $i <= count($arregloCamposJson); $i++) {
                                                $jsonCampo = json_decode($arregloCamposJson[($i - 1)]); ?>
                                                <div id="accionesCampoID<?= $i; ?>" style="align-items: flex-start; height: auto; display: flex; justify-content: center; padding-top: 10px;"><button class="btn btn-primary" onclick="moverCampo(<?= $i; ?>, 'ARRIBA')" style="flex-grow: 1; margin-right: 5px;" title="Mover este campo una posición arriba." type="button"><span class="oi oi-arrow-top"></span></button><button class="btn btn-primary" onclick="moverCampo(<?= $i; ?>, 'ABAJO')" style="flex-grow: 1; margin-right: 5px;" title="Mover este campo una posición abajo." type="button"><span class="oi oi-arrow-bottom"></span></button><button class="btn btn-warning" onclick="editarCampo(<?= $i; ?>)" style="flex-grow: 1; margin-right: 5px;" title="Editar este campo." type="button"><span class="oi oi-pencil"></span></button><button class="btn btn-danger" onclick="eliminarCampo(<?= $i; ?>)" style="flex-grow: 1;" title="Eliminar este campo del formulario." type="button"><span class="oi oi-trash"></span></button></div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr/>

                        <fieldset id="camposCreados" style="display: none;">
                            <?php $i = 1;
                            foreach ($arregloCamposJson as $jsonCampo) { ?>
                            <input name="campoID<?= $i; ?>" type="hidden" value="<?= htmlspecialchars($jsonCampo); ?>">
                                <?php $i++;
                            } ?>
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
    <script type="text/javascript">
        /* Se corrigen detalles visuales en la interfaz. */
        
        $(document).ready(function () {
            desactivarBotonesMoverInnecesarios();
            
            var idCampo = 0;
            
            $('#botonesPreviaFormulario > div').each(function () {
                idCampo++;
                
                $(this).css('height', $('#campoID' + idCampo).height() + 11 + 'px');
            });
            
            sessionStorage.setItem('id', Number(idCampo));
        });
    </script>
</html>


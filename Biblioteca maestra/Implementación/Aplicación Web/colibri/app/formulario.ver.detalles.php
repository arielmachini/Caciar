<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';

if (!(ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS) || ControlAcceso::verificaPermiso(PermisosSistema::PERMISO_ADMINISTRAR_GESTORES))) {
    ControlAcceso::redireccionar();
}

require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Usuario.Class.php';

$usuario = new Usuario($_SESSION['usuario']->id);

/* Se sanitiza la variable recibida por GET. */
$idFormulario = filter_var(filter_input(INPUT_GET, "id"), FILTER_SANITIZE_NUMBER_INT);

if ($usuario->esAdministradorDeGestores()) {
    $query = "" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `idFormulario` = {$idFormulario}";
} else {
    $query = "" .
            "SELECT * " .
            "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
            "WHERE `idCreador` = {$_SESSION['usuario']->id} AND `idFormulario` = {$idFormulario}";
}

$formulario = BDConexion::getInstancia()->query($query);

if (mysqli_num_rows($formulario) == 0) {
    /* El formulario no existe o el usuario que intenta acceder no tiene acceso a este. */
    ControlAcceso::redireccionar("formulario.gestor.php");
}

$formulario = $formulario->fetch_assoc();

$cantidadRespuestas = BDConexion::getInstancia()->query("" .
                "SELECT COUNT(`csv`) " .
                "FROM " . BDCatalogoTablas::BD_TABLA_RESPUESTA . " " .
                "WHERE `idFormulario` = {$idFormulario}")->fetch_array()[0];

$rolesDestino = BDConexion::getInstancia()->query("" .
        "SELECT `nombre` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " JOIN " . BDCatalogoTablas::BD_TABLA_ROL . " ON " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . ".`idRol` = " . BDCatalogoTablas::BD_TABLA_ROL . ".`id` " .
        "WHERE `idFormulario` = {$idFormulario}");
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
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="../gui/css/colibri.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        <style>
            div.informacion {
                background-color: #e9eaec;
                border-radius: 10px;
                -moz-border-radius: 10px;
                color: #42484d;
                margin-bottom: 10px;
                padding: 20px;
            }
            
            div.informacion > h5 {
                font-weight: bold;
            }
            
            div.informacion > div {
                display: block;
            }
            
            div.informacion span {
                margin-right: 10px;
            }
            
            div.informacion-principal {
                background-color: #d9ecff !important;
                border: 1px solid;
                border-color: #b8daff;
                color: #00438c !important;
            }
            
            div.informacion-respuestas {
                background-color: #d9f2df !important;
                border: 1px solid;
                border-color: #c3e6cb;
                color: #176028 !important;
            }
            
            p.separador-opciones-descarga-pdf {
                border-bottom: 1px solid #6c757d;
                line-height: 0.1em;
                margin: 25px 0 25px;
                text-align: center;
                width: 100%;
            }
            
            p.separador-opciones-descarga-pdf > span {
                background: #e9eaec;
                font-weight: bold;
                padding: 0 10px;
            }
        </style>

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        
        <!-- Scripts requeridos por el sistema Colibrí -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Detalles del formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Detalles del formulario</h3>
                </div>
                <div class="card-body">
                    <div class="informacion informacion-principal">
                        <h5><span class="oi oi-double-quote-serif-left"></span>Título</h5>
                        <div><strong><?= $formulario['titulo']; ?></strong></div>
                        <br/>
                        
                        <h5><span class="oi oi-excerpt"></span>Descripción</h5>
                        <div>
                            <?php if ($formulario['descripcion'] != "") { ?>
                                «<i><?= $formulario['descripcion']; ?></i>»
                            <?php } else { ?>
                                Sin descripción
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="informacion">
                        <h5><span class="oi oi-envelope-open"></span>Dirección de e-mail que recibe las respuestas</h5>
                        <div><?= $formulario['emailReceptor']; ?></div>
                    </div>
                    
                    <div class="informacion">
                        <h5><span class="oi oi-calendar"></span>Fecha de creación</h5>
                        <div><?= $formulario['fechaCreacion']; ?></div>
                        <br/>
                        
                        <h5><span class="oi oi-calendar"></span>Fecha de apertura</h5>
                        <div>
                            <?php if ($formulario['fechaApertura'] != "") { ?>
                                <?= $formulario['fechaApertura']; ?>
                            <?php } else { ?>
                                No fue definida
                            <?php } ?>
                        </div>
                        <br/>
                        
                        <h5><span class="oi oi-calendar"></span>Fecha de cierre</h5>
                        <div>
                            <?php if ($formulario['fechaCierre'] != "") { ?>
                                <?= $formulario['fechaCierre']; ?>
                            <?php } else { ?>
                                No fue definida
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="informacion">
                        <h5><span class="oi oi-people"></span>Destinatarios del formulario</h5>
                        <div>
                            <?php
                            while ($rol = $rolesDestino->fetch_assoc()) {
                                if ($rol['nombre'] !== PermisosSistema::ROL_GESTOR && $rol['nombre'] !== PermisosSistema::ROL_ADMINISTRADOR_GESTORES && $rol['nombre'] !== PermisosSistema::ROL_ADMINISTRADOR) {
                            ?>
                                    • <?= $rol['nombre']; ?><br/>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="informacion informacion-respuestas">
                        <h5><span class="oi oi-chat"></span>Respuestas registradas</h5>
                        <div>
                            <?php if ($cantidadRespuestas > 0) { ?>
                                Actualmente, este formulario registra <?= $cantidadRespuestas; ?> respuesta(s)<br/>
                                
                                <a class="btn btn-sm btn-success" href="formulario.respuestas.php?id=<?= $idFormulario; ?>&csv=true" style="margin-top: 20px;" target="_blank">
                                    <span class="oi oi-spreadsheet"></span>Descargar respuestas en formato CSV
                                </a>
                                
                                <button class="btn btn-sm btn-secondary" id="verOpcionesDescargaPDF" style="margin-top: 20px;" title="Haga clic aquí para ver las opciones de descarga para este formato." type="button">
                                    <span class="oi oi-file"></span>Descargar respuestas en formato PDF
                                </button>
                                
                                <div class="informacion" id="opcionesDescargaPDF" style="border: 1px solid; border-color: #c3e6cb; display: none; margin-top: 25px;">
                                    <h5><span class="oi oi-file"></span>Opciones de descarga para formato PDF</h5> 
                                    <div>
                                        <strong>Opción #1:</strong> Puede descargar un documento PDF que sólo incluya respuestas dentro de un intervalo que usted defina. <span class="campo-tipo-ayuda oi oi-question-mark" id="ayudaOpcion1" style="cursor: pointer !important;" title="Haga clic aquí para visualizar una mini-guía sobre la opción de descarga #1."></span><br/>
                                        Tenga en cuenta que <strong>deberá definir al menos una de las dos fechas</strong>.<br/>

                                        <?php
                                        $respuestas = BDConexion::getInstancia()->query("" .
                                                "SELECT `csv` " .
                                                "FROM " . BDCatalogoTablas::BD_TABLA_RESPUESTA . " " .
                                                "WHERE `idFormulario` = {$idFormulario} " .
                                                "ORDER BY `idRespuesta` ASC");
                                        
                                        $arregloFechas = array();
                                        
                                        while ($csv = $respuestas->fetch_array()) {
                                            $respuesta = str_getcsv($csv[0]);
                                            $fecha = substr($respuesta[0], 0, strlen($respuesta[0]) - 9); // Se elimina la hora de la fecha.
                                            
                                            if (!in_array($fecha, $arregloFechas)) { // Se evita la inserción de fechas repetidas.
                                                $arregloFechas[] = $fecha;
                                            }
                                        }
                                        ?>
                                        
                                        <span style="display: block; margin-bottom: 10px; margin-top: 25px;">
                                            <span class="oi oi-calendar"></span>Incluir respuestas desde...
                                        </span>

                                        <select class="form-control" id="fechaDesde">
                                            <option selected value="">No limitar</option>
                                            <?php foreach ($arregloFechas as $fecha) { ?>
                                                <option value="<?= $fecha; ?>"><?= $fecha; ?></option>
                                            <?php } ?>
                                        </select>

                                        <span style="display: block; margin-bottom: 10px; margin-top: 25px;">
                                            <span class="oi oi-calendar"></span>Incluir respuestas hasta... <span class="campo-tipo-ayuda oi oi-warning" id="avisoFechaHasta" style="display: none;" title="Aviso: Esta fecha es menor que la que definió en el campo de arriba, por lo tanto será ignorada."></span>
                                        </span>

                                        <select class="form-control" id="fechaHasta">
                                            <option selected value="">No limitar</option>
                                            <?php foreach ($arregloFechas as $fecha) { ?>
                                                <option value="<?= $fecha; ?>"><?= $fecha; ?></option>
                                            <?php } ?>
                                        </select>

                                        <button class="btn btn-sm btn-primary" id="descargarOpcion1" style="margin-top: 20px;" title="Haga clic aquí para descargar un documento PDF con las respuestas dentro del intervalo que haya definido." type="button">
                                            <span class="oi oi-arrow-circle-bottom"></span>Descargar (opción #1)
                                        </button>
                                        
                                        <?php if (date("d/m/Y") === end($arregloFechas)) { ?>
                                            <a class="btn btn-sm btn-secondary" href="formulario.respuestas.php?id=<?= $idFormulario; ?>&desde=<?= date("d-m-Y"); ?>&hasta=<?= date("d-m-Y"); ?>" style="margin-top: 20px;" title="Haga clic aquí para descargar un documento PDF con las respuestas para este formulario registradas el día de hoy (<?= date("d/m/Y"); ?> hasta las <?= date("h:i A"); ?>)." type="button">
                                                <span class="oi oi-bolt"></span>Sólo descargar las respuestas de hoy
                                            </a>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-secondary" disabled style="cursor: not-allowed; margin-top: 20px;" title="Hasta el momento, hoy no se recibieron nuevas respuestas para este formulario. Por favor vuelva más tarde." type="button">
                                                <span class="oi oi-bolt"></span>Sólo descargar las respuestas de hoy
                                            </button>
                                        <?php } ?>

                                        <p class="separador-opciones-descarga-pdf">
                                            <span>Ó</span>
                                        </p>

                                        <strong>Opción #2:</strong> Puede descargar un documento PDF con <strong>todas</strong> las respuestas que registra el formulario hasta el momento.<br/>

                                        <a class="btn btn-sm btn-primary" href="formulario.respuestas.php?id=<?= $idFormulario; ?>" style="margin-top: 20px;">
                                            <span class="oi oi-arrow-circle-bottom"></span>Descargar (opción #2)
                                        </a>
                                    </div>
                                </div>
                            <?php } else { ?>
                                Este formularios aún no registra respuestas
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a class="btn btn-primary" href="formulario.gestor.php">
                        <span class="oi oi-account-logout"></span> Volver
                    </a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>
    
    <script type="text/javascript">
        /* Se habilitan los tooltips de jQuery. */
        $(document).tooltip({
            show: {
                effect: "fade",
                delay: 0,
                duration: 275
            }
        });
        
        $('#verOpcionesDescargaPDF').click(function() {
            $('#opcionesDescargaPDF').slideDown(300);
        });
        
        $('#ayudaOpcion1').click(function() {
            $.confirm({
                icon: 'oi oi-book',
                title: 'Mini-guía sobre la opción #1',
                content: '<span class="oi oi-lightbulb"></span> <strong>Tip #1:</strong> Si sólo define «<i>Incluir respuestas desde...</i>» sólo se incluirán aquellas respuestas enviadas <strong>a partir de esa fecha</strong> hasta hoy (<?= date("d/m/Y"); ?>).<br/><span class="oi oi-lightbulb"></span> <strong>Tip #2:</strong> Si sólo define «<i>Incluir respuestas hasta...</i>» sólo se incluirán aquellas respuestas enviadas desde la primera fecha registrada (en este caso, ' + document.getElementById('fechaDesde').options[1].value + ') <strong>hasta la fecha que haya definido</strong>.<br/><span class="oi oi-lightbulb"></span> <strong>Tip #3:</strong> Si define la misma fecha para «<i>Incluir respuestas desde...</i>» y «<i>Incluir respuestas hasta...</i>», sólo se incluirán las respuestas <strong>enviadas durante ese día</strong>.<br/><br/><span class="oi oi-warning"></span> También tenga en consideración que, si la fecha definida en «<i>Incluir respuestas desde...</i>» <strong>es mayor</strong> que le fecha definida en «<i>Incluir respuestas hasta...</i>», <strong>esta última</strong> («<i>Incluir respuestas hasta...</i>») <strong>será ignorada</strong> (o expresado de otra manera, sólo se tendrá en cuenta la fecha definida en «<i>Incluir respuestas desde...</i>»).',
                animation: 'none',
                closeAnimation: 'none',
                theme: 'material',
                type: 'blue',
                useBootstrap: false,
                buttons: {
                    confirm: {
                        btnClass: 'btn-blue',
                        text: 'Cerrar'
                    }
                }
            });
        });
        
        $('#descargarOpcion1').click(function() {
            if ($('#fechaDesde :selected').val() === '' && $('#fechaHasta :selected').val() === '') {
                $.confirm({
                    icon: 'oi oi-warning',
                    title: 'Defina un intervalo para continuar',
                    content: 'Para descargar respuestas mediante la opción #1, primero debe definir <strong>al menos una de las dos fechas</strong> para el intervalo («<i>Incluir respuestas desde...</i>» o «<i>Incluir respuestas hasta...</i>»).',
                    animation: 'none',
                    closeAnimation: 'none',
                    theme: 'material',
                    type: 'red',
                    useBootstrap: false,
                    buttons: {
                        confirm: {
                            btnClass: 'btn-red',
                            text: 'Ok'
                        }
                    }
                });
            } else {
                if (!($('#fechaDesde :selected').val() === '') && !($('#fechaHasta :selected').val() === '')) { // Ambas fechas fueron definidas.
                    var fechaInicial = new Date(parseInt($('#fechaDesde :selected').val().substring(6, 10)), (parseInt($('#fechaDesde :selected').val().substring(3, 5)) - 1), parseInt($('#fechaDesde :selected').val().substring(0, 2)));
                    var fechaFinal = new Date(parseInt($('#fechaHasta :selected').val().substring(6, 10)), (parseInt($('#fechaHasta :selected').val().substring(3, 5)) - 1), parseInt($('#fechaHasta :selected').val().substring(0, 2)));
                    
                    if (fechaInicial > fechaFinal) { // Se ignora la fecha final del intervalo. 
                       window.location.replace('formulario.respuestas.php?id=<?= $idFormulario; ?>&desde=' + $('#fechaDesde :selected').val().replace(/\//g, '-'));
                    } else {
                        window.location.replace('formulario.respuestas.php?id=<?= $idFormulario; ?>&desde=' + $('#fechaDesde :selected').val().replace(/\//g, '-') + "&hasta=" + $('#fechaHasta :selected').val().replace(/\//g, '-'));
                    }
                } else if (!($('#fechaDesde :selected').val() === '')) { // Sólo se definió "desde".
                    window.location.replace('formulario.respuestas.php?id=<?= $idFormulario; ?>&desde=' + $('#fechaDesde :selected').val().replace(/\//g, '-'));
                } else { // Sólo se definió "hasta".
                    window.location.replace('formulario.respuestas.php?id=<?= $idFormulario; ?>&hasta=' + $('#fechaHasta :selected').val().replace(/\//g, '-'));
                }
            }
        });
        
        $('#fechaDesde').on('change', function() {
            if (!($('#fechaDesde :selected').val() === '') && !($('#fechaHasta :selected').val() === '')) {
                var fechaInicial = new Date(parseInt($('#fechaDesde :selected').val().substring(6, 10)), (parseInt($('#fechaDesde :selected').val().substring(3, 5)) - 1), parseInt($('#fechaDesde :selected').val().substring(0, 2)));
                var fechaFinal = new Date(parseInt($('#fechaHasta :selected').val().substring(6, 10)), (parseInt($('#fechaHasta :selected').val().substring(3, 5)) - 1), parseInt($('#fechaHasta :selected').val().substring(0, 2)));
                
                if (fechaInicial > fechaFinal) {
                    $('#avisoFechaHasta').show();
                } else {
                    $('#avisoFechaHasta').hide();
                }
            } else {
                $('#avisoFechaHasta').hide();
            }
        });
        
        $('#fechaHasta').on('change', function() {
            if (!($('#fechaDesde :selected').val() === '') && !($('#fechaHasta :selected').val() === '')) {
                var fechaInicial = new Date(parseInt($('#fechaDesde :selected').val().substring(6, 10)), (parseInt($('#fechaDesde :selected').val().substring(3, 5)) - 1), parseInt($('#fechaDesde :selected').val().substring(0, 2)));
                var fechaFinal = new Date(parseInt($('#fechaHasta :selected').val().substring(6, 10)), (parseInt($('#fechaHasta :selected').val().substring(3, 5)) - 1), parseInt($('#fechaHasta :selected').val().substring(0, 2)));
                
                if (fechaInicial > fechaFinal) {
                    $('#avisoFechaHasta').show();
                } else {
                    $('#avisoFechaHasta').hide();
                }
            } else {
                $('#avisoFechaHasta').hide();
            }
        });
    </script>
</html>


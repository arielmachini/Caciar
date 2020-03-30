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
$cantidadRespuestas = BDConexion::getInstancia()->query("" .
                "SELECT COUNT(`csv`) " .
                "FROM " . BDCatalogoTablas::BD_TABLA_RESPUESTA . " " .
                "WHERE `idFormulario` = {$idFormulario}")->fetch_array()[0];

if (mysqli_num_rows($formulario) == 0) {
    /* El formulario no existe o el usuario que intenta acceder no tiene acceso a este. */
    ControlAcceso::redireccionar("formulario.gestor.php");
}

$formulario = $formulario->fetch_assoc();

$rolesDestino = BDConexion::getInstancia()->query("" .
        "SELECT `nombre` " .
        "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " JOIN " . BDCatalogoTablas::BD_TABLA_ROL . " ON " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . ".`idRol` = " . BDCatalogoTablas::BD_TABLA_ROL . ".`id` " .
        "WHERE `idFormulario` = {$idFormulario}");
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="../gui/css/colibri.css" />
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
        </style>

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>

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
                                
                                <a class="btn btn-sm btn-secondary" href="formulario.respuestas.php?id=<?= $idFormulario; ?>" style="margin-top: 20px;" target="_blank" title="Haga clic aquí para abrir una nueva ventana donde podrá ver todas las respuestas a este formulario y descargarlas en formato PDF.">
                                    <span class="oi oi-file"></span>Descargar respuestas en formato PDF
                                </a>
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
</html>


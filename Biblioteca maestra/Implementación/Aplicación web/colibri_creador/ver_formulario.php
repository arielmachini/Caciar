<!DOCTYPE html>

<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once '../lib/ControlAcceso.class.php';
// ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CONSULTAR);

include_once './Campos.class.php';
include_once './Formulario.class.php';

session_start();

$idformulario = filter_input(INPUT_GET, "id");

$formularioRecibido = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO WHERE `idFormulario` = {$idformulario}")->fetch_assoc();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title><?= Constantes::NOMBRE_SISTEMA; ?> ~ <?= $formularioRecibido['titulo'] ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link href="../gui/estilo.css" type="text/css" rel="stylesheet">
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet">
        <link href="./gui/formulario.css" rel="stylesheet">
    </head>

    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <?php
                    $FormularioRecuperado = new Formulario($formularioRecibido['fechaCreacion']);

                    $FormularioRecuperado->setTitulo($formularioRecibido['titulo']);
                    $FormularioRecuperado->setDescripcion($formularioRecibido['descripcion']);
                    $FormularioRecuperado->setEmailReceptor($formularioRecibido['emailReceptor']);
                    $FormularioRecuperado->setFechaInicio($formularioRecibido['fechaInicio']);
                    $FormularioRecuperado->setFechaFin($formularioRecibido['fechaFin']);

                    $camposFormulario = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM CAMPO WHERE `idFormulario` = {$idformulario}");

                    while ($campoActual = $camposFormulario->fetch_assoc()) {
                        $idcampo = $campoActual['idCampo'];
                        $campoTextoActual = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * " .
                                        "FROM CAMPO_TEXTO " .
                                        "WHERE `idCampo` = {$idcampo}")->fetch_assoc();
                        $CampoTexto = new CampoTexto();

                        $CampoTexto->setTitulo($campoActual['titulo']);
                        $CampoTexto->setDescripcion($campoActual['descripcion']);

                        if ($campoActual['esObligatorio'] == 1) {
                            $CampoTexto->setEsObligatorio(true);
                        } else {
                            $CampoTexto->setEsObligatorio(false);
                        }

                        $CampoTexto->setPosicion($campoActual['posicion']);
                        $CampoTexto->setPista($campoTextoActual['pista']);

                        $FormularioRecuperado->agregarCampo($CampoTexto);
                    }
                    ?>
                    <div id="formularioParseado" style="width: 100%">
                        <h2><?= $FormularioRecuperado->getTitulo() ?></h2>
                        <?php if (!empty($FormularioRecuperado->getDescripcion())) { ?>
                            <h3 style="padding-bottom: 15px"><?= $FormularioRecuperado->getDescripcion() ?></h3>
                        <?php } ?>

                            <form action="procesarSolicitud.php" id="formulario" method="POST">
                            <?php
                            foreach ($FormularioRecuperado->getCampos() as $campoActual) {
                                if ($campoActual instanceof CampoTexto) {
                                    ?>
                                    <?= $campoActual->getCodigo(); ?>
                                    <?php
                                }
                            }
                            
                            $_SESSION['formulario'] = $FormularioRecuperado;
                            ?>
                            <br/>
                            <input name="emailReceptor" type="hidden" value="<?= $FormularioRecuperado->getEmailReceptor(); ?>">
                            <input type="submit" value="Enviar solicitud">
                        </form>
                    </div>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>;
    </body>
</html>

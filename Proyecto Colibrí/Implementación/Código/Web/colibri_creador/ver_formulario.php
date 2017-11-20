<!DOCTYPE html>

<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require_once '../lib/ControlAcceso.class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CONSULTAR);

include_once './Campos.class.php';
include_once './Formulario.class.php';
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title><?= Constantes::NOMBRE_SISTEMA; ?>: Vista del formulario</title>
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
                    <h2>VISTA DEL FORMULARIO</h2>
                    <p>
                        En esta página se obtiene el formulario de la base de datos y se muestran sus campos correspondientes.
                    </p>

                    <?php
                    /* function ordenarPorPosicion($campo1_, $campo2_) {
                      return $campo2_->getPosicion() - $campo1_->getPosicion();
                      } */

                    $idformulario = filter_input(INPUT_GET, "id");

                    $formularioRecibido = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO WHERE `idFormulario` = {$idformulario}");
                    $camposFormulario = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM CAMPO WHERE `idFormulario` = {$idformulario}");

                    $formularioRecibido = $formularioRecibido->fetch_assoc();

                    $FormularioRecuperado = new Formulario($formularioRecibido['fechaCreacion']);

                    $FormularioRecuperado->setTitulo($formularioRecibido['titulo']);
                    $FormularioRecuperado->setDescripcion($formularioRecibido['descripcion']);
                    $FormularioRecuperado->setEmailReceptor($formularioRecibido['emailReceptor']);
                    $FormularioRecuperado->setFechaInicio($formularioRecibido['fechaInicio']);
                    $FormularioRecuperado->setFechaFin($formularioRecibido['fechaFin']);
                    ?>

                    <p><u>Datos para el desarrollador:</u></p>
                    <p><strong>RESPONSABLE DEL FORMULARIO:</strong> ID DE USUARIO <?= $formularioRecibido['idCreador']; ?></p>
                    <p><strong>RESPUESTAS A:</strong> <?= $FormularioRecuperado->getEmailReceptor(); ?></p>
                    <hr>
                    <br/>

                    <?php
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

                    // $FormularioRecuperado->setCampos() = usort($FormularioRecuperado->getCampos(), "ordenarPorPosicion");
                    ?>

                    <div id="formularioParseado" style="text-align: center; width: 100%">
                        <h1><?= $FormularioRecuperado->getTitulo() ?></h1>
                        <h3 style="padding-bottom: 15px"><?= $FormularioRecuperado->getDescripcion() ?></h3>

                        <form id="formulario">

                            <?php
                            foreach ($FormularioRecuperado->getCampos() as $campoActual) {
                                if ($campoActual instanceof CampoTexto) {
                                    ?>

                                    <?= $campoActual->getCodigo(); ?>

                                    <?php
                                }
                            }
                            ?>
                            <br/>
                            <input disabled type="submit" value="Enviar solicitud :D">
                        </form>
                    </div>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>;
    </body>
</html>

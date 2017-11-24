<!DOCTYPE HTML>
<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include_once "../lib/ControlAcceso.class.php";
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CONSULTAR);

include_once "../modelo/Workflow.class.php";

$formulario = ObjetoDatos::getInstancia()->ejecutarQuery("SELECT * FROM FORMULARIO WHERE `idFormulario` =" . filter_input(INPUT_GET, 'id'))->fetch_assoc();

$WorkflowRoles = new WorkflowRoles();
?>
<html>
    <head>
        <title><?= Constantes::NOMBRE_SISTEMA ?>: Editar el formulario <?= $formulario['titulo'] ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link href="../gui/estilo.css" type="text/css" rel="stylesheet">
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/start/jquery-ui.css">
        <link href="./gui/formulario.css" rel="stylesheet">

        <script type="text/javascript">
            sessionStorage.clear();
            // sessionStorage.setItem("formularioNuevo", []);

            /*
             * Se evita que el usuario salga de la página sin confirmar que
             * está seguro de que quiere hacerlo. Con esto se evita que se
             * pierdan cambios que no se querían perder por accidente.
             */
            window.onbeforeunload = confirmarSalida;

            function confirmarSalida() {
                return "¿Está seguro de que quiere terminar? ¡SE PERDERÁN TODOS LOS CAMBIOS QUE HAYA REALIZADO!";
            }
        </script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="../colibri/colibri.js"></script>
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Crear un nuevo formulario</h3>
                    <p>A través de esta página usted podrá crear un nuevo formulario para el sistema Colibrí.</p>
                    <form action="procesar.php" id="formularioCreacion" method="post">
                        <span class="cabecera">¿Dónde enviamos las respuestas?:</span><br/>
                        <span class="texto">Ingrese una dirección de correo electrónico válida a la que desee recibir las respuestas al formulario que está creando.</span><br/>
                        <input autocomplete="on" class="campoFormularioPrincipal" name="destinatario" placeholder="Ingrese una dirección de correo-e válida" required type="email" value="<?= $formulario['emailReceptor'] ?>"/><br/><br/>

                        <span class="cabecera">Título del formulario:</span><br/>
                        <span class="texto">Ingrese un título corto pero descriptivo para el formulario.</span><br/>
                        <input autocomplete="off" autofocus class="campoFormularioPrincipal" name="titulo" required spellcheck="true" type="text" value="<?= $formulario['titulo'] ?>"/><br/><br/>

                        <span class="cabecera">Descripción del formulario:</span><br/>
                        <span class="texto">Rellenar esto no es obligatorio, pero si lo hace procure ser breve y preciso para que quienes vayan a leer el formulario puedan comprenderlo en pocas palabras.</span><br/>
                        <textarea class="campoFormularioPrincipal" maxlength="500" name="descripcion" placeholder="Se admiten hasta 500 caracteres." spellcheck="true" value="<?= $formulario['descripcion'] ?>"></textarea><br/><br/>

                        <span class="cabecera">Destinatarios del formulario:</span>
                        <span class="texto">Seleccione uno o más roles a los que estará dirigido el formulario. Aquellos usuarios con roles que no seleccione no serán capaces de ver el formulario.</span>
                        <select class="campoFormularioPrincipal" multiple name="rolesDestino" required style="height: 100px" title="Puede seleccionar uno o más roles. Mantenga presionada la tecla control (Ctrl) mientras hace clic para realizar una selección múltiple">
                            <?php foreach ($WorkflowRoles->getRoles() as $WorkflowRol) { ?>
                                <option value="<?= $WorkflowRol->getIdRol() ?>"><?= $WorkflowRol->getNombre() ?></option>
                            <?php } ?>
                        </select><br/><br/>

                        <span class="cabecera">Fechas límite:</span><br/>
                        <span class="texto">Si lo desea puede establecer una fecha a partir de la cual el formulario estará disponible para ser rellenado, así como también puede establecer una fecha en la que se dejen de aceptar respuestas. Esto es opcional, por lo que también puede elegir que el formulario esté disponible hasta que se deshabilite manualmente.</span><br/>
                        <span class="texto" style="font-style: italic">Abierto desde:</span><br/>
                        <input autocomplete="off" class="campoFormularioPrincipal" id="fechaApertura" name="fechaInicio" placeholder="Haga clic aquí para abrir el calendario" readonly type="date" value="<?= $formulario['fechaInicio'] ?>"><br/><br/>
                        <span class="texto" style="font-style: italic">Abierto hasta:</span><br/>
                        <input autocomplete="off" class="campoFormularioPrincipal" id="fechaCierre" name="fechaFin" placeholder="Haga clic aquí para abrir el calendario" readonly type="date" value="<?= $formulario['fechaFin'] ?>"><br/><br/>

                        <table class="editorFormulario">
                            <tr>
                                <td class="editorFormulario" style="visibility: visible; width: 71%">
                                    <table id="vistaPrevia" style="text-align: center; width: 96%">
                                        <tr>
                                            <th style="text-align: center">Posición</th>
                                            <th style="text-align: center">Título</th>
                                            <th style="text-align: center">¿Obligatorio?</th>
                                            <th style="text-align: center">Acciones</th>
                                        </tr>
                                    </table>
                                </td>

                                <td class="editorFormulario" style="width: 29%">
                                    <span class="cabecera" style="color: #00466e; font-size: 17px; padding-bottom: 18px">Campos disponibles</span><br/>

                                    <input class="campoExhibicion" disabled type="text" value="Campo de texto" style="cursor: help" title="Un campo de texto. Para escribir una sola línea."> <img id="agregarCampoTexto" src="../imagenes/creador_agregar_campo.png" style="cursor:pointer" title="Haga clic aquí para agregar un nuevo campo de texto"/><br/>
                                    <!-- Div que contiene el editor de campo de texto !-->
                                    <div class="edicionCampo" id="edicionCampoTexto" style="box-sizing: border-box">
                                        <span class="cabecera" style="cursor: help" title="El título del campo de texto es la cabecera que va antes de este. Tómelo como el NOMBRE del campo.">Título<span style="color: red">*</span>:</span><br/>
                                        <input class="campoExhibicion" id="tituloCampoTexto" maxlength="20" placeholder="Se admiten hasta 20 caracteres" style="width: 100%"><br/>

                                        <span class="cabecera" style="cursor: help" title="La descripción no siempre es necesaria, pero en el caso de que lo fuese, sirve para describir al campo en pocas palabras.">Descripción:</span><br/>
                                        <textarea class="campoExhibicion" id="descripcionCampoTexto" maxlength="100" placeholder="Se admiten hasta 100 caracteres" style="width: 100%"></textarea><br/>

                                        <div><input id="obligatorioCampoTexto" name="esObligatorio" style="vertical-align: middle" type="checkbox" value="Sí"> <span class="cabecera" style="cursor: help" title="Elija si el campo va a ser o no requerido para enviar el formulario">Campo obligatorio</span></div>

                                        <span class="cabecera" style="cursor: help; margin-top: 10px" title="Es el texto que se muestra dentro del campo antes de que se escriba algo en él. Procure escribir pistas breves.">Pista:</span><br/>
                                        <input class="campoExhibicion" id="pistaCampoTexto" maxlength="50" placeholder="Se admiten hasta 50 caracteres" style="width: 100%" type="text"><br/>

                                        <input id="guardarEdicionCampoTexto" style="box-sizing: border-box; width: 100%" type="button" value="💾 Guardar este campo">
                                        <input disabled id="cancelarEdicionCampoTexto" style="box-sizing: border-box; width: 100%" type="button" value="🚮 Descartar cambios">
                                    </div>

                                    <textarea class="campoExhibicion" disabled style="cursor: help" title="Un área de texto. Útil cuando el usuario tiene que escribir mucho acerca de algo.">Área de texto</textarea> <img id="agregarAreaTexto" src="../imagenes/creador_agregar_campo.png" style="cursor:pointer" title="Haga clic aquí para agregar una nueva área de texto"/><br/><br/>

                                    <select class="campoExhibicion" disabled style="cursor: help" title="Una lista desplegable. Contiene un número de opciones de las cuales sólo se puede seleccionar una.">
                                        <option selected>Lista de opciones</option>
                                    </select> <img id="agregarListaDesplegable" src="../imagenes/creador_agregar_campo.png" style="cursor:pointer" title="Haga clic aquí para agregar una nueva lista desplegable"/><br/><br/>
                                    <br/>
                                </td>
                            </tr>
                        </table><br/>

                        <fieldset id="camposCreados" style="display: none"></fieldset>

                        <input class="campoFormularioPrincipal" type="submit" value="✔ Listo">
                    </form>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.class.php'; ?>;
    </body>
</html>

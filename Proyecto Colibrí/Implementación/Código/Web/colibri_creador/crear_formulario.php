<!DOCTYPE HTML>

<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

$WorkflowRoles = new WorkflowRoles();

date_default_timezone_set("America/Argentina/Rio_Gallegos");
?>
<html>
    <head>
        <title><?= Constantes::NOMBRE_SISTEMA ?>: Crear un nuevo formulario</title>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet">
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet">
        <link href="./gui/formulario.css" rel="stylesheet">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/start/jquery-ui.css">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <script src="are-you-sure.js"></script>
        <script type="text/javascript">
            sessionStorage.clear();
            sessionStorage.setItem("formularioNuevo", []);

            $(function () {
                $('#formularioCreacion').areYouSure(
                        {
                            message: '¿Está seguro de que desea salir del editor de formularios? Se perderán todos los cambios que haya hecho.'
                        }
                );
            });
        </script>
        <script src="../colibri/colibri.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
                        <input autocomplete="on" class="campoFormularioPrincipal" name="destinatario" placeholder="Ingrese una dirección de correo-e válida" required type="email"/><br/><br/>

                        <span class="cabecera">Título del formulario:</span><br/>
                        <span class="texto">Ingrese un título corto pero descriptivo para el formulario.</span><br/>
                        <input autocomplete="off" autofocus class="campoFormularioPrincipal" name="titulo" required type="text"/><br/><br/>

                        <span class="cabecera">Descripción del formulario:</span><br/>
                        <span class="texto">Rellenar esto no es obligatorio, pero si lo hace procure ser breve y preciso para que quienes vayan a leer el formulario puedan comprenderlo en pocas palabras.</span><br/>
                        <textarea class="campoFormularioPrincipal" maxlength="500" name="descripcion" placeholder="Se admiten hasta 500 caracteres."></textarea><br/><br/>

                        <span class="cabecera">Destinatarios del formulario:</span>
                        <span class="texto">Seleccione uno o más roles a los que estará dirigido el formulario. Aquellos usuarios con roles que no seleccione no serán capaces de ver el formulario.</span>
                        <select multiple name="rolesDestinatarios" required title="Puede seleccionar uno o más roles. Mantenga presionada la tecla control (Ctrl) mientras hace clic para realizar una selección múltiple">
                            <?php foreach ($WorkflowRoles as $WorkflowRol) { ?>
                                <option value="<?= $WorkflowRol . getIdRol() ?>"><?= $WorkflowRol . getNombre() ?></option>
                            <?php } ?>
                        </select>

                        <span class="cabecera">Fechas límite:</span><br/>
                        <span class="texto">Si lo desea puede establecer una fecha a partir de la cual el formulario estará disponible para ser rellenado, así como también puede establecer una fecha en la que se dejen de aceptar respuestas. Esto es opcional, por lo que también puede elegir que el formulario esté disponible hasta que se deshabilite manualmente.</span><br/>
                        <span class="texto" style="font-style: italic">Abierto desde:</span><br/>
                        <input autocomplete="off" class="campoFormularioPrincipal" id="fechaApertura" placeholder="Haga clic aquí para abrir el calendario" readonly type="date"><br/><br/>
                        <span class="texto" style="font-style: italic">Abierto hasta:</span><br/>
                        <input autocomplete="off" class="campoFormularioPrincipal" id="fechaCierre" placeholder="Haga clic aquí para abrir el calendario" readonly type="date"><br/><br/>

                        <table class="editorFormulario">
                            <tr>
                                <td class="editorFormulario" style="text-align: center; width: 60%">
                                    <table id="vistaPrevia" style="width: 97%">
                                        <tr>
                                            <th style="text-align: center">Posición</th>
                                            <th style="text-align: center">Título</th>
                                            <th style="text-align: center">Obligatorio</th>
                                            <th style="text-align: center">Acciones</th>
                                        </tr>
                                    </table>
                                </td>

                                <td class="editorFormulario" style="width: 40%">
                                    <h3>Campos disponibles</h3>

                                    <input class="campoExhibicion" disabled type="text" value="Campo de texto" style="cursor: help" title="Un campo de texto. Para escribir una sola línea."> <img id="agregarCampoTexto" src="../imagenes/creador_agregar_campo.png" style="cursor:pointer" title="Haga clic aquí para agregar un nuevo campo de texto"/><br/><br/>
                                    <!-- Div que contiene el editor de campo de texto !-->
                                    <div class="edicionCampo" id="edicionCampoTexto">
                                        <span class="cabecera" style="cursor: help" title="El título del campo de texto es la cabecera que va antes de este. Tómelo como el NOMBRE del campo.">Título<span style="color: red">*</span>:</span><br/>
                                        <input class="campoExhibicion" id="tituloCampoTexto" maxlength="20" placeholder="Se admiten hasta 20 caracteres" style="width: 100%"><br/>

                                        <span class="cabecera" style="cursor: help" title="La descripción no siempre es necesaria, pero en el caso de que lo fuese, sirve para describir al campo en pocas palabras.">Descripción:</span><br/>
                                        <textarea class="campoExhibicion" id="descripcionCampoTexto" maxlength="100" placeholder="Se admiten hasta 100 caracteres" style="width: 100%"></textarea><br/>

                                        <div><input id="obligatorioCampoTexto" name="esObligatorio" style="vertical-align: middle" type="checkbox" value="Sí"> <span class="cabecera" style="cursor: help" title="Elija si el campo va a ser o no requerido para enviar el formulario">Obligatorio<span style="color: red">*</span></span></div>

                                        <span class="cabecera" style="cursor: help; margin-top: 10px" title="Es el texto que se muestra dentro del campo antes de que se escriba algo en él. Procure escribir pistas breves.">Pista:</span><br/>
                                        <input class="campoExhibicion" id="pistaCampoTexto" maxlength="50" placeholder="Se admiten hasta 50 caracteres" style="width: 100%" type="text"><br/>

                                        <input id="guardarEdicionCampoTexto" style="width: 100%" type="button" value="Agregar este campo">
                                    </div>

                                    <textarea class="campoExhibicion" disabled style="cursor: help" title="Un área de texto. Útil cuando el usuario tiene que escribir mucho acerca de algo.">Área de texto</textarea> <img id="agregarAreaTexto" src="../imagenes/creador_agregar_campo.png" style="cursor:pointer" title="Haga clic aquí para agregar una nueva área de texto"/><br/><br/>

                                    <select class="campoExhibicion" disabled style="cursor: help" title="Una lista desplegable. Contiene un número de opciones de las cuales sólo se puede seleccionar una.">
                                        <option selected>Lista de opciones</option>
                                    </select> <img id="agregarListaDesplegable" src="../imagenes/creador_agregar_campo.png" style="cursor:pointer" title="Haga clic aquí para agregar una nueva lista desplegable"/><br/><br/>
                                    <br/>
                                </td>
                            </tr>
                        </table><br/>
                        <input id="botonGuardar" type="button" value="Guardar cambios"> <input disabled type="submit" value="Siguiente »">
                    </form>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>

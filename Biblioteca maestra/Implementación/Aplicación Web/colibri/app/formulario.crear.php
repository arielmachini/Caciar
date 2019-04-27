<!DOCTYPE html>

<?php
include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

if (preg_match('/MSIE\s(?P<v>\d+)/i', filter_input(INPUT_SERVER, "HTTP_USER_AGENT"), $B) && $B['v'] <= 8) {
    echo "Tiene que actualizar su navegador para poder acceder a esta página. Disculpe las molestias.";

    die();
}

include_once '../modelo/ColeccionRoles.php';
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
        <link rel="stylesheet" href="../lib/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

        <!-- Scripts requeridos por el sistema Colibrí -->
        <script type="text/javascript">
            /* Se borra cualquier progreso que haya quedado guardado. */
            sessionStorage.clear();
        </script>
        <script type="text/javascript" src="../lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Crear formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container" style="min-width: 800px;">
            <div class="card">
                <div class="card-header">
                    <h3>Crear formulario</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Atención:</strong> Todos los campos acompañados por un asterisco (<span style="color: red; font-weight: bold;">*</span>) son obligatorios. Si no los rellena no podrá crear el formulario.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="alert alert-danger fade show" id="errorSinCampos" role="alert" style="display: none;">
                        <strong>Error:</strong> Debe agregar al menos un campo a su formulario.
                    </div>

                    <form action="formulario.crear.procesar.php" id="crearFormulario" method="post" novalidate>
                        <p for="destinatarioFormulario" class="campo-cabecera">Dirección de e-mail que recibirá las respuestas<span style="color: red; font-weight: bold;">*</span></p>
                        <div>
                            <p class="campo-descripcion">¿Qué dirección de e-mail debería recibir las respuestas al formulario que está creando?</p>
                            <input autocomplete="on" class="form-control form-control-lg" id="destinatarioFormulario" maxlength="200" name="destinatarioFormulario" required type="email"/>
                            <div class="invalid-feedback">
                                <span class="oi oi-circle-x"></span> La dirección de e-mail que ingresó no es válida.
                            </div>
                        </div>
                        <br/>

                        <div>
                            <p class="campo-cabecera" for="tituloFormulario">Título del formulario<span style="color: red; font-weight: bold;">*</span></p>
                            <p class="campo-descripcion">Ingrese un título corto (pero descriptivo) para el formulario.</p>
                            <input autocomplete="off" autofocus class="form-control" id="tituloFormulario" maxlength="40" name="tituloFormulario" required spellcheck="true" type="text"/>
                            <div class="invalid-feedback">
                                <span class="oi oi-circle-x"></span> No escribió un título para el formulario.
                            </div>
                        </div>
                        <br/>

                        <p class="campo-cabecera">Descripción del formulario</p>
                        <p class="campo-descripcion">Una descripción concisa, que facilite la comprensión de su formulario.</p>
                        <textarea class="form-control" id="descripcionFormulario" maxlength="400" name="descripcionFormulario" placeholder="Puede escribir una descripción de hasta 400 caracteres." spellcheck="true" style="max-height: 120px; min-height: 60px;"></textarea>
                        <br/>

                        <div>
                            <p class="campo-cabecera" for="rolesDestinoFormulario">Destinatarios del formulario<span style="color: red; font-weight: bold;">*</span></p>
                            <p class="campo-descripcion">Seleccione uno o más roles a los que estará dirigido su formulario. Aquellos usuarios con roles que no seleccione no podrán acceder al formulario.<br/><strong>Consejo:</strong> Mantenga presionada la tecla control (Ctrl) mientras esté haciendo clic para realizar una selección múltiple.</p>
                            <select class="form-control" id="rolesDestinoFormulario" multiple="multiple" name="rolesDestinoFormulario[]" required style="max-height: 150px;" title="Puede seleccionar uno o más roles. Mantenga presionada la tecla control (Ctrl) mientras esté haciendo clic para realizar una selección múltiple.">
                                <option value="-1">Invitado</option>
                                <?php foreach ($ColeccionRoles->getRoles() as $Rol) { ?>
                                    <option value="<?= $Rol->getId() ?>"><?= $Rol->getNombre() ?></option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback">
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
                            <input autocomplete="off" class="form-control" id="fechaApertura" name="fechaAperturaFormulario" placeholder="Haga clic aquí para abrir el calendario" readonly style="background-color: white; cursor: pointer;" type="date"/>
                        </div>
                        <br/>

                        <p class="campo-descripcion" style="font-style: italic;">Abierto hasta:</p>
                        <button type="button" class="btn btn-outline-danger" id="borrarFechaCierre" style="float: right;" title="Haga clic aquí para borrar la fecha de cierre del formulario.">
                            <span class="oi oi-delete"></span>
                        </button>
                        <div style="overflow: hidden; padding-right: 5px;">
                            <input autocomplete="off" class="form-control" id="fechaCierre" name="fechaCierreFormulario" placeholder="Haga clic aquí para abrir el calendario" readonly style="background-color: white; cursor: pointer;" type="date"/>
                        </div>
                        <br/>

                        <hr/>

                        <p class="campo-cabecera">Campos de su formulario<span style="color: red; font-weight: bold;">*</span></p>
                        <p class="campo-descripcion">A través de la siguiente herramienta puede agregar y editar los campos que tendrá su formulario.<br/><strong>Consejo:</strong> Si no entiende para qué sirve un determinado campo, sitúe su cursor sobre el <span class="campo-tipo-ayuda oi oi-question-mark"></span> ubicado junto al nombre de dicho campo para visualizar una breve descripción sobre este.</p>
                        <table class="editor">
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
                                        <div class="div-editor" id="editorInicial">
                                            <span class="editor-cabecera">EDITOR DE CAMPOS</span>

                                            <span class="editor-propiedad-pista" style="padding-top: 10px;"><span class="oi oi-info" style="padding-right: 3px;"></span> Seleccione un campo del expositor para comenzar.</span>
                                        </div>

                                        <div class="div-editor oculto" id="editorCampoTexto">
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
                                                Guardar
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarCampoTexto" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-editor oculto" id="editorListaDesplegable">
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
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarOpcionLista" title="Haga clic aquí para eliminar la última opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesListaDesplegable">
                                                <input class="campo-editor" id="opcionNumero1" maxlength="40" placeholder="Opción 1" type="text"/>
                                            </fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 50.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarListaDesplegable" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarListaDesplegable" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-editor oculto" id="editorCampoFecha">
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
                                                Guardar
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarCampoFecha" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-editor oculto" id="editorCasillasVerificacion">
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
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarCasillaVerificacion" title="Haga clic aquí para eliminar la última opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesCasillasVerificacion">
                                                <input class="campo-editor" id="opcionNumero1" maxlength="40" placeholder="Casilla de verificación 1" type="text"/>
                                            </fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 20.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarCasillasVerificacion" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarCasillasVerificacion" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-editor oculto" id="editorAreaTexto">
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
                                                Guardar
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" id="descartarAreaTexto" style="width: 100%;" type="button">
                                                <span class="oi oi-x"></span>
                                                Descartar cambios
                                            </button>
                                        </div>

                                        <div class="div-editor oculto" id="editorBotonesRadio">
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
                                            <span class="editor-propiedad-cabecera" style="margin-bottom: 10px;">OPCIONES <div class="campo-error" id="errorOpcionesBotonesRadio" style="display: none;" title="Debe rellenar todas las opciones de la lista."><span class="oi oi-warning"></span> COMPLETAR</div></span>
                                            <div class="editor-opciones-botones" style="margin-bottom: 10px;">
                                                <button class="btn btn-sm btn-outline-primary" id="agregarBotonRadio" type="button"><span class="oi oi-plus"></span> Agregar</button>
                                                <button class="btn btn-sm btn-outline-secondary" id="eliminarBotonRadio" title="Haga clic aquí para eliminar la última opción de la lista." type="button"><span class="oi oi-trash"></span> Eliminar</button>
                                            </div>

                                            <fieldset class="opciones-lista" id="opcionesBotonesRadio">
                                                <input class="campo-editor" id="opcionNumero1" maxlength="40" placeholder="Botón de radio 1" type="text"/>
                                                <input class="campo-editor" id="opcionNumero2" maxlength="40" placeholder="Botón de radio 2" style="margin-top: 5px;" type="text"/>
                                            </fieldset>
                                            <span class="editor-propiedad-pista"><span class="oi oi-info" style="padding-right: 3px;"></span> Máximo de opciones: 20.</span>
                                            <hr/>

                                            <button class="btn btn-sm btn-outline-success" id="guardarBotonesRadio" style="margin-bottom: 10px; width: 100%;" type="button">
                                                <span class="oi oi-check"></span>
                                                Guardar
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
                        <br/>

                        <table class="previa-formulario oculto">
                            <tbody>
                                <tr>
                                    <td style="max-width: 300px;">
                                        <span class="editor-cabecera" style="margin-bottom: 17.5px;">VISTA PREVIA DEL FORMULARIO</span>
                                        <div id="vistaPreviaFormulario"></div>
                                    </td>

                                    <td style="min-width: 220px; padding-left: 15px; text-align: center; width: 220px;">
                                        <span class="editor-cabecera" style="margin-bottom: 17.5px; margin-top: 9px;">ACCIONES</span>
                                        <div id="botonesPreviaFormulario"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr/>

                        <fieldset id="camposCreados" style="display: none;"></fieldset>

                        <button class="btn btn-success" type="submit" value="Crear formulario">
                            <span class="oi oi-check"></span>
                            Crear formulario
                        </button>
                        <button class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea borrar el progreso realizado sobre el formulario? Esta acción no se puede deshacer.');" type="reset" value="Empezar de nuevo">
                            <span class="oi oi-trash"></span>
                            Empezar de nuevo
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


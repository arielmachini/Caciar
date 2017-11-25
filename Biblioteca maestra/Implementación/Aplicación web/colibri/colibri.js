/* Funciones del sistema ABM de gestores de formularios */

function abrirDialogoBusquedaAlta() {
    var dialogo = window.open("alta_gestor_busqueda.php", "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=75, left=100, width=800, height=600");

    terminadorAMGestores(dialogo);
}

function abrirDialogoModificacion(idusuario) {
    var dialogo = window.open("modificacion_gestor.php?idusuario=" + idusuario, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=75, left=100, width=800, height=600");

    terminadorAMGestores(dialogo);
}

function revisarInputLimiteManual(input) {
    $(input).change(function () {
        if ($(this).val() > parseInt($(this).attr("max"))) {
            $(this).val(parseInt($(this).attr("max")));
        } else if ($(this).val() < parseInt($(this).attr("min"))) {
            $(this).val(parseInt($(this).attr("min")));
        }
    });
}

function terminadorAMGestores(dialogo) {
    var manejadorDialogo = setInterval(function () {
        if (dialogo.closed) {
            window.location.reload();
            clearInterval(manejadorDialogo);
        }
    }, 100);
}

/* Funciones del creador de formularios */

$(document).ready(function () {
    /* Funciones relacionadas con agregar campos de texto */
    $("#agregarCampoTexto").click(function () {
        $("#edicionCampoTexto").slideDown(700, "linear");
    });
    
    /* Al presionar el botón para guardar el campo de texto... */
    $("#guardarEdicionCampoTexto").click(function () {
        /*
         * La variable "id" de sessionStorage se utiliza dentro de este
         * contexto, principalmente para mostrar el índice en la tabla y para
         * asignar en algunos nombres de atributo. También es utilizada más
         * adelante, aunque indirectamente, en el archivo PHP encargado de
         * procesar la información enviada a través del formulario.
         */
        if (sessionStorage.getItem("id") !== null) {
            sessionStorage.setItem("id", Number(sessionStorage.getItem("id")) + 1);
        } else {
            sessionStorage.setItem("id", 1);
        }
        
        /* Es obligatorio poner un título para el campo */
        if ($.trim($("#tituloCampoTexto").val()) === "") {
            alert("Por favor, rellene los campos requeridos antes de guardar este campo de texto.");
        } else {
            /*var camposFormulario;
            
            if (sessionStorage.getItem("formularioNuevo") === null || sessionStorage.getItem("formularioNuevo") === "") {
                camposFormulario = [];
            } else {
                camposFormulario = JSON.parse(sessionStorage.getItem("formularioNuevo"));
            }*/
            
            var campoNuevo;
            
            if ($("#obligatorioCampoTexto").is(':checked')) { /* Si es obligatorio... */
                /*
                 * Se genera un objeto para guardar las propiedades del campo
                 * recién generado y se lo convierte en un string utilizando
                 * JSON para, posteriormente, ser procesado con PHP.
                 * Lo mismo aplica para el caso dentro del 'else'.
                 */
                campoNuevo = {tipoCampo: "CampoTexto", descripcion: $("#descripcionCampoTexto").val(), obligatorio: 1, pista: $("#pistaCampoTexto").val(), titulo: $("#tituloCampoTexto").val()};
                campoNuevo = JSON.stringify(campoNuevo);
                
                /*
                 * Se crea un campo oculto para cada campo nuevo de manera que
                 * se pueda enviar a través del formulario mediante POST.
                 * Lo mismo aplica para el caso dentro del 'else'.
                 */
                $("#camposCreados").append("<input name=\"campoId" + sessionStorage.getItem("id") + "\" type=\"hidden\" value=\"\">");
                $("input[name=campoId" + sessionStorage.getItem("id") + "]").val(campoNuevo);
                
                /*
                 * Se "agrega el campo" a la tabla de previsualización del
                 * nuevo formulario.
                 * Lo mismo aplica para el caso dentro del 'else'.
                 */
                $("#vistaPrevia").append("<tr><td>" + sessionStorage.getItem("id") + "</td><td><span style=\"font-weight: 600\">" + $("#tituloCampoTexto").val() + "</span></td><td><span style=\"color: red; font-weight: 600\">Sí</span></td><td><img onclick=\"subirCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_arriba.png\" style=\"cursor:pointer\" title=\"Subir este campo\"/> <img id=\"bajarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_abajo.png\" style=\"cursor:pointer\" title=\"Bajar este campo\"/><br/><img onclick=\"editarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_editar.png\" style=\"cursor:pointer\" title=\"Editar este campo\"/> <img onclick=\"borrarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_revocar_permisos.png\" style=\"cursor:pointer\" title=\"Eliminar este campo\"/></td></tr>");
            } else { /* Si no es obligatorio... */
                campoNuevo = {tipoCampo: "CampoTexto", descripcion: $("#descripcionCampoTexto").val(), obligatorio: 0, pista: $("#pistaCampoTexto").val(), titulo: $("#tituloCampoTexto").val()};
                campoNuevo = JSON.stringify(campoNuevo);
                
                $("#camposCreados").append("<input name=\"campoId" + sessionStorage.getItem("id") + "\" type=\"hidden\" value=\"\">");
                $("input[name=campoId" + sessionStorage.getItem("id") + "]").val(campoNuevo);
                
                $("#vistaPrevia").append("<tr><td>" + sessionStorage.getItem("id") + "</td><td><span style=\"font-weight: 600\">" + $("#tituloCampoTexto").val() + "</span></td><td>No</td><td><img onclick=\"subirCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_arriba.png\" style=\"cursor:pointer\" title=\"Subir este campo\"/> <img id=\"bajarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_abajo.png\" style=\"cursor:pointer\" title=\"Bajar este campo\"/><br/><img onclick=\"editarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_editar.png\" style=\"cursor:pointer\" title=\"Editar este campo\"/> <img onclick=\"borrarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_revocar_permisos.png\" style=\"cursor:pointer\" title=\"Eliminar este campo\"/></td></tr>");
            }
            
            /* camposFormulario.push(campoNuevo);
            sessionStorage.setItem("formularioNuevo", JSON.stringify(camposFormulario));
            
            alert(sessionStorage.getItem("formularioNuevo")); */
        }
    });
});

$(document).ready(function () {
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar selector',
        prevText: 'Mes anterior',
        nextText: 'Siguiente mes',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    $.datepicker.setDefaults($.datepicker.regional[ "es" ]);

    $("#fechaApertura").datepicker({minDate: 0});
    $("#fechaCierre").datepicker({minDate: 1});

    $("#fechaApertura").on("change", function () {
        var fechaCierreMinima = $("#fechaApertura").datepicker("getDate", "+1d");
        
        fechaCierreMinima.setDate(fechaCierreMinima.getDate() + 1);
        
        $('#fechaCierre').datepicker('option', 'minDate', fechaCierreMinima);
        $("#fechaCierre").datepicker("refresh");
    });
});
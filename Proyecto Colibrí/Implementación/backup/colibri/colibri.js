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
    /* Guardado del formulario en la variable $_SESSION */
    $("#botonGuardar").click(function () {
        $.post("procesar.php", JSON.stringify(sessionStorage.getItem("formularioNuevo")));
    });
    
    /* Funciones relacionadas con agregar campos de texto */    
    $("#agregarCampoTexto").click(function () {
        $("#edicionCampoTexto").slideDown(400, "swing");
    });
    
    $("#guardarEdicionCampoTexto").click(function () {
        if (sessionStorage.getItem("id") !== null) {
            sessionStorage.setItem("id", Number(sessionStorage.getItem("id")) + 1);
        } else {
            sessionStorage.setItem("id", 1);
        }
        
        if ($.trim($("#tituloCampoTexto").val().replace(/ /g, '_')) === "") {
            alert("Por favor, rellene los campos requeridos antes de agregar el campo de texto.");
        } else {
            var camposFormulario;
            
            if (sessionStorage.getItem("formularioNuevo") === null || sessionStorage.getItem("formularioNuevo") === "") {
                camposFormulario = [];
            } else {
                camposFormulario = JSON.parse(sessionStorage.getItem("formularioNuevo"));
            }
            
            var campoNuevo;
            
            if ($("#obligatorioCampoTexto").is(':checked')) {    
                campoNuevo = {descripcion: $("#descripcionCampoTexto").val(), id: "id_" + $.trim($("#tituloCampoTexto").val().replace(/ /g, '_')), nombre: "nombre_" + $.trim($("#tituloCampoTexto").val().replace(/ /g, '_')), obligatorio: 1, titulo: $.trim($("#tituloCampoTexto").val().replace(/ /g, '_'))};
                
                $("#vistaPrevia").append("<tr><td>" + sessionStorage.getItem("id") + "</td><td><span style=\"font-weight: 600\">" + $("#tituloCampoTexto").val() + "</span></td><td><span style=\"color: red; font-weight: 600\">Sí</span></td><td><img onclick=\"subirCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_arriba.png\" style=\"cursor:pointer\" title=\"Subir este campo\"/> <img id=\"bajarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_abajo.png\" style=\"cursor:pointer\" title=\"Bajar este campo\"/> <img onclick=\"editarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_editar.png\" style=\"cursor:pointer\" title=\"Editar este campo\"/> <img onclick=\"borrarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_revocar_permisos.png\" style=\"cursor:pointer\" title=\"Eliminar este campo\"/></td></tr>");
            } else {
                campoNuevo = {descripcion: $("#descripcionCampoTexto").val(), id: "id_" + $.trim($("#tituloCampoTexto").val().replace(/ /g, '_')), nombre: "nombre_" + $.trim($("#tituloCampoTexto").val().replace(/ /g, '_')), obligatorio: 0, titulo: $.trim($("#tituloCampoTexto").val().replace(/ /g, '_'))};
                
                $("#vistaPrevia").append("<tr><td>" + sessionStorage.getItem("id") + "</td><td><span style=\"font-weight: 600\">" + $("#tituloCampoTexto").val() + "</span></td><td>No</td><td><img onclick=\"subirCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_arriba.png\" style=\"cursor:pointer\" title=\"Subir este campo\"/> <img id=\"bajarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/flecha_abajo.png\" style=\"cursor:pointer\" title=\"Bajar este campo\"/> <img onclick=\"editarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_editar.png\" style=\"cursor:pointer\" title=\"Editar este campo\"/> <img onclick=\"borrarCampo(\'" + sessionStorage.getItem("id") + "\')\" src=\"../imagenes/gestor_revocar_permisos.png\" style=\"cursor:pointer\" title=\"Eliminar este campo\"/></td></tr>");
            }
            
            camposFormulario.push(campoNuevo);
            sessionStorage.setItem("formularioNuevo", JSON.stringify(camposFormulario));
            
            alert(sessionStorage.getItem("formularioNuevo"));
        }
    });
});

$(document).ready(function () {
    $("#formularioCreacion").change(function () {
        $(":submit").prop("disabled", true);
    });

    $("#botonGuardar").click(function () {
        $(":submit").prop("disabled", false);
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
        dateFormat: 'yy/mm/dd',
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
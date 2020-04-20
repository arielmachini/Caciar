/**
 * Archivo: colibri.creador.js.
 * 
 * El código en este archivo corresponde a aquellas partes del sistema Colibrí
 * en las que se muestre un formulario.
 * 
 * @author Ariel Machini <arielmachini@pm.me>
 */

$(document).ready(function () {
    // "use strict"; Sólo descomentar esta línea para hacer debugging.

    /* Se establecen límites en la selección de fechas de apertura y cierre. */
    $('#fechaApertura').datepicker({minDate: 0});
    $('#fechaCierre').datepicker({minDate: 1});

    $('#fechaApertura').on('change', function () {
        var fechaCierreMinima = $('#fechaApertura').datepicker('getDate', '+1d');

        fechaCierreMinima.setDate(fechaCierreMinima.getDate() + 1);

        $('#fechaCierre').datepicker('option', 'minDate', fechaCierreMinima);
        $('#fechaCierre').datepicker('refresh');
    });

    /*
     * Se asigna funcionalidad a los botones para restablecer los campos de
     * fecha de apertura y fecha de cierre.
     */
    $('#borrarFechaApertura').click(function () {
        document.getElementById('fechaApertura').value = '';
    });

    $('#borrarFechaCierre').click(function () {
        document.getElementById('fechaCierre').value = '';
    });

    /*
     * Se deshabilita el selector de fecha predeterminado del navegador,
     * de manera que no interfiera con el selector de fecha de jQuery.
     */
    $('input[type=date]').addClass('date').attr('type', 'text');

    /* Se habilitan los tooltips de jQuery. */
    $(document).tooltip({
        show: {
            effect: "fade",
            delay: 0,
            duration: 275
        }
    });

    /*
     * Se deshabilita la posibilidad de enviar el formulario con la tecla INTRO.
     * Esto se hace para que no se pueda enviar el formulario apretando dicha
     * tecla en la herramienta de edición de campos, que está contenida dentro
     * del formulario.
     */
    $(document).on('keypress', ':input:not([type=submit])', function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        }
    });
    
    $('#creacionDescartar').click(function () {
        $.confirm({
            icon: 'oi oi-signpost',
            title: 'Empezar de nuevo',
            content: '¿Desea descartar su progreso actual y empezar de nuevo? Esta acción <strong>no se puede deshacer</strong>.',
            animation: 'none',
            closeAnimation: 'none',
            theme: 'material',
            type: 'red',
            useBootstrap: false,
            buttons: {
                confirm: {
                    btnClass: 'btn-red',
                    text: 'Descartar mi progreso',
                    action: function () {
                        $('#crearFormulario').trigger('reset');
                        $('#vistaPreviaFormulario').empty();
                        $('#botonesPreviaFormulario').empty();
                        $('.previa-formulario').addClass('oculto');
                        $('#camposCreados').empty();
                        
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                    }
                },
                cancelar: {
                    text: 'Cancelar'
                }
            }
        });
    });
    
    $('#edicionCancelar').click(function () {
        $.confirm({
            icon: 'oi oi-signpost',
            title: 'Cancelar edición',
            content: '¿Desea descartar todos los cambios realizados sobre este formulario? Esta acción <strong>no se puede deshacer</strong>.',
            animation: 'none',
            closeAnimation: 'none',
            theme: 'material',
            type: 'orange',
            useBootstrap: false,
            buttons: {
                confirm: {
                    btnClass: 'btn-orange',
                    text: 'Descartar mis cambios',
                    action: function () {
                        window.location.replace('formulario.gestor.php');
                    }
                },
                seguirEditando: {
                    text: 'Seguir editando'
                }
            }
        });
    });
    
    $('#enviarFormulario').click(function () {
        descartarCampoTexto();
        descartarCampoFecha();
        descartarAreaTexto();
        descartarListaDesplegable();
        descartarCasillasVerificacion();
        descartarBotonesRadio();
        
        descartarEdicionCampoTexto();
        descartarEdicionCampoFecha();
        descartarEdicionAreaTexto();
        descartarEdicionListaDesplegable();
        descartarEdicionCasillasVerificacion();
        descartarEdicionBotonesRadio();
        
        $('#crearFormulario').submit();
    });

    /*
     * Se previene que la sesión expire (por inactividad) mientras el usuario
     * esté creando o modificando un formulario.
     * 
     * NOTA: Por defecto, una sesión en PHP expira al cumplirse 24 minutos de
     * inactividad.
     */
    var tiempoRefresco = 1200000; // Equivale a 20 minutos.

    window.setInterval(function () {
        $.ajax({
            cache: false,
            type: 'GET',
            url: '../lib/mantenerSesion.php',
            success: function (data) {}
        });
    }, tiempoRefresco);
});

/*
 * La siguiente lista de funciones pertenece a la herramienta de edición de
 * campos del sistema.
 */

/* * * * * * * * * *
 * CAMPO DE TEXTO  *
 * * * * * * * * * */
$('#nuevoCampoTexto').click(function () {
    $('.div-crear').removeClass('oculto').not('#editorCampoTexto').addClass('oculto');
    $('.nuevo-campo').addClass('seleccionado').not('#nuevoCampoTexto').removeClass('seleccionado');
    $('.editor-cabecera').addClass('animacion').not('#cabeceraCampoTexto').removeClass('animacion');
});

$('#guardarCampoTexto').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloCampoTexto').val()) === '') {
        $('#guardarCampoTexto').addClass('animacion-error');
        $('#errorTituloCampoTexto').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloCampoTexto').css('display', 'none');
    }

    if (!$('input[name=subtipoCampoTexto]:checked').val()) {
        $('#guardarCampoTexto').addClass('animacion-error');
        $('#errorSubtipoCampoTexto').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorSubtipoCampoTexto').css('display', 'none');
    }

    if (!hayErrores) {
        if (sessionStorage.getItem('id') !== null) {
            sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) + 1);
        } else {
            sessionStorage.setItem('id', 1);
        }

        $('#guardarCampoTexto').removeClass('animacion-error');
        $('.previa-formulario').fadeIn(650).removeClass('oculto');

        var campoTexto;

        if ($('#obligatorioCampoTexto').is(':checked')) {
            if ($('#campoTextoEmail').is(':checked')) {
                /* CAMPO DE TEXTO PARA DIRECCIONES DE E-MAIL (obligatorio). */
                campoTexto = {tipoCampo: "CampoEmail", titulo: $('#tituloCampoTexto').val(), descripcion: $('#descripcionCampoTexto').val(), obligatorio: 1, pista: $('#pistaCampoTexto').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTexto').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTexto').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTexto').val() + '\" type=\"email\"/></div>');
            } else if ($('#campoTextoNumerico').is(':checked')) {
                /* CAMPO DE TEXTO PARA VALORES NUMÉRICOS (obligatorio). */
                campoTexto = {tipoCampo: "CampoNumerico", titulo: $('#tituloCampoTexto').val(), descripcion: $('#descripcionCampoTexto').val(), obligatorio: 1, pista: $('#pistaCampoTexto').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTexto').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTexto').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTexto').val() + '\" type=\"number\"/></div>');
            } else {
                /* CAMPO DE TEXTO (obligatorio). */
                campoTexto = {tipoCampo: "CampoTexto", titulo: $('#tituloCampoTexto').val(), descripcion: $('#descripcionCampoTexto').val(), obligatorio: 1, pista: $('#pistaCampoTexto').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTexto').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTexto').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTexto').val() + '\" type=\"text\"/></div>');
            }
        } else {
            if ($('#campoTextoEmail').is(':checked')) {
                /* CAMPO DE TEXTO PARA DIRECCIONES DE E-MAIL (opcional). */
                campoTexto = {tipoCampo: "CampoEmail", titulo: $('#tituloCampoTexto').val(), descripcion: $('#descripcionCampoTexto').val(), obligatorio: 0, pista: $('#pistaCampoTexto').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTexto').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTexto').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTexto').val() + '\" type=\"email\"/></div>');
            } else if ($('#campoTextoNumerico').is(':checked')) {
                /* CAMPO DE TEXTO PARA VALORES NUMÉRICOS (opcional). */
                campoTexto = {tipoCampo: "CampoNumerico", titulo: $('#tituloCampoTexto').val(), descripcion: $('#descripcionCampoTexto').val(), obligatorio: 0, pista: $('#pistaCampoTexto').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTexto').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTexto').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTexto').val() + '\" type=\"number\"/></div>');
            } else {
                /* CAMPO DE TEXTO (opcional). */
                campoTexto = {tipoCampo: "CampoTexto", titulo: $('#tituloCampoTexto').val(), descripcion: $('#descripcionCampoTexto').val(), obligatorio: 0, pista: $('#pistaCampoTexto').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTexto').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTexto').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTexto').val() + '\" type=\"text\"/></div>');
            }
        }

        $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: flex-start; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center; padding-top: 10px;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición arriba.\" type=\"button\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición abajo.\" type=\"button\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Editar este campo.\" type=\"button\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1;\" title=\"Eliminar este campo del formulario.\" type=\"button\"><span class=\"oi oi-trash\"></span></button></div>');
        $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
        $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(campoTexto);

        desactivarBotonesMoverInnecesarios();
        descartarCampoTexto();
    }
});

$('#guardarEdicionCampoTexto').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloCampoTextoEdicion').val()) === '') {
        $('#guardarEdicionCampoTexto').addClass('animacion-error');
        $('#errorTituloCampoTextoEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloCampoTextoEdicion').css('display', 'none');
    }

    if (!$('input[name=subtipoCampoTextoEdicion]:checked').val()) {
        $('#guardarEdicionCampoTexto').addClass('animacion-error');
        $('#errorSubtipoCampoTextoEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorSubtipoCampoTextoEdicion').css('display', 'none');
    }

    if (!hayErrores) {
        var idCampoEditado = $('#idCampoEditado').val();

        $('#guardarEdicionCampoTexto').removeClass('animacion-error');

        var campoTexto;

        if ($('#obligatorioCampoTextoEdicion').is(':checked')) {
            if ($('#campoTextoEmailEdicion').is(':checked')) {
                /* CAMPO DE TEXTO PARA DIRECCIONES DE E-MAIL (obligatorio). */
                campoTexto = {tipoCampo: "CampoEmail", titulo: $('#tituloCampoTextoEdicion').val(), descripcion: $('#descripcionCampoTextoEdicion').val(), obligatorio: 1, pista: $('#pistaCampoTextoEdicion').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTextoEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTextoEdicion').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTextoEdicion').val() + '\" type=\"email\"/>');
            } else if ($('#campoTextoNumericoEdicion').is(':checked')) {
                /* CAMPO DE TEXTO PARA VALORES NUMÉRICOS (obligatorio). */
                campoTexto = {tipoCampo: "CampoNumerico", titulo: $('#tituloCampoTextoEdicion').val(), descripcion: $('#descripcionCampoTextoEdicion').val(), obligatorio: 1, pista: $('#pistaCampoTextoEdicion').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTextoEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTextoEdicion').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTextoEdicion').val() + '\" type=\"number\"/>');
            } else {
                /* CAMPO DE TEXTO (obligatorio). */
                campoTexto = {tipoCampo: "CampoTexto", titulo: $('#tituloCampoTextoEdicion').val(), descripcion: $('#descripcionCampoTextoEdicion').val(), obligatorio: 1, pista: $('#pistaCampoTextoEdicion').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTextoEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTextoEdicion').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTextoEdicion').val() + '\" type=\"text\"/>');
            }
        } else {
            if ($('#campoTextoEmailEdicion').is(':checked')) {
                /* CAMPO DE TEXTO PARA DIRECCIONES DE E-MAIL (opcional). */
                campoTexto = {tipoCampo: "CampoEmail", titulo: $('#tituloCampoTextoEdicion').val(), descripcion: $('#descripcionCampoTextoEdicion').val(), obligatorio: 0, pista: $('#pistaCampoTextoEdicion').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTextoEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTextoEdicion').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTextoEdicion').val() + '\" type=\"email\"/>');
            } else if ($('#campoTextoNumericoEdicion').is(':checked')) {
                /* CAMPO DE TEXTO PARA VALORES NUMÉRICOS (opcional). */
                campoTexto = {tipoCampo: "CampoNumerico", titulo: $('#tituloCampoTextoEdicion').val(), descripcion: $('#descripcionCampoTextoEdicion').val(), obligatorio: 0, pista: $('#pistaCampoTextoEdicion').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTextoEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTextoEdicion').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTextoEdicion').val() + '\" type=\"number\"/>');
            } else {
                /* CAMPO DE TEXTO (opcional). */
                campoTexto = {tipoCampo: "CampoTexto", titulo: $('#tituloCampoTextoEdicion').val(), descripcion: $('#descripcionCampoTextoEdicion').val(), obligatorio: 0, pista: $('#pistaCampoTextoEdicion').val()};
                campoTexto = JSON.stringify(campoTexto);

                $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoTextoEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoTextoEdicion').val() + '</p><input class=\"campo-editor\" disabled placeholder=\"' + $('#pistaCampoTextoEdicion').val() + '\" type=\"text\"/>');
            }
        }

        $('#accionesCampoID' + idCampoEditado).css('height', $('#campoID' + idCampoEditado).height() + 11 + 'px');
        $('input[name=campoID' + idCampoEditado + ']').val(campoTexto);

        descartarEdicionCampoTexto();

        $('html, body').animate({
            scrollTop: $("#campoID" + idCampoEditado).offset().top
        }, 500);
    }
});

function descartarCampoTexto() {
    $('#tituloCampoTexto').val('');
    $('#descripcionCampoTexto').val('');
    $('#obligatorioCampoTexto').prop('checked', false);
    $('#pistaCampoTexto').val('');
    $('#campoTextoEmail').prop('checked', false);
    $('#campoTextoNumerico').prop('checked', false);
    $('#campoTextoTexto').prop('checked', false);
}

$('#descartarCampoTexto').click(function () {
    descartarCampoTexto();
});

function descartarEdicionCampoTexto() {
    $('#tituloCampoTextoEdicion').val('');
    $('#descripcionCampoTextoEdicion').val('');
    $('#obligatorioCampoTextoEdicion').prop('checked', false);
    $('#pistaCampoTextoEdicion').val('');
    $('#campoTextoEmailEdicion').prop('checked', false);
    $('#campoTextoNumericoEdicion').prop('checked', false);
    $('#campoTextoTextoEdicion').prop('checked', false);
    $('#idCampoEditado').val('');

    $('.editor').removeClass('oculto').not('#editorCamposCrear').addClass('oculto');

    /* Se reactivan los botones de acción que se desactivaron durante la edición. */
    reactivarBotonesAccion();
}

$('#descartarEdicionCampoTexto').click(function () {
    descartarEdicionCampoTexto();
});

/* * * * * * * * * * *
 * LISTA DESPLEGABLE *
 * * * * * * * * * * */

$('#nuevaListaDesplegable').click(function () {
    $('.div-crear').removeClass('oculto').not('#editorListaDesplegable').addClass('oculto');
    $('.nuevo-campo').addClass('seleccionado').not('#nuevaListaDesplegable').removeClass('seleccionado');
    $('.editor-cabecera').addClass('animacion').not('#cabeceraListaDesplegable').removeClass('animacion');
});

/* Funciones para agregar y eliminar elementos de la lista desplegable. */

$('#agregarOpcionLista').click(function () {
    /* Se obtiene el ID numérico del último campo. */
    var idOpcionNueva = parseInt($('#opcionesListaDesplegable input:last').attr('id').substring(12)) + 1;

    if (idOpcionNueva <= 100) {
        var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Opción ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');

        $('#opcionesListaDesplegable').append(opcionNueva);
        $('#opcionesListaDesplegable').scrollTop($('#opcionesListaDesplegable')[0].scrollHeight);
    }
});

$('#eliminarOpcionLista').click(function () {
    var identificadorPrimeraOpcion = $('#opcionesListaDesplegable input:first').attr('id');
    var identificadorUltimaOpcion = $('#opcionesListaDesplegable input:last').attr('id');

    if (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
        var ultimaOpcion = $('#opcionesListaDesplegable input:last');

        ultimaOpcion.remove();
    }
});

$('#agregarOpcionListaEdicion').click(function () {
    /* Se obtiene el ID numérico del último campo. */
    var idOpcionNueva = parseInt($('#opcionesListaDesplegableEdicion input:last').attr('id').substring(12)) + 1;

    if (idOpcionNueva <= 100) {
        var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Opción ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');

        $('#opcionesListaDesplegableEdicion').append(opcionNueva);
        $('#opcionesListaDesplegableEdicion').scrollTop($('#opcionesListaDesplegableEdicion')[0].scrollHeight);
    }
});

$('#eliminarOpcionListaEdicion').click(function () {
    var identificadorPrimeraOpcion = $('#opcionesListaDesplegableEdicion input:first').attr('id');
    var identificadorUltimaOpcion = $('#opcionesListaDesplegableEdicion input:last').attr('id');

    if (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
        var ultimaOpcion = $('#opcionesListaDesplegableEdicion input:last');

        ultimaOpcion.remove();
    }
});

$('#guardarListaDesplegable').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloListaDesplegable').val()) === '') {
        $('#guardarListaDesplegable').addClass('animacion-error');
        $('#errorTituloListaDesplegable').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloListaDesplegable').css('display', 'none');
    }

    var opciones = document.getElementById('opcionesListaDesplegable').getElementsByTagName('input');
    opciones = Array.from(opciones);

    for (var i = 0; i < opciones.length; i++) {
        if ($.trim(opciones[i].value) === '') {
            $('#guardarListaDesplegable').addClass('animacion-error');
            $('#errorOpcionesListaDesplegable').fadeIn(300).css('display', 'inline-block');

            hayErrores = true;

            break;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorOpcionesListaDesplegable').css('display', 'none');
        }
    }

    if (!hayErrores) {
        if (sessionStorage.getItem('id') !== null) {
            sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) + 1);
        } else {
            sessionStorage.setItem('id', 1);
        }

        $('#guardarListaDesplegable').removeClass('animacion-error');
        $('.previa-formulario').fadeIn(650).removeClass('oculto');

        var listaDesplegable;
        var listaOpciones = '';

        if ($('#obligatorioListaDesplegable').is(':checked')) {
            listaDesplegable = {tipoCampo: "ListaDesplegable", titulo: $('#tituloListaDesplegable').val(), descripcion: $('#descripcionListaDesplegable').val(), obligatorio: 1, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaDesplegable.opciones.indexOf(opcion) === -1) { // Mediante esta comprobación se omiten valores duplicados.
                    listaOpciones = listaOpciones + '<option value=\"' + opcion + '\">' + opcion + '</option>';
                    listaDesplegable.opciones.push(opcion);
                }
            }

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloListaDesplegable').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionListaDesplegable').val() + '</p><select class=\"campo-editor\" disabled>' + listaOpciones + '</select></div>');
        } else {
            listaDesplegable = {tipoCampo: "ListaDesplegable", titulo: $('#tituloListaDesplegable').val(), descripcion: $('#descripcionListaDesplegable').val(), obligatorio: 0, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaDesplegable.opciones.indexOf(opcion) === -1) {
                    listaOpciones = listaOpciones + '<option value=\"' + opcion + '\">' + opcion + '</option>';
                    listaDesplegable.opciones.push(opcion);
                }
            }

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloListaDesplegable').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionListaDesplegable').val() + '</p><select class=\"campo-editor\" disabled>' + listaOpciones + '</select></div>');
        }

        listaDesplegable = JSON.stringify(listaDesplegable);

        $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: flex-start; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center; padding-top: 10px;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición arriba.\" type=\"button\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición abajo.\" type=\"button\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Editar este campo.\" type=\"button\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1;\" title=\"Eliminar este campo del formulario.\" type=\"button\"><span class=\"oi oi-trash\"></span></button></div>');

        $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
        $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(listaDesplegable);

        desactivarBotonesMoverInnecesarios();
        descartarListaDesplegable();
    }
});

$('#guardarEdicionListaDesplegable').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloListaDesplegableEdicion').val()) === '') {
        $('#guardarEdicionListaDesplegable').addClass('animacion-error');
        $('#errorTituloListaDesplegableEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloListaDesplegableEdicion').css('display', 'none');
    }

    var opciones = document.getElementById('opcionesListaDesplegableEdicion').getElementsByTagName('input');
    opciones = Array.from(opciones);

    for (var i = 0; i < opciones.length; i++) {
        if ($.trim(opciones[i].value) === '') {
            $('#guardarEdicionListaDesplegable').addClass('animacion-error');
            $('#errorOpcionesListaDesplegableEdicion').fadeIn(300).css('display', 'inline-block');

            hayErrores = true;

            break;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorOpcionesListaDesplegableEdicion').css('display', 'none');
        }
    }

    if (!hayErrores) {
        var idCampoEditado = $('#idCampoEditado').val();

        $('#guardarEdicionListaDesplegable').removeClass('animacion-error');

        var listaDesplegable;
        var listaOpciones = '';

        if ($('#obligatorioListaDesplegableEdicion').is(':checked')) {
            listaDesplegable = {tipoCampo: "ListaDesplegable", titulo: $('#tituloListaDesplegableEdicion').val(), descripcion: $('#descripcionListaDesplegableEdicion').val(), obligatorio: 1, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaDesplegable.opciones.indexOf(opcion) === -1) { // Mediante esta comprobación se omiten valores duplicados.
                    listaOpciones = listaOpciones + '<option value=\"' + opcion + '\">' + opcion + '</option>';
                    listaDesplegable.opciones.push(opcion);
                }
            }

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloListaDesplegableEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionListaDesplegableEdicion').val() + '</p><select class=\"campo-editor\" disabled>' + listaOpciones + '</select>');
        } else {
            listaDesplegable = {tipoCampo: "ListaDesplegable", titulo: $('#tituloListaDesplegableEdicion').val(), descripcion: $('#descripcionListaDesplegableEdicion').val(), obligatorio: 0, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaDesplegable.opciones.indexOf(opcion) === -1) {
                    listaOpciones = listaOpciones + '<option value=\"' + opcion + '\">' + opcion + '</option>';
                    listaDesplegable.opciones.push(opcion);
                }
            }

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloListaDesplegableEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionListaDesplegableEdicion').val() + '</p><select class=\"campo-editor\" disabled>' + listaOpciones + '</select>');
        }

        listaDesplegable = JSON.stringify(listaDesplegable);

        $('#accionesCampoID' + idCampoEditado).css('height', $('#campoID' + idCampoEditado).height() + 11 + 'px');
        $('input[name=campoID' + idCampoEditado + ']').val(listaDesplegable);

        descartarEdicionListaDesplegable();

        $('html, body').animate({
            scrollTop: $("#campoID" + idCampoEditado).offset().top
        }, 500);
    }
});

function descartarListaDesplegable() {
    $('#tituloListaDesplegable').val('');
    $('#descripcionListaDesplegable').val('');
    $('#obligatorioListaDesplegable').prop('checked', false);

    var identificadorPrimeraOpcion = $('#opcionesListaDesplegable input:first').attr('id');
    var identificadorUltimaOpcion = $('#opcionesListaDesplegable input:last').attr('id');

    while (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
        $('#opcionesListaDesplegable input:last').remove();
        identificadorUltimaOpcion = $('#opcionesListaDesplegable input:last').attr('id');
    }

    $('#opcionesListaDesplegable input:first').val('');
}

$('#descartarListaDesplegable').click(function () {
    descartarListaDesplegable();
});

function descartarEdicionListaDesplegable() {
    $('#tituloListaDesplegableEdicion').val('');
    $('#descripcionListaDesplegableEdicion').val('');
    $('#obligatorioListaDesplegableEdicion').prop('checked', false);
    $('#opcionesListaDesplegableEdicion input').remove();
    $('#idCampoEditado').val('');

    $('.editor').removeClass('oculto').not('#editorCamposCrear').addClass('oculto');

    /* Se reactivan los botones de acción que se desactivaron durante la edición. */
    reactivarBotonesAccion();
}

$('#descartarEdicionListaDesplegable').click(function () {
    descartarEdicionListaDesplegable();
});

/* * * * * * * * * * *
 * SELECTOR DE FECHA *
 * * * * * * * * * * */

$('#nuevoCampoFecha').click(function () {
    $('.div-crear').removeClass('oculto').not('#editorCampoFecha').addClass('oculto');
    $('.nuevo-campo').addClass('seleccionado').not('#nuevoCampoFecha').removeClass('seleccionado');
    $('.editor-cabecera').addClass('animacion').not('#cabeceraCampoFecha').removeClass('animacion');
});

$('#guardarCampoFecha').click(function () {
    var campoFecha;

    if ($.trim($('#tituloCampoFecha').val()) === '') {
        $('#guardarCampoFecha').addClass('animacion-error');
        $('#errorTituloCampoFecha').fadeIn(300).css('display', 'inline-block');
    } else {
        if (sessionStorage.getItem('id') !== null) {
            sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) + 1);
        } else {
            sessionStorage.setItem('id', 1);
        }

        $('#guardarCampoFecha').removeClass('animacion-error');
        $('#errorTituloCampoFecha').css('display', 'none');
        $('.previa-formulario').fadeIn(650).removeClass('oculto');

        if ($('#obligatorioCampoFecha').is(':checked')) {
            campoFecha = {tipoCampo: "Fecha", titulo: $('#tituloCampoFecha').val(), descripcion: $('#descripcionCampoFecha').val(), obligatorio: 1};

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoFecha').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoFecha').val() + '</p><input class=\"campo-editor\" disabled type=\"date\"/></div>');
        } else {
            campoFecha = {tipoCampo: "Fecha", titulo: $('#tituloCampoFecha').val(), descripcion: $('#descripcionCampoFecha').val(), obligatorio: 0};

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoFecha').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoFecha').val() + '</p><input class=\"campo-editor\" disabled type=\"date\"/></div>');
        }

        campoFecha = JSON.stringify(campoFecha);

        $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: flex-start; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center; padding-top: 10px;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición arriba.\" type=\"button\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición abajo.\" type=\"button\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Editar este campo.\" type=\"button\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1;\" title=\"Eliminar este campo del formulario.\" type=\"button\"><span class=\"oi oi-trash\"></span></button></div>');

        $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
        $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(campoFecha);

        desactivarBotonesMoverInnecesarios();
        descartarCampoFecha();
    }
});

$('#guardarEdicionCampoFecha').click(function () {
    var campoFecha;

    if ($.trim($('#tituloCampoFechaEdicion').val()) === '') {
        $('#guardarEdicionCampoFecha').addClass('animacion-error');
        $('#errorTituloCampoFechaEdicion').fadeIn(300).css('display', 'inline-block');
    } else {
        var idCampoEditado = $('#idCampoEditado').val();

        $('#guardarEdicionCampoFecha').removeClass('animacion-error');
        $('#errorTituloCampoFechaEdicion').css('display', 'none');

        if ($('#obligatorioCampoFechaEdicion').is(':checked')) {
            campoFecha = {tipoCampo: "Fecha", titulo: $('#tituloCampoFechaEdicion').val(), descripcion: $('#descripcionCampoFechaEdicion').val(), obligatorio: 1};

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoFechaEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoFechaEdicion').val() + '</p><input class=\"campo-editor\" disabled type=\"date\"/>');
        } else {
            campoFecha = {tipoCampo: "Fecha", titulo: $('#tituloCampoFechaEdicion').val(), descripcion: $('#descripcionCampoFechaEdicion').val(), obligatorio: 0};

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCampoFechaEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCampoFechaEdicion').val() + '</p><input class=\"campo-editor\" disabled type=\"date\"/>');
        }

        campoFecha = JSON.stringify(campoFecha);

        $('#accionesCampoID' + idCampoEditado).css('height', $('#campoID' + idCampoEditado).height() + 11 + 'px');
        $('input[name=campoID' + idCampoEditado + ']').val(campoFecha);

        descartarEdicionCampoFecha();

        $('html, body').animate({
            scrollTop: $("#campoID" + idCampoEditado).offset().top
        }, 500);
    }
});

function descartarCampoFecha() {
    $('#tituloCampoFecha').val('');
    $('#descripcionCampoFecha').val('');
    $('#obligatorioCampoFecha').prop('checked', false);
}

$('#descartarCampoFecha').click(function () {
    descartarCampoFecha();
});

function descartarEdicionCampoFecha() {
    $('#tituloCampoFechaEdicion').val('');
    $('#descripcionCampoFechaEdicion').val('');
    $('#obligatorioCampoFechaEdicion').prop('checked', false);
    $('#idCampoEditado').val('');

    $('.editor').removeClass('oculto').not('#editorCamposCrear').addClass('oculto');

    /* Se reactivan los botones de acción que se desactivaron durante la edición. */
    reactivarBotonesAccion();
}

$('#descartarEdicionCampoFecha').click(function () {
    descartarEdicionCampoFecha();
});

/* * * * * * * * * * * * * * *
 * CASILLAS DE VERIFICACIÓN  *
 * * * * * * * * * * * * * * */

$('#nuevaListaVerificacion').click(function () {
    $('.div-crear').removeClass('oculto').not('#editorCasillasVerificacion').addClass('oculto');
    $('.nuevo-campo').addClass('seleccionado').not('#nuevaListaVerificacion').removeClass('seleccionado');
    $('.editor-cabecera').addClass('animacion').not('#cabeceraCasillasVerificacion').removeClass('animacion');
});

/* Funciones para agregar y eliminar elementos de la lista de verificación. */

$('#agregarCasillaVerificacion').click(function () {
    /* Se obtiene el ID numérico del último campo. */
    var idOpcionNueva = parseInt($('#opcionesCasillasVerificacion input:last').attr('id').substring(12)) + 1;

    if (idOpcionNueva <= 50) {
        var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Casilla de verificación ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');

        $('#opcionesCasillasVerificacion').append(opcionNueva);
        $('#opcionesCasillasVerificacion').scrollTop($('#opcionesCasillasVerificacion')[0].scrollHeight);
    }
});

$('#eliminarCasillaVerificacion').click(function () {
    var identificadorPrimeraOpcion = $('#opcionesCasillasVerificacion input:first').attr('id');
    var identificadorUltimaOpcion = $('#opcionesCasillasVerificacion input:last').attr('id');

    if (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
        var ultimaOpcion = $('#opcionesCasillasVerificacion input:last');

        ultimaOpcion.remove();
    }
});

$('#agregarCasillaVerificacionEdicion').click(function () {
    /* Se obtiene el ID numérico del último campo. */
    var idOpcionNueva = parseInt($('#opcionesCasillasVerificacionEdicion input:last').attr('id').substring(12)) + 1;

    if (idOpcionNueva <= 50) {
        var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Casilla de verificación ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');

        $('#opcionesCasillasVerificacionEdicion').append(opcionNueva);
        $('#opcionesCasillasVerificacionEdicion').scrollTop($('#opcionesCasillasVerificacionEdicion')[0].scrollHeight);
    }
});

$('#eliminarCasillaVerificacionEdicion').click(function () {
    var identificadorPrimeraOpcion = $('#opcionesCasillasVerificacionEdicion input:first').attr('id');
    var identificadorUltimaOpcion = $('#opcionesCasillasVerificacionEdicion input:last').attr('id');

    if (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
        var ultimaOpcion = $('#opcionesCasillasVerificacionEdicion input:last');

        ultimaOpcion.remove();
    }
});

$('#guardarCasillasVerificacion').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloCasillasVerificacion').val()) === '') {
        $('#guardarCasillasVerificacion').addClass('animacion-error');
        $('#errorTituloCasillasVerificacion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloCasillasVerificacion').css('display', 'none');
    }

    var opciones = document.getElementById('opcionesCasillasVerificacion').getElementsByTagName('input');
    opciones = Array.from(opciones);

    for (var i = 0; i < opciones.length; i++) {
        if (opciones[i].value.includes(';')) {
            $('#errorOpcionesCasillasVerificacion').css('display', 'none');
            
            $('#guardarCasillasVerificacion').addClass('animacion-error');
            $('#errorTextoOpcionesCasillasVerificacion').fadeIn(300).css('display', 'inline-block');
            
            hayErrores = true;
            
            break;
        } else if ($.trim(opciones[i].value) === '') {
            $('#errorTextoOpcionesCasillasVerificacion').css('display', 'none');
            
            $('#guardarCasillasVerificacion').addClass('animacion-error');
            $('#errorOpcionesCasillasVerificacion').fadeIn(300).css('display', 'inline-block');

            hayErrores = true;

            break;
        } else {
            /* Si el usuario corrije los problemas, se ocultan los errores. */
            $('#errorOpcionesCasillasVerificacion').css('display', 'none');
            $('#errorTextoOpcionesCasillasVerificacion').css('display', 'none');
        }
    }

    if (!hayErrores) {
        if (sessionStorage.getItem('id') !== null) {
            sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) + 1);
        } else {
            sessionStorage.setItem('id', 1);
        }

        $('#guardarCasillasVerificacion').removeClass('animacion-error');
        $('.previa-formulario').fadeIn(650).removeClass('oculto');

        var listaCheckbox;
        var listaOpciones = '';

        listaCheckbox = {tipoCampo: "ListaCheckbox", titulo: $('#tituloCasillasVerificacion').val(), descripcion: $('#descripcionCasillasVerificacion').val(), obligatorio: 0, opciones: []};

        while (opciones.length > 0) {
            var opcion = opciones.shift().value;

            if (listaCheckbox.opciones.indexOf(opcion) === -1) {
                listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"checkbox\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                listaCheckbox.opciones.push(opcion);
            }
        }

        $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCasillasVerificacion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCasillasVerificacion').val() + '</p>' + listaOpciones + '</div>');
        $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: flex-start; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center; padding-top: 10px;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición arriba.\" type=\"button\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición abajo.\" type=\"button\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Editar este campo.\" type=\"button\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1;\" title=\"Eliminar este campo del formulario.\" type=\"button\"><span class=\"oi oi-trash\"></span></button></div>');

        listaCheckbox = JSON.stringify(listaCheckbox);

        $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
        $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(listaCheckbox);

        desactivarBotonesMoverInnecesarios();
        descartarCasillasVerificacion();
    }
});

$('#guardarEdicionCasillasVerificacion').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloCasillasVerificacionEdicion').val()) === '') {
        $('#guardarEdicionCasillasVerificacion').addClass('animacion-error');
        $('#errorTituloCasillasVerificacionEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloCasillasVerificacionEdicion').css('display', 'none');
    }

    var opciones = document.getElementById('opcionesCasillasVerificacionEdicion').getElementsByTagName('input');
    opciones = Array.from(opciones);

    for (var i = 0; i < opciones.length; i++) {
        if ($.trim(opciones[i].value) === '') {
            $('#guardarEdicionCasillasVerificacion').addClass('animacion-error');
            $('#errorOpcionesCasillasVerificacionEdicion').fadeIn(300).css('display', 'inline-block');

            hayErrores = true;

            break;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorOpcionesCasillasVerificacionEdicion').css('display', 'none');
        }
    }

    if (!hayErrores) {
        var idCampoEditado = $('#idCampoEditado').val();

        $('#guardarEdicionCasillasVerificacion').removeClass('animacion-error');

        var listaCheckbox;
        var listaOpciones = '';

        listaCheckbox = {tipoCampo: "ListaCheckbox", titulo: $('#tituloCasillasVerificacionEdicion').val(), descripcion: $('#descripcionCasillasVerificacionEdicion').val(), obligatorio: 0, opciones: []};

        while (opciones.length > 0) {
            var opcion = opciones.shift().value;

            if (listaCheckbox.opciones.indexOf(opcion) === -1) {
                listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"checkbox\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                listaCheckbox.opciones.push(opcion);
            }
        }

        $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCasillasVerificacionEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCasillasVerificacionEdicion').val() + '</p>' + listaOpciones);

        listaCheckbox = JSON.stringify(listaCheckbox);

        $('#accionesCampoID' + idCampoEditado).css('height', $('#campoID' + idCampoEditado).height() + 11 + 'px');
        $('input[name=campoID' + idCampoEditado + ']').val(listaCheckbox);

        descartarEdicionCasillasVerificacion();

        $('html, body').animate({
            scrollTop: $("#campoID" + idCampoEditado).offset().top
        }, 500);
    }
});

function descartarCasillasVerificacion() {
    $('#tituloCasillasVerificacion').val('');
    $('#descripcionCasillasVerificacion').val('');
    $('#obligatorioCasillasVerificacion').prop('checked', false);

    var identificadorPrimeraOpcion = $('#opcionesCasillasVerificacion input:first').attr('id');
    var identificadorUltimaOpcion = $('#opcionesCasillasVerificacion input:last').attr('id');

    while (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
        $('#opcionesCasillasVerificacion input:last').remove();
        identificadorUltimaOpcion = $('#opcionesCasillasVerificacion input:last').attr('id');
    }

    $('#opcionesCasillasVerificacion input:first').val('');
}

$('#descartarCasillasVerificacion').click(function () {
    descartarCasillasVerificacion();
});

function descartarEdicionCasillasVerificacion() {
    $('#tituloCasillasVerificacion').val('');
    $('#descripcionCasillasVerificacion').val('');
    $('#obligatorioCasillasVerificacion').prop('checked', false);
    $('#opcionesCasillasVerificacionEdicion input').remove();
    $('#idCampoEditado').val('');

    $('.editor').removeClass('oculto').not('#editorCamposCrear').addClass('oculto');

    /* Se reactivan los botones de acción que se desactivaron durante la edición. */
    reactivarBotonesAccion();
}

$('#descartarEdicionCasillasVerificacion').click(function () {
    descartarEdicionCasillasVerificacion();
});

/* * * * * * * * *
 * ÁREA DE TEXTO *
 * * * * * * * * */

$('#limiteAreaTexto, #limiteAreaTextoEdicion').on({
    keydown: function (event) {
        if (event.keyCode === 32) {
            return false;
        }
    },

    paste: function (event) {
        event.preventDefault();

        var contenidoPortapapeles = parseInt(event.originalEvent.clipboardData.getData('Text'));

        /*
         * Se asigna el contenido del portapapeles al campo solo si dicho
         * contenido es un número entero.
         */
        if (Number.isInteger(contenidoPortapapeles)) {
            $(this).val(contenidoPortapapeles);
        }
    }
});

$('#nuevaAreaTexto').click(function () {
    $('.div-crear').removeClass('oculto').not('#editorAreaTexto').addClass('oculto');
    $('.nuevo-campo').addClass('seleccionado').not('#nuevaAreaTexto').removeClass('seleccionado');
    $('.editor-cabecera').addClass('animacion').not('#cabeceraAreaTexto').removeClass('animacion');
});

$('#guardarAreaTexto').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloAreaTexto').val()) === '') {
        $('#guardarAreaTexto').addClass('animacion-error');
        $('#errorTituloAreaTexto').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloAreaTexto').css('display', 'none');
    }

    var limiteAreaTexto = parseInt($('#limiteAreaTexto').val());

    if (limiteAreaTexto === '' || !Number.isInteger(limiteAreaTexto) || limiteAreaTexto < 100 || limiteAreaTexto > 500) {
        $('#guardarAreaTexto').addClass('animacion-error');
        $('#errorLimiteAreaTexto').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorLimiteAreaTexto').css('display', 'none');
    }

    if (!hayErrores) {
        if (sessionStorage.getItem('id') !== null) {
            sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) + 1);
        } else {
            sessionStorage.setItem('id', 1);
        }

        $('#guardarAreaTexto').removeClass('animacion-error');
        $('.previa-formulario').fadeIn(650).removeClass('oculto');

        var areaTexto;

        if ($('#obligatorioAreaTexto').is(':checked')) {
            areaTexto = {tipoCampo: "AreaTexto", titulo: $('#tituloAreaTexto').val(), descripcion: $('#descripcionAreaTexto').val(), obligatorio: 1, limite: limiteAreaTexto};

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloAreaTexto').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionAreaTexto').val() + '</p><textarea class=\"campo-editor\" disabled maxlength=\"' + limiteAreaTexto + '\" style=\"resize: none;\"></textarea></div>');
        } else {
            areaTexto = {tipoCampo: "AreaTexto", titulo: $('#tituloAreaTexto').val(), descripcion: $('#descripcionAreaTexto').val(), obligatorio: 0, limite: limiteAreaTexto};

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloAreaTexto').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionAreaTexto').val() + '</p><textarea class=\"campo-editor\" disabled maxlength=\"' + limiteAreaTexto + '\" style=\"resize: none;\"></textarea></div>');
        }

        areaTexto = JSON.stringify(areaTexto);

        $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: flex-start; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center; padding-top: 10px;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición arriba.\" type=\"button\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición abajo.\" type=\"button\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Editar este campo.\" type=\"button\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1;\" title=\"Eliminar este campo del formulario.\" type=\"button\"><span class=\"oi oi-trash\"></span></button></div>');

        $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
        $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(areaTexto);

        desactivarBotonesMoverInnecesarios();
        descartarAreaTexto();
    }
});

$('#guardarEdicionAreaTexto').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloAreaTextoEdicion').val()) === '') {
        $('#guardarEdicionAreaTexto').addClass('animacion-error');
        $('#errorTituloAreaTextoEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloAreaTextoEdicion').css('display', 'none');
    }

    var limiteAreaTexto = parseInt($('#limiteAreaTextoEdicion').val());

    if (limiteAreaTexto === '' || !Number.isInteger(limiteAreaTexto) || limiteAreaTexto < 100 || limiteAreaTexto > 500) {
        $('#guardarEdicionAreaTexto').addClass('animacion-error');
        $('#errorLimiteAreaTextoEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorLimiteAreaTextoEdicion').css('display', 'none');
    }

    if (!hayErrores) {
        var idCampoEditado = $('#idCampoEditado').val();

        $('#guardarEdicionAreaTexto').removeClass('animacion-error');

        var areaTexto;

        if ($('#obligatorioAreaTextoEdicion').is(':checked')) {
            areaTexto = {tipoCampo: "AreaTexto", titulo: $('#tituloAreaTextoEdicion').val(), descripcion: $('#descripcionAreaTextoEdicion').val(), obligatorio: 1, limite: limiteAreaTexto};

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloAreaTextoEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionAreaTextoEdicion').val() + '</p><textarea class=\"campo-editor\" disabled maxlength=\"' + limiteAreaTexto + '\" style=\"resize: none;\"></textarea>');
        } else {
            areaTexto = {tipoCampo: "AreaTexto", titulo: $('#tituloAreaTextoEdicion').val(), descripcion: $('#descripcionAreaTextoEdicion').val(), obligatorio: 0, limite: limiteAreaTexto};

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloAreaTextoEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionAreaTextoEdicion').val() + '</p><textarea class=\"campo-editor\" disabled maxlength=\"' + limiteAreaTexto + '\" style=\"resize: none;\"></textarea>');
        }

        areaTexto = JSON.stringify(areaTexto);

        $('#accionesCampoID' + idCampoEditado).css('height', $('#campoID' + idCampoEditado).height() + 11 + 'px');
        $('input[name=campoID' + idCampoEditado + ']').val(areaTexto);

        descartarEdicionAreaTexto();

        $('html, body').animate({
            scrollTop: $("#campoID" + idCampoEditado).offset().top
        }, 500);
    }
});

function descartarAreaTexto() {
    $('#tituloAreaTexto').val('');
    $('#descripcionAreaTexto').val('');
    $('#obligatorioAreaTexto').prop('checked', false);
    $('#limiteAreaTexto').val('');
}

$('#descartarAreaTexto').click(function () {
    descartarAreaTexto();
});

function descartarEdicionAreaTexto() {
    $('#tituloAreaTextoEdicion').val('');
    $('#descripcionAreaTextoEdicion').val('');
    $('#obligatorioAreaTextoEdicion').prop('checked', false);
    $('#limiteAreaTextoEdicion').val('');
    $('#idCampoEditado').val('');

    $('.editor').removeClass('oculto').not('#editorCamposCrear').addClass('oculto');

    /* Se reactivan los botones de acción que se desactivaron durante la edición. */
    reactivarBotonesAccion();
}

$('#descartarEdicionAreaTexto').click(function () {
    descartarEdicionAreaTexto();
});

/* * * * * * * * * * *
 * BOTONES DE RADIO  *
 * * * * * * * * * * */

$('#nuevaListaRadio').click(function () {
    $('.div-crear').removeClass('oculto').not('#editorBotonesRadio').addClass('oculto');
    $('.nuevo-campo').addClass('seleccionado').not('#nuevaListaRadio').removeClass('seleccionado');
    $('.editor-cabecera').addClass('animacion').not('#cabeceraBotonesRadio').removeClass('animacion');
});

/* Funciones para agregar y eliminar elementos de la lista de verificación. */

$('#agregarBotonRadio').click(function () {
    /* Se obtiene el ID numérico del último campo. */
    var idOpcionNueva = parseInt($('#opcionesBotonesRadio input:last').attr('id').substring(12)) + 1;

    if (idOpcionNueva <= 50) {
        var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Botón de radio ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');

        $('#opcionesBotonesRadio').append(opcionNueva);
        $("#opcionesBotonesRadio").scrollTop($("#opcionesBotonesRadio")[0].scrollHeight);
    }
});

$('#eliminarBotonRadio').click(function () {
    var identificadorSegundaOpcion = $('#opcionesBotonesRadio input:nth-child(2)').attr('id');
    var identificadorUltimaOpcion = $('#opcionesBotonesRadio input:last').attr('id');

    if (identificadorSegundaOpcion !== identificadorUltimaOpcion) {
        var ultimaOpcion = $('#opcionesBotonesRadio input:last');

        ultimaOpcion.remove();
    }
});

$('#agregarBotonRadioEdicion').click(function () {
    /* Se obtiene el ID numérico del último campo. */
    var idOpcionNueva = parseInt($('#opcionesBotonesRadioEdicion input:last').attr('id').substring(12)) + 1;

    if (idOpcionNueva <= 50) {
        var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Botón de radio ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');

        $('#opcionesBotonesRadioEdicion').append(opcionNueva);
        $("#opcionesBotonesRadioEdicion").scrollTop($("#opcionesBotonesRadioEdicion")[0].scrollHeight);
    }
});

$('#eliminarBotonRadioEdicion').click(function () {
    var identificadorSegundaOpcion = $('#opcionesBotonesRadioEdicion input:nth-child(2)').attr('id');
    var identificadorUltimaOpcion = $('#opcionesBotonesRadioEdicion input:last').attr('id');

    if (identificadorSegundaOpcion !== identificadorUltimaOpcion) {
        var ultimaOpcion = $('#opcionesBotonesRadioEdicion input:last');

        ultimaOpcion.remove();
    }
});

$('#guardarBotonesRadio').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloBotonesRadio').val()) === '') {
        $('#guardarBotonesRadio').addClass('animacion-error');
        $('#errorTituloBotonesRadio').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloBotonesRadio').css('display', 'none');
    }

    var opciones = document.getElementById('opcionesBotonesRadio').getElementsByTagName('input');
    opciones = Array.from(opciones);

    if (opciones.every(opcion => opcion.value === opciones[0].value)) {
        $('#guardarBotonesRadio').addClass('animacion-error');
        $('#errorOpcionesBotonesRadioIguales').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        $('#errorOpcionesBotonesRadioIguales').css('display', 'none');
    }

    for (var i = 0; i < opciones.length; i++) {
        if ($.trim(opciones[i].value) === '') {
            $('#guardarBotonesRadio').addClass('animacion-error');
            $('#errorOpcionesBotonesRadioIguales').css('display', 'none');
            $('#errorOpcionesBotonesRadio').fadeIn(300).css('display', 'inline-block');

            hayErrores = true;

            break;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorOpcionesBotonesRadio').css('display', 'none');
        }
    }

    if (!hayErrores) {
        if (sessionStorage.getItem('id') !== null) {
            sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) + 1);
        } else {
            sessionStorage.setItem('id', 1);
        }

        $('#guardarBotonesRadio').removeClass('animacion-error');
        $('.previa-formulario').fadeIn(650).removeClass('oculto');

        var listaBotonRadio;
        var listaOpciones = '';

        if ($('#obligatorioBotonesRadio').is(':checked')) {
            listaBotonRadio = {tipoCampo: "ListaBotonRadio", titulo: $('#tituloBotonesRadio').val(), descripcion: $('#descripcionBotonesRadio').val(), obligatorio: 1, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaBotonRadio.opciones.indexOf(opcion) === -1) {
                    listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"radio\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                    listaBotonRadio.opciones.push(opcion);
                }
            }

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloBotonesRadio').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionBotonesRadio').val() + '</p>' + listaOpciones + '</div>');
        } else {
            listaBotonRadio = {tipoCampo: "ListaBotonRadio", titulo: $('#tituloBotonesRadio').val(), descripcion: $('#descripcionBotonesRadio').val(), obligatorio: 0, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaBotonRadio.opciones.indexOf(opcion) === -1) {
                    listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"radio\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                    listaBotonRadio.opciones.push(opcion);
                }
            }

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloBotonesRadio').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionBotonesRadio').val() + '</p>' + listaOpciones + '</div>');
        }

        listaBotonRadio = JSON.stringify(listaBotonRadio);

        $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: flex-start; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center; padding-top: 10px;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición arriba.\" type=\"button\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Mover este campo una posición abajo.\" type=\"button\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1; margin-right: 5px;\" title=\"Editar este campo.\" type=\"button\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" style=\"flex-grow: 1;\" title=\"Eliminar este campo del formulario.\" type=\"button\"><span class=\"oi oi-trash\"></span></button></div>');

        $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
        $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(listaBotonRadio);

        desactivarBotonesMoverInnecesarios();
        descartarBotonesRadio();
    }
});

$('#guardarEdicionBotonesRadio').click(function () {
    var hayErrores = false;

    if ($.trim($('#tituloBotonesRadioEdicion').val()) === '') {
        $('#guardarEdicionBotonesRadio').addClass('animacion-error');
        $('#errorTituloBotonesRadioEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        /* Si el usuario corrije este problema, se oculta el error. */
        $('#errorTituloBotonesRadioEdicion').css('display', 'none');
    }

    var opciones = document.getElementById('opcionesBotonesRadioEdicion').getElementsByTagName('input');
    opciones = Array.from(opciones);

    if (opciones.every(opcion => opcion.value === opciones[0].value)) {
        $('#guardarEdicionBotonesRadio').addClass('animacion-error');
        $('#errorOpcionesBotonesRadioIgualesEdicion').fadeIn(300).css('display', 'inline-block');

        hayErrores = true;
    } else {
        $('#errorOpcionesBotonesRadioIgualesEdicion').css('display', 'none');
    }

    for (var i = 0; i < opciones.length; i++) {
        if ($.trim(opciones[i].value) === '') {
            $('#guardarEdicionBotonesRadio').addClass('animacion-error');
            $('#errorOpcionesBotonesRadioIgualesEdicion').css('display', 'none');
            $('#errorOpcionesBotonesRadioEdicion').fadeIn(300).css('display', 'inline-block');

            hayErrores = true;

            break;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorOpcionesBotonesRadioEdicion').css('display', 'none');
        }
    }

    if (!hayErrores) {
        var idCampoEditado = $('#idCampoEditado').val();

        $('#guardarEdicionBotonesRadio').removeClass('animacion-error');

        var listaBotonRadio;
        var listaOpciones = '';

        if ($('#obligatorioBotonesRadioEdicion').is(':checked')) {
            listaBotonRadio = {tipoCampo: "ListaBotonRadio", titulo: $('#tituloBotonesRadioEdicion').val(), descripcion: $('#descripcionBotonesRadioEdicion').val(), obligatorio: 1, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaBotonRadio.opciones.indexOf(opcion) === -1) {
                    listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"radio\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                    listaBotonRadio.opciones.push(opcion);
                }
            }

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloBotonesRadioEdicion').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionBotonesRadioEdicion').val() + '</p>' + listaOpciones);
        } else {
            listaBotonRadio = {tipoCampo: "ListaBotonRadio", titulo: $('#tituloBotonesRadioEdicion').val(), descripcion: $('#descripcionBotonesRadioEdicion').val(), obligatorio: 0, opciones: []};

            while (opciones.length > 0) {
                var opcion = opciones.shift().value;

                if (listaBotonRadio.opciones.indexOf(opcion) === -1) {
                    listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"radio\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                    listaBotonRadio.opciones.push(opcion);
                }
            }

            $('#campoID' + idCampoEditado).html('<p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloBotonesRadioEdicion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionBotonesRadioEdicion').val() + '</p>' + listaOpciones);
        }

        listaBotonRadio = JSON.stringify(listaBotonRadio);

        $('#accionesCampoID' + idCampoEditado).css('height', $('#campoID' + idCampoEditado).height() + 11 + 'px');
        $('input[name=campoID' + idCampoEditado + ']').val(listaBotonRadio);

        descartarEdicionBotonesRadio();

        $('html, body').animate({
            scrollTop: $("#campoID" + idCampoEditado).offset().top
        }, 500);
    }
});

function descartarBotonesRadio() {
    $('#tituloBotonesRadio').val('');
    $('#descripcionBotonesRadio').val('');
    $('#obligatorioBotonesRadio').prop('checked', false);

    var identificadorSegundaOpcion = $('#opcionesBotonesRadio input:nth-child(2)').attr('id');
    var identificadorUltimaOpcion = $('#opcionesBotonesRadio input:last').attr('id');

    while (identificadorSegundaOpcion !== identificadorUltimaOpcion) {
        $('#opcionesBotonesRadio input:last').remove();
        identificadorUltimaOpcion = $('#opcionesBotonesRadio input:last').attr('id');
    }

    $('#opcionesBotonesRadio input:first').val('');
    $('#opcionesBotonesRadio input:nth-child(2)').val('');
}

$('#descartarBotonesRadio').click(function () {
    descartarBotonesRadio();
});

function descartarEdicionBotonesRadio() {
    $('#tituloBotonesRadioEdicion').val('');
    $('#descripcionBotonesRadioEdicion').val('');
    $('#obligatorioBotonesRadioEdicion').prop('checked', false);
    $('#opcionesBotonesRadioEdicion input').remove();
    $('#idCampoEditado').val('');

    $('.editor').removeClass('oculto').not('#editorCamposCrear').addClass('oculto');

    /* Se reactivan los botones de acción que se desactivaron durante la edición. */
    reactivarBotonesAccion();
}

$('#descartarEdicionBotonesRadio').click(function () {
    descartarEdicionBotonesRadio();
});

function desactivarBotonesMoverInnecesarios() {
    var idAnteultimo = $("#botonesPreviaFormulario > div").length - 1;
    var idUltimo = $("#botonesPreviaFormulario > div").length;

    $('#accionesCampoID1 :button[onclick*=ARRIBA]').fadeTo(200, 0.1).attr('disabled', true);
    $('#accionesCampoID' + idAnteultimo + ' :button[onclick*=ABAJO]').fadeTo(200, 1).attr('disabled', false);
    $('#accionesCampoID' + idUltimo + ' :button[onclick*=ABAJO]').fadeTo(200, 0.1).attr('disabled', true);
}

function editarCampo(idCampo) {
    /* Se desactivan los botones para eliminar y mover campos durante la edición. */
    $('#botonesPreviaFormulario .btn-danger').fadeTo(300, 0.1).attr('disabled', true);
    $('#botonesPreviaFormulario .btn-primary').fadeTo(300, 0.1).attr('disabled', true);
    $('#botonesPreviaFormulario .btn-warning').fadeTo(200, 1).attr('disabled', false);

    /* Se desactivan los botones de acción para este campo durante la edición. */
    $('#accionesCampoID' + idCampo + ' .btn-warning').fadeTo(300, 0.1).attr('disabled', true);

    var jsonCampo = JSON.parse($('#camposCreados').find('input[name=campoID' + idCampo + ']').val());

    /* Propiedades genéricas del campo recuperado */
    var tipoCampo = jsonCampo.tipoCampo;
    var titulo = jsonCampo.titulo;
    var descripcion = jsonCampo.descripcion;

    $('#idCampoEditado').val(idCampo);

    switch (tipoCampo) {
        case 'CampoEmail':
            var esObligatorio = jsonCampo.obligatorio;
            var pista = jsonCampo.pista;

            $('#cabeceraEdicionCampo').text('EDITANDO CAMPO DE TEXTO');
            $('#tituloCampoTextoEdicion').val(titulo);
            $('#descripcionCampoTextoEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioCampoTextoEdicion').prop('checked', true);
            } else {
                $('#obligatorioCampoTextoEdicion').prop('checked', false);
            }

            $('#pistaCampoTextoEdicion').val(pista);
            $('#campoTextoEmailEdicion').prop('checked', true);

            $('.div-editar').removeClass('oculto').not('#edicionCampoTexto').addClass('oculto');

            break;
        case 'CampoNumerico':
            var esObligatorio = jsonCampo.obligatorio;
            var pista = jsonCampo.pista;

            $('#cabeceraEdicionCampo').text('EDITANDO CAMPO DE TEXTO');
            $('#tituloCampoTextoEdicion').val(titulo);
            $('#descripcionCampoTextoEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioCampoTextoEdicion').prop('checked', true);
            } else {
                $('#obligatorioCampoTextoEdicion').prop('checked', false);
            }

            $('#pistaCampoTextoEdicion').val(pista);
            $('#campoTextoNumericoEdicion').prop('checked', true);

            $('.div-editar').removeClass('oculto').not('#edicionCampoTexto').addClass('oculto');

            break;
        case 'CampoTexto':
            var esObligatorio = jsonCampo.obligatorio;
            var pista = jsonCampo.pista;

            $('#cabeceraEdicionCampo').text('EDITANDO CAMPO DE TEXTO');
            $('#tituloCampoTextoEdicion').val(titulo);
            $('#descripcionCampoTextoEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioCampoTextoEdicion').prop('checked', true);
            } else {
                $('#obligatorioCampoTextoEdicion').prop('checked', false);
            }

            $('#pistaCampoTextoEdicion').val(pista);
            $('#campoTextoTextoEdicion').prop('checked', true);

            $('.div-editar').removeClass('oculto').not('#edicionCampoTexto').addClass('oculto');

            break;
        case 'AreaTexto':
            var esObligatorio = jsonCampo.obligatorio;
            var limiteCaracteres = jsonCampo.limite;

            $('#cabeceraEdicionCampo').text('EDITANDO ÁREA DE TEXTO');
            $('#tituloAreaTextoEdicion').val(titulo);
            $('#descripcionAreaTextoEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioAreaTextoEdicion').prop('checked', true);
            } else {
                $('#obligatorioAreaTextoEdicion').prop('checked', false);
            }

            $('#limiteAreaTextoEdicion').val(limiteCaracteres);

            $('.div-editar').removeClass('oculto').not('#edicionAreaTexto').addClass('oculto');

            break;
        case 'Fecha':
            var esObligatorio = jsonCampo.obligatorio;

            $('#cabeceraEdicionCampo').text('EDITANDO SELECTOR DE FECHA');
            $('#tituloCampoFechaEdicion').val(titulo);
            $('#descripcionCampoFechaEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioCampoFechaEdicion').prop('checked', true);
            } else {
                $('#obligatorioCampoFechaEdicion').prop('checked', false);
            }

            $('.div-editar').removeClass('oculto').not('#edicionCampoFecha').addClass('oculto');

            break;
        case 'ListaDesplegable':
            $('#opcionesListaDesplegableEdicion input').remove();

            var esObligatorio = jsonCampo.obligatorio;
            var opciones = jsonCampo.opciones;

            $('#cabeceraEdicionCampo').text('EDITANDO LISTA DESPLEGABLE');
            $('#tituloListaDesplegableEdicion').val(titulo);
            $('#descripcionListaDesplegableEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioListaDesplegableEdicion').prop('checked', true);
            } else {
                $('#obligatorioListaDesplegableEdicion').prop('checked', false);
            }

            opciones.forEach(function (opcion) {
                var opcionRecuperada = $('<input class=\"campo-editor\" id=\"opcionNumero1Edicion\" maxlength=\"40\" placeholder=\"Opción 1\" type=\"text\" value=\"' + opcion + '\"/>');

                if ($('#opcionesListaDesplegableEdicion input').length) {
                    var idOpcionRecuperada = parseInt($('#opcionesListaDesplegableEdicion input:last').attr('id').substring(12)) + 1;
                    opcionRecuperada = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionRecuperada + 'Edicion\" maxlength=\"40\" placeholder=\"Opción ' + idOpcionRecuperada + '\" style=\"margin-top: 5px;\" type=\"text\" value=\"' + opcion + '\"/>');
                }

                $('#opcionesListaDesplegableEdicion').append(opcionRecuperada);
            });

            $('.div-editar').removeClass('oculto').not('#edicionListaDesplegable').addClass('oculto');

            break;
        case 'ListaCheckbox':
            $('#opcionesCasillasVerificacionEdicion input').remove();

            var opciones = jsonCampo.opciones;

            $('#cabeceraEdicionCampo').text('EDITANDO CASILLAS DE VERIFICACIÓN');
            $('#tituloCasillasVerificacionEdicion').val(titulo);
            $('#descripcionCasillasVerificacionEdicion').val(descripcion);

            opciones.forEach(function (opcion) {
                var opcionRecuperada = $('<input class=\"campo-editor\" id=\"opcionNumero1Edicion\" maxlength=\"40\" placeholder=\"Casilla de verificación 1\" type=\"text\" value=\"' + opcion + '\"/>');

                if ($('#opcionesCasillasVerificacionEdicion input').length) {
                    var idOpcionRecuperada = parseInt($('#opcionesCasillasVerificacionEdicion input:last').attr('id').substring(12)) + 1;
                    opcionRecuperada = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionRecuperada + 'Edicion\" maxlength=\"40\" placeholder=\"Casilla de verificación ' + idOpcionRecuperada + '\" style=\"margin-top: 5px;\" type=\"text\" value=\"' + opcion + '\"/>');
                }

                $('#opcionesCasillasVerificacionEdicion').append(opcionRecuperada);
            });

            $('.div-editar').removeClass('oculto').not('#edicionCasillasVerificacion').addClass('oculto');

            break;
        case 'ListaBotonRadio':
            $('#opcionesBotonesRadioEdicion input').remove();

            var esObligatorio = jsonCampo.obligatorio;
            var opciones = jsonCampo.opciones;

            $('#cabeceraEdicionCampo').text('EDITANDO BOTONES DE RADIO');
            $('#tituloBotonesRadioEdicion').val(titulo);
            $('#descripcionBotonesRadioEdicion').val(descripcion);

            if (esObligatorio === 1) {
                $('#obligatorioBotonesRadioEdicion').prop('checked', true);
            } else {
                $('#obligatorioBotonesRadioEdicion').prop('checked', false);
            }

            opciones.forEach(function (opcion) {
                var opcionRecuperada = $('<input class=\"campo-editor\" id=\"opcionNumero1Edicion\" maxlength=\"40\" placeholder=\"Botón de radio 1\" type=\"text\" value=\"' + opcion + '\"/>');

                if ($('#opcionesBotonesRadioEdicion input').length) {
                    var idOpcionRecuperada = parseInt($('#opcionesBotonesRadioEdicion input:last').attr('id').substring(12)) + 1;
                    opcionRecuperada = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionRecuperada + 'Edicion\" maxlength=\"40\" placeholder=\"Botón de radio ' + idOpcionRecuperada + '\" style=\"margin-top: 5px;\" type=\"text\" value=\"' + opcion + '\"/>');
                }

                $('#opcionesBotonesRadioEdicion').append(opcionRecuperada);
            });

            $('.div-editar').removeClass('oculto').not('#edicionBotonesRadio').addClass('oculto');

            break;
    }

    $('.editor').removeClass('oculto').not('#editorCamposEditar').addClass('oculto');

    $('html, body').animate({
        scrollTop: $("#editorCamposEditar").offset().top
    }, 500);

    $('.editor-cabecera').addClass('animacion').not('#cabeceraEdicionCampo').removeClass('animacion');
}

function eliminarCampo(idCampo) {
    var nombreCampo = $('#campoID' + idCampo).find('.campo-cabecera').text();

    if ($('#camposCreados').find('input[name=campoID' + idCampo + ']').val().replace(/\s+/g, '').indexOf('"obligatorio":1') > -1) {
        nombreCampo = nombreCampo.substring(0, nombreCampo.length - 1);
    }

    $.confirm({
        icon: 'oi oi-trash',
        title: 'Eliminar campo',
        content: '¿Desea eliminar el campo «<i>' + nombreCampo + '</i>» de su formulario? Esta acción <strong>no se puede deshacer</strong>.',
        animation: 'none',
        boxWidth: '500px',
        closeAnimation: 'none',
        theme: 'material',
        type: 'red',
        useBootstrap: false,
        buttons: {
            confirm: {
                btnClass: 'btn-red',
                text: 'Sí',
                action: function () {
                    $('#camposCreados').find('input[name=campoID' + idCampo + ']').remove();
                    $('#campoID' + idCampo).remove();
                    $('#accionesCampoID' + idCampo).remove();

                    /* Se reasignan los ID para los campos siguientes. */
                    var campoInferior = $('#campoID' + (idCampo + 1));

                    while (campoInferior.val() !== undefined) {
                        idCampo++;
                        var nuevaIdCampo = idCampo - 1;

                        $('#camposCreados').find('input[name=campoID' + idCampo + ']').attr('name', 'campoID' + nuevaIdCampo);
                        campoInferior.attr('id', 'campoID' + nuevaIdCampo);
                        $('#accionesCampoID' + idCampo).attr('id', 'accionesCampoID' + nuevaIdCampo);

                        $('#accionesCampoID' + nuevaIdCampo).children().each(function () {
                            if ($(this).attr('onclick').indexOf('ARRIBA') > -1) {
                                $(this).attr('onclick', 'moverCampo(' + nuevaIdCampo + ', \'ARRIBA\')');
                            } else if ($(this).attr('onclick').indexOf('ABAJO') > -1) {
                                $(this).attr('onclick', 'moverCampo(' + nuevaIdCampo + ', \'ABAJO\')');
                            } else if ($(this).attr('onclick').indexOf('editarCampo') > -1) {
                                $(this).attr('onclick', 'editarCampo(' + nuevaIdCampo + ')');
                            } else {
                                $(this).attr('onclick', 'eliminarCampo(' + nuevaIdCampo + ')');
                            }
                        });

                        campoInferior = $('#campoID' + (idCampo + 1));
                    }

                    /* También se "elimina" el campo en sessionStorage. */
                    sessionStorage.setItem('id', Number(sessionStorage.getItem('id')) - 1);

                    /* Si no existen más campos, se oculta la vista previa del formulario. */
                    if ($('#vistaPreviaFormulario').text().trim() === "") {
                        $('.previa-formulario').addClass('oculto');
                    } else {
                        desactivarBotonesMoverInnecesarios();
                    }
                }
            },
            cancelar: {
                text: 'No'
            }
        }
    });
}

function moverCampo(idCampo, direccionDesplazamiento) {
    if (direccionDesplazamiento === 'ABAJO') {
        var idCampoInferior = idCampo + 1;

        var jsonCampo = $('#camposCreados').find('input[name=campoID' + idCampo + ']').val();
        var jsonCampoInferior = $('#camposCreados').find('input[name=campoID' + idCampoInferior + ']').val();

        $('#camposCreados').find('input[name=campoID' + idCampoInferior + ']').val(jsonCampo);
        $('#camposCreados').find('input[name=campoID' + idCampo + ']').val(jsonCampoInferior);

        var htmlCampo = $('#campoID' + idCampo).html();
        var htmlCampoInferior = $('#campoID' + idCampoInferior).html();

        $('#campoID' + idCampoInferior).html(htmlCampo);
        $('#campoID' + idCampo).html(htmlCampoInferior);

        var alturaDivAcciones = $('#accionesCampoID' + idCampo).height();

        $('#accionesCampoID' + idCampo).height($('#accionesCampoID' + idCampoInferior).height());
        $('#accionesCampoID' + idCampoInferior).height(alturaDivAcciones);
    } else if (direccionDesplazamiento === 'ARRIBA') {
        var idCampoSuperior = idCampo - 1;

        var jsonCampo = $('#camposCreados').find('input[name=campoID' + idCampo + ']').val();
        var jsonCampoSuperior = $('#camposCreados').find('input[name=campoID' + idCampoSuperior + ']').val();

        $('#camposCreados').find('input[name=campoID' + idCampoSuperior + ']').val(jsonCampo);
        $('#camposCreados').find('input[name=campoID' + idCampo + ']').val(jsonCampoSuperior);

        var htmlCampo = $('#campoID' + idCampo).html();
        var htmlCampoSuperior = $('#campoID' + idCampoSuperior).html();

        $('#campoID' + idCampoSuperior).html(htmlCampo);
        $('#campoID' + idCampo).html(htmlCampoSuperior);

        var alturaDivAcciones = $('#accionesCampoID' + idCampo).height();

        $('#accionesCampoID' + idCampo).height($('#accionesCampoID' + idCampoSuperior).height());
        $('#accionesCampoID' + idCampoSuperior).height(alturaDivAcciones);
    }
}

function reactivarBotonesAccion() {
    $('#botonesPreviaFormulario :button').fadeTo(300, 1).attr('disabled', false);
    desactivarBotonesMoverInnecesarios();
}

/*
 * Aquí termina la lista de funciones correspondientes a la herramienta de
 * edición de campos del sistema.
 */

/*
 * Código correspondiente a la validación del formulario.
 */
(function () {
    window.addEventListener('load', function () {
        var formulario = document.getElementById('crearFormulario');

        formulario.addEventListener('submit', function (event) {
            if (formulario.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (!$('input[name="rolesDestinoFormulario[]"]').is(':checked')) {
                event.preventDefault();
                event.stopPropagation();

                $('#errorSinDestinatarios').css('display', 'block');
            } else {
                $('#errorSinDestinatarios').css('display', 'none');
            }

            if ($('#camposCreados').html().trim() === "") {
                event.preventDefault();
                event.stopPropagation;

                $('#errorSinCamposObligatorios').css('display', 'none');
                $('#errorSinCampos').css('display', 'block');
            } else {
                $('#errorSinCampos').css('display', 'none');
                
                if ($('#camposCreados').html().trim().indexOf('&quot;obligatorio&quot;:1') === -1 && $('#camposCreados').html().trim().indexOf('&quot;obligatorio&quot;: 1') === -1) {
                    event.preventDefault();
                    event.stopPropagation;

                    $('#errorSinCamposObligatorios').css('display', 'block');
                } else {
                    $('#errorSinCamposObligatorios').css('display', 'none');
                }
            }

            $('html, body').animate({
                scrollTop: 0
            }, 750);

            formulario.classList.add('was-validated');
        }, false);
    }, false);
})();
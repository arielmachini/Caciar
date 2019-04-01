/**
 * Archivo: colibri.creador.js.
 * 
 * El código en este archivo corresponde a aquellas partes del sistema Colibrí
 * en las que se muestre un formulario.
 * 
 * @author Ariel Machini
 */

function eliminarCampo(idCampo) {
    $('#camposCreados').find('input[name=campoID' + idCampo + ']').remove();
}

function editarCampoTexto(idCampo) {
    var jsonCampoTexto = $('#camposCreados').find('input[name=campoID' + idCampo + ']').val();
    alert(jsonCampoTexto);
}

$(document).ready(function () {
    "use strict"; // PARA DEBUGGING.
    
    /* Se establecen límites en la selección de fechas de apertura y cierre. */
    $('#fechaApertura').datepicker({minDate: 0});
    $('#fechaCierre').datepicker({minDate: 1});

    $('#fechaApertura').on('change', function () {
        var fechaCierreMinima = $('#fechaApertura').datepicker('getDate', '+1d');

        fechaCierreMinima.setDate(fechaCierreMinima.getDate() + 1);

        $('#fechaCierre').datepicker('option', 'minDate', fechaCierreMinima);
        $('#fechaCierre').datepicker('refresh');
    });

    /**
     * Se asigna funcionalidad a los botones para restablecer los campos de
     * fecha de apertura y fecha de cierre.
     */
    $('#borrarFechaApertura').click(function () {
        document.getElementById('fechaApertura').value = '';
    });

    $('#borrarFechaCierre').click(function () {
        document.getElementById('fechaCierre').value = '';
    });

    /**
     * La siguiente lista de funciones pertenece a la herramienta de edición de
     * campos del sistema.
     */
    
    /* * * * * * * * * *
     * CAMPO DE TEXTO  *
     * * * * * * * * * */
    $('#nuevoCampoTexto').click(function () {
        $('.div-editor').removeClass('oculto').not('#editorCampoTexto').addClass('oculto');
        $('.nuevo-campo').addClass('seleccionado').not('#nuevoCampoTexto').removeClass('seleccionado');
        $('.editor-cabecera').addClass('animacion').not('#cabeceraCampoTexto').removeClass('animacion');
    });
    
    $('#guardarCampoTexto').click(function () {
        var hayErrores = false;
        
        if ($.trim($('#tituloCampoTexto').val()) === '') {
            $('#guardarCampoTexto').addClass('animacion-error');
            $('#errorTituloCampoTexto').fadeIn(200).css('display', 'inline-block');
            
            hayErrores = true;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorTituloCampoTexto').css('display', 'none');
        }
        
        if (!$('input[name=subtipoCampoTexto]:checked').val()) {
            $('#guardarCampoTexto').addClass('animacion-error');
            $('#errorSubtipoCampoTexto').fadeIn(200).css('display', 'inline-block');
            
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
            $('#errorTituloCampoTexto').css('display', 'none');
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
            
            $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: center; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición arriba.\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición abajo.\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampoTexto(' + sessionStorage.getItem('id') + ')\" style=\"margin-right: 5px;\" title=\"Editar este campo.\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" title=\"Eliminar este campo del formulario.\"><span class=\"oi oi-trash\"></span></button></div>');
            $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
            $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(campoTexto);
            
            descartarCampoTexto();
        }
    });
    
    $('#descartarCampoTexto').click(function () {
        descartarCampoTexto();
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
    
    /* * * * * * * * * * *
     * LISTA DESPLEGABLE *
     * * * * * * * * * * */

    $('#nuevaListaDesplegable').click(function () {
        $('.div-editor').removeClass('oculto').not('#editorListaDesplegable').addClass('oculto');
        $('.nuevo-campo').addClass('seleccionado').not('#nuevaListaDesplegable').removeClass('seleccionado');
        $('.editor-cabecera').addClass('animacion').not('#cabeceraListaDesplegable').removeClass('animacion');
    });

        /* Funciones para agregar y eliminar elementos de la lista desplegable. */

        $('#agregarOpcionLista').click(function () {
            /* Se obtiene el ID numérico del último campo. */
            var idOpcionNueva = parseInt($('#opcionesListaDesplegable input:last').attr('id').substring(12)) + 1;
            
            if (idOpcionNueva <= 50) {
                var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Opción ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');
                
                $('#opcionesListaDesplegable').append(opcionNueva);
                $("#opcionesListaDesplegable").scrollTop($("#opcionesListaDesplegable")[0].scrollHeight);
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

    $('#guardarListaDesplegable').click(function () {
        var hayErrores = false;
        
        if ($.trim($('#tituloListaDesplegable').val()) === '') {
            $('#guardarListaDesplegable').addClass('animacion-error');
            $('#errorTituloListaDesplegable').fadeIn(200).css('display', 'inline-block');
            
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
                $('#errorOpcionesListaDesplegable').fadeIn(200).css('display', 'inline-block');
                
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
                
                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloListaDesplegable').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionListaDesplegable').val() + '</p><select class=\"campo-editor\" disabled>' + listaOpciones + '</select>');
            } else {
                listaDesplegable = {tipoCampo: "ListaDesplegable", titulo: $('#tituloListaDesplegable').val(), descripcion: $('#descripcionListaDesplegable').val(), obligatorio: 0, opciones: []};
                
                while (opciones.length > 0) {
                    var opcion = opciones.shift().value;
                    
                    if (listaDesplegable.opciones.indexOf(opcion) === -1) {
                        listaOpciones = listaOpciones + '<option value=\"' + opcion + '\">' + opcion + '</option>';
                        listaDesplegable.opciones.push(opcion);
                    }
                }
                
                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloListaDesplegable').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionListaDesplegable').val() + '</p><select class=\"campo-editor\" disabled>' + listaOpciones + '</select>');
            }
            
            listaDesplegable = JSON.stringify(listaDesplegable);
            
            $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: center; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición arriba.\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición abajo.\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarListaDesplegable(' + sessionStorage.getItem('id') + ')\" style=\"margin-right: 5px;\" title=\"Editar este campo.\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" title=\"Eliminar este campo del formulario.\"><span class=\"oi oi-trash\"></span></button></div>');
            
            $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
            $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(listaDesplegable);
            
            descartarListaDesplegable();
        }
    });
    
    $('#descartarListaDesplegable').click(function () {
        descartarListaDesplegable();
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
    
    /* * * * * * * * * * *
     * SELECTOR DE FECHA *
     * * * * * * * * * * */
    
    $('#nuevoCampoFecha').click(function () {
        $('.div-editor').removeClass('oculto').not('#editorCampoFecha').addClass('oculto');
        $('.nuevo-campo').addClass('seleccionado').not('#nuevoCampoFecha').removeClass('seleccionado');
        $('.editor-cabecera').addClass('animacion').not('#cabeceraCampoFecha').removeClass('animacion');
    });
    
    $('#guardarCampoFecha').click(function () {
        var campoFecha;
        
        if ($.trim($('#tituloCampoFecha').val()) === '') {
            $('#guardarCampoFecha').addClass('animacion-error');
            $('#errorTituloCampoFecha').fadeIn(200).css('display', 'inline-block');
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
            
            $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: center; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición arriba.\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición abajo.\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCampoFecha(' + sessionStorage.getItem('id') + ')\" style=\"margin-right: 5px;\" title=\"Editar este campo.\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" title=\"Eliminar este campo del formulario.\"><span class=\"oi oi-trash\"></span></button></div>');
            
            $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
            $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(campoFecha);

            descartarCampoFecha();
        }
    });
    
    $('#descartarCampoFecha').click(function () {
        descartarCampoFecha();
    });
    
    function descartarCampoFecha() {
        $('#tituloCampoFecha').val('');
        $('#descripcionCampoFecha').val('');
        $('#obligatorioCampoFecha').prop('checked', false);
    }
    
    /* * * * * * * * * * * * * * *
     * CASILLAS DE VERIFICACIÓN  *
     * * * * * * * * * * * * * * */

    $('#nuevaListaVerificacion').click(function () {
        $('.div-editor').removeClass('oculto').not('#editorCasillasVerificacion').addClass('oculto');
        $('.nuevo-campo').addClass('seleccionado').not('#nuevaListaVerificacion').removeClass('seleccionado');
        $('.editor-cabecera').addClass('animacion').not('#cabeceraCasillasVerificacion').removeClass('animacion');
    });
    
        /* Funciones para agregar y eliminar elementos de la lista de verificación. */

        $('#agregarCasillaVerificacion').click(function () {
            /* Se obtiene el ID numérico del último campo. */
            var idOpcionNueva = parseInt($('#opcionesCasillasVerificacion input:last').attr('id').substring(12)) + 1;
            
            if (idOpcionNueva <= 20) {
                var opcionNueva = $('<input class=\"campo-editor\" id=\"opcionNumero' + idOpcionNueva + '\" maxlength=\"40\" placeholder=\"Casilla de verificación ' + idOpcionNueva + '\" style=\"margin-top: 5px;\" type=\"text\"/>');
                
                $('#opcionesCasillasVerificacion').append(opcionNueva);
                $("#opcionesCasillasVerificacion").scrollTop($("#opcionesCasillasVerificacion")[0].scrollHeight);
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

    $('#guardarCasillasVerificacion').click(function () {
        var hayErrores = false;
        
        if ($.trim($('#tituloCasillasVerificacion').val()) === '') {
            $('#guardarCasillasVerificacion').addClass('animacion-error');
            $('#errorTituloCasillasVerificacion').fadeIn(200).css('display', 'inline-block');
            
            hayErrores = true;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorTituloCasillasVerificacion').css('display', 'none');
        }
        
        var opciones = document.getElementById('opcionesCasillasVerificacion').getElementsByTagName('input');
        opciones = Array.from(opciones);
        
        for (var i = 0; i < opciones.length; i++) {
            if ($.trim(opciones[i].value) === '') {
                $('#guardarCasillasVerificacion').addClass('animacion-error');
                $('#errorOpcionesCasillasVerificacion').fadeIn(200).css('display', 'inline-block');
                
                hayErrores = true;
                
                break;
            } else {
                /* Si el usuario corrije este problema, se oculta el error. */
                $('#errorOpcionesCasillasVerificacion').css('display', 'none');
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

            $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloCasillasVerificacion').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionCasillasVerificacion').val() + '</p>' + listaOpciones);
            $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: center; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición arriba.\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición abajo.\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarCasillasVerificacion(' + sessionStorage.getItem('id') + ')\" style=\"margin-right: 5px;\" title=\"Editar este campo.\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" title=\"Eliminar este campo del formulario.\"><span class=\"oi oi-trash\"></span></button></div>');
            
            listaCheckbox = JSON.stringify(listaCheckbox);
            
            $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
            $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(listaCheckbox);
            
            descartarCasillasVerificacion();
        }
    });
    
    $('#descartarCasillasVerificacion').click(function () {
        descartarCasillasVerificacion();
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
    
    /* * * * * * * * *
     * ÁREA DE TEXTO *
     * * * * * * * * */
    
    $('#limiteAreaTexto').on({
        keydown: function (event) {
            if (event.keyCode === 32) {
                return false;
            }
        },

        paste: function (event) {
            event.preventDefault();
            
            var contenidoPortapapeles = parseInt(event.originalEvent.clipboardData.getData('Text'));
            
            /**
             * Se asigna el contenido del portapapeles al campo solo si dicho
             * contenido es un número entero.
             */
            if (Number.isInteger(contenidoPortapapeles)) {
                $(this).val(contenidoPortapapeles);
            }
        }
    });
    
    $('#nuevaAreaTexto').click(function () {
        $('.div-editor').removeClass('oculto').not('#editorAreaTexto').addClass('oculto');
        $('.nuevo-campo').addClass('seleccionado').not('#nuevaAreaTexto').removeClass('seleccionado');
        $('.editor-cabecera').addClass('animacion').not('#cabeceraAreaTexto').removeClass('animacion');
    });
    
    $('#guardarAreaTexto').click(function () {
        var hayErrores = false;
        
        if ($.trim($('#tituloAreaTexto').val()) === '') {
            $('#guardarAreaTexto').addClass('animacion-error');
            $('#errorTituloAreaTexto').fadeIn(200).css('display', 'inline-block');
            
            hayErrores = true;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorTituloAreaTexto').css('display', 'none');
        }
        
        var limiteAreaTexto = parseInt($('#limiteAreaTexto').val());
        
        if (limiteAreaTexto === '' || !Number.isInteger(limiteAreaTexto) || limiteAreaTexto < 100 || limiteAreaTexto > 500) {
            $('#guardarAreaTexto').addClass('animacion-error');
            $('#errorLimiteAreaTexto').fadeIn(200).css('display', 'inline-block');
            
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
            
            $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: center; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición arriba.\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición abajo.\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarAreaTexto(' + sessionStorage.getItem('id') + ')\" style=\"margin-right: 5px;\" title=\"Editar este campo.\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" title=\"Eliminar este campo del formulario.\"><span class=\"oi oi-trash\"></span></button></div>');
            
            $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
            $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(areaTexto);

            descartarAreaTexto();
        }
    });
    
    $('#descartarAreaTexto').click(function () {
        descartarAreaTexto();
    });
    
    function descartarAreaTexto() {
        $('#tituloAreaTexto').val('');
        $('#descripcionAreaTexto').val('');
        $('#obligatorioAreaTexto').prop('checked', false);
        $('#limiteAreaTexto').val('');
    }
    
    /* * * * * * * * * * *
     * BOTONES DE RADIO  *
     * * * * * * * * * * */

    $('#nuevaListaRadio').click(function () {
        $('.div-editor').removeClass('oculto').not('#editorBotonesRadio').addClass('oculto');
        $('.nuevo-campo').addClass('seleccionado').not('#nuevaListaRadio').removeClass('seleccionado');
        $('.editor-cabecera').addClass('animacion').not('#cabeceraBotonesRadio').removeClass('animacion');
    });
    
        /* Funciones para agregar y eliminar elementos de la lista de verificación. */

        $('#agregarBotonRadio').click(function () {
            /* Se obtiene el ID numérico del último campo. */
            var idOpcionNueva = parseInt($('#opcionesBotonesRadio input:last').attr('id').substring(12)) + 1;
            
            if (idOpcionNueva <= 20) {
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

    $('#guardarBotonesRadio').click(function () {
        var hayErrores = false;
        
        if ($.trim($('#tituloBotonesRadio').val()) === '') {
            $('#guardarBotonesRadio').addClass('animacion-error');
            $('#errorTituloBotonesRadio').fadeIn(200).css('display', 'inline-block');
            
            hayErrores = true;
        } else {
            /* Si el usuario corrije este problema, se oculta el error. */
            $('#errorTituloBotonesRadio').css('display', 'none');
        }
        
        var opciones = document.getElementById('opcionesBotonesRadio').getElementsByTagName('input');
        opciones = Array.from(opciones);
        
        for (var i = 0; i < opciones.length; i++) {
            if ($.trim(opciones[i].value) === '') {
                $('#guardarBotonesRadio').addClass('animacion-error');
                $('#errorOpcionesBotonesRadio').fadeIn(200).css('display', 'inline-block');
                
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
                
                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloBotonesRadio').val() + '<span style=\"color: red; font-weight: bold;\">*</span></p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionBotonesRadio').val() + '</p>' + listaOpciones);
            } else {
                listaBotonRadio = {tipoCampo: "ListaBotonRadio", titulo: $('#tituloBotonesRadio').val(), descripcion: $('#descripcionBotonesRadio').val(), obligatorio: 0, opciones: []};
                
                while (opciones.length > 0) {
                    var opcion = opciones.shift().value;

                    if (listaBotonRadio.opciones.indexOf(opcion) === -1) {
                        listaOpciones = listaOpciones + '<label style=\"font-size: 13px;\"><input class=\"opcion-editor\" disabled type=\"radio\" value=\"' + opcion + '\"/> ' + opcion + '</label>';
                        listaBotonRadio.opciones.push(opcion);
                    }
                }
                
                $('#vistaPreviaFormulario').append('<div id=\"campoID' + sessionStorage.getItem('id') + '\" style=\"border-bottom: 1px solid #e9e9e9; display: block; padding-bottom: 10px;\"><p class=\"campo-cabecera\" style=\"font-size: 14px !important;\">' + $('#tituloBotonesRadio').val() + '</p><p class=\"campo-descripcion\" style=\"font-size: 13px !important;\">' + $('#descripcionBotonesRadio').val() + '</p>' + listaOpciones);
            }
            
            listaBotonRadio = JSON.stringify(listaBotonRadio);
            
            $('#botonesPreviaFormulario').append('<div id=\"accionesCampoID' + sessionStorage.getItem('id') + '\" style=\"align-items: center; height: ' + ($('#campoID' + sessionStorage.getItem('id')).height() + 11) + 'px; display: flex; justify-content: center;\"><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ARRIBA\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición arriba.\"><span class=\"oi oi-arrow-top\"></span></button><button class=\"btn btn-primary\" onclick=\"moverCampo(' + sessionStorage.getItem('id') + ', \'ABAJO\')\" style=\"margin-right: 5px;\" title=\"Mover este campo una posición abajo.\"><span class=\"oi oi-arrow-bottom\"></span></button><button class=\"btn btn-warning\" onclick=\"editarBotonesRadio(' + sessionStorage.getItem('id') + ')\" style=\"margin-right: 5px;\" title=\"Editar este campo.\"><span class=\"oi oi-pencil\"></span></button><button class=\"btn btn-danger\" onclick=\"eliminarCampo(' + sessionStorage.getItem('id') + ')\" title=\"Eliminar este campo del formulario.\"><span class=\"oi oi-trash\"></span></button></div>');
            
            $('#camposCreados').append('<input name=\"campoID' + sessionStorage.getItem('id') + '\" type=\"hidden\" value=\"\">');
            $('input[name=campoID' + sessionStorage.getItem('id') + ']').val(listaBotonRadio);
            
            descartarBotonesRadio();
        }
    });
    
    $('#descartarBotonesRadio').click(function () {
        descartarBotonesRadio();
    });
    
    function descartarBotonesRadio() {
        $('#tituloBotonesRadio').val('');
        $('#descripcionBotonesRadio').val('');
        $('#obligatorioBotonesRadio').prop('checked', false);
        
        var identificadorPrimeraOpcion = $('#opcionesBotonesRadio input:first').attr('id');
        var identificadorUltimaOpcion = $('#opcionesBotonesRadio input:last').attr('id');
        
        while (identificadorPrimeraOpcion !== identificadorUltimaOpcion) {
            $('#opcionesBotonesRadio input:last').remove();
            identificadorUltimaOpcion = $('#opcionesBotonesRadio input:last').attr('id');
        }
        
        $('#opcionesBotonesRadio input:first').val('');
    }
    
    /**
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
    
    /**
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
});

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
            
            if ($('#camposCreados').is(':empty')) {
                event.preventDefault();
                event.stopPropagation;
                
                $('#errorSinCampos').css('display', 'block');
            } else {
                $('#errorSinCampos').css('display', 'none');
            }
            
            $("html, body").animate({
                scrollTop: 0
            }, 750);

            formulario.classList.add('was-validated');
        }, false);
    }, false);
})();
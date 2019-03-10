/*
 * El código en este archivo corresponde a aquellas partes del sistema Colibrí
 * en las que se muestre un formulario.
 * 
 * @author Ariel Machini
 */

/*
 * Código correspondiente a los campos con selector de fecha.
 * Su función es cambiar el idioma del selector de fecha de jQuery al español.
 * 
 * NOTA: Sí, las líneas que comienzan con "$.datepicker" son correctas.
 * Ver más sobre el selector de fecha de jQuery en la siguiente página web:
 * http://api.jqueryui.com/datepicker/
 */
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

    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('*').datepicker('option', 'showAnim', 'fadeIn');
});
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
        var max = parseInt($(this).attr('max'));
        var min = parseInt($(this).attr('min'));
        if ($(this).val() > max) {
            $(this).val(max);
        } else if ($(this).val() < min) {
            $(this).val(min);
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
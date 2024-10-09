$(document).ready(function () {
    // Obtener la URL actual del navegador
    var currentUrl = window.location.pathname;

    // Comparar con la vista o archivo PHP que quieres verificar
    if (currentUrl.includes('sistema.php')) {

        id_terapeuta = $("#id_personal").val();

        $.ajax({
            cache: false,
            url: 'componentes/catalogos/cargar/cargar_datos_sistema.php',
            type: 'POST',
            dataType: 'json',
            data: {},
        }).done(function (resultado) {
            $("#citas_hoy").html(resultado.citas_hoy)
            $("#citas_sin_cobrar").html(resultado.citas_sin_cobrar)
        });
        // Realiza acciones específicas si la vista está activa
    }
});
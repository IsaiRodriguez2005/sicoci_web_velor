function cerrar_sesion(id_usuario)
{
    $.ajax({
        cache: false,
        url : 'componentes/login/cerrar_sesion.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_usuario': id_usuario},
    }).done(function(resultado){
        if(resultado == "sesion_close")
        {
            Swal.fire({
                icon: 'warning',
                title: 'Sesión finalizada!',
                showConfirmButton: false,
                timer: 2000
                }).then(function() {
                    window.location = 'index.php';
            });
        }
    });
}

function resetear(elemento)
{
    $("#"+elemento).removeClass('is-invalid');
}

function cerrar_modal(modal1, modal2) {
    if (modal1.length != 0) {
        //ocultamos el modal de agendar cita
        $('#' + modal1).modal('hide');
    }

    if (modal2.length != 0) {
        // abrimos el modal del cliente
        $("#" + modal2).modal("show");
    }
}

function recargarIgnorandoCache() {
    // Recargar la página ignorando la caché
    location.reload(true);
}
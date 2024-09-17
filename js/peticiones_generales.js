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


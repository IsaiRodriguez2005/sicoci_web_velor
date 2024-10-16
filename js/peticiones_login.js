function validar_login()
{
    var correo = $('#correo').val();
    var pass = $('#pass').val();
    var pin = $('#pin').val();
    var bandera = 1;
    if(correo.length == 0)
    {
        $("#correo").addClass('is-invalid');
        bandera = 2;
    }
    if(pass.length == 0)
    {
        $("#pass").addClass('is-invalid');
        bandera = 2;
    }
    if(pin.length == 0)
    {
        $("#pin").addClass('is-invalid');
        bandera = 2;
    }
    
    if(bandera == 1)
    {
        $.ajax({
            cache: false,
            url : 'componentes/login/validar_usuario.php',
            type : 'POST',
            dataType : 'html',
            data : { 'correo': correo, 'password': pass, 'pin': pin},
        }).done(function(resultado){
            console.log(resultado);
            if(resultado == "correcto")
            {
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario logeado!',
                    showConfirmButton: false,
                    timer: 2000
                    }).then(function() {
                        window.location = 'sistema.php';
                });
            }
            else
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Usuario no encontrado!',
                    html: 'Confirma los datos de acceso con el administrador del sistema',
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        });
    }
}

function resetear(elemento)
{
    $("#"+elemento).removeClass('is-invalid');
}

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
                    window.location = 'login.html';
            });
        }
    });
}

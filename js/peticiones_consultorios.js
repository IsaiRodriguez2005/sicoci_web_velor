function gestionar_consultorio( redireccion, modal_agenda = '', modal_cliente = '' )
{

    var nombre_consultorio = $('#nombre_consultorio').val();
    var tipo_gestion = $('#tipo_gestion').val();


    Swal.fire({
        title: 'Registrando consultorio...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url : "componentes/catalogos/registrar_consultorio.php",
        type : 'POST',
        dataType : 'html',
        data : { 'nombre': nombre_consultorio, 'id_consultorio': tipo_gestion},
    }).done(function(resultado){
        if(resultado == "ok")
        {
            Swal.fire({
                icon: "success",
                title: "Consultorio Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                if(redireccion){
                    window.location = redireccion ;
                }
                if(modal_agenda && modal_cliente){
                    $('#' + modal_cliente).modal('hide');
                    $("#" + modal_agenda).modal("show");
                    $("#tipo_gestion").val(0);
                }
            });
        }
        else
        {
            Swal.fire({
                icon: "warning",
                title: "Consultorio No Registrado",
                html: "La informaci&oacute;n no se logro registrar",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}
function gestionar_consultorio(redireccion, modal_agenda = '', modal_cliente = '' )
{

    var nombre_consultorio = $('#nombre_consultorio').val();
    var tipo_gestion = $('#tipo_gestion').val();
    
    if(nombre_consultorio.length == 0)
    {
        $("#nombre_consultorio").addClass("is-invalid");
        return false;
    }


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
                    $.ajax({
                        cache: false,
                
                        url: "componentes/catalogos/cargar_consultorios.php",
                        type: 'POST',
                        dataType: 'html',
                        data: { 'fecha_hora': fecha_hora_cita, },
                    }).done(function (resultado) {
                        //console.log(resultado)
                        $("#consultorio_form").html(resultado);
                    })
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
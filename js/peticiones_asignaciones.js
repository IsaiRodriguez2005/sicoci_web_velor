function gestionar_asignaciones()
{
    var bandera = 1;
    var emisor = $('#emisor').val();
    var modulo = $('#modulo').val();

    if(emisor == 0)
    {
        $("#emisor").addClass('is-invalid');
        bandera = 2;
    }
    if(modulo == 0)
    {
        $("#modulo").addClass('is-invalid');
        bandera = 2;
    }

    if(bandera == 1)
    {
        $.ajax({
            cache: false,
            url : "componentes/administrador/gestionar_asignaciones.php",
            type : 'POST',
            dataType : 'html',
            data : { 'id_emisor': emisor, 'id_modulo': modulo},
        }).done(function(resultado){
            if(resultado == "correcto")
            {
                Swal.fire({
                    icon: "success",
                    title: "Modulo asignado",
                    html: "La informaci&oacute;n se registro exitosamente",
                    showConfirmButton: false,
                    timer: 2000
                }).then(function() {
                    $.ajax({
                        cache: false,
                        url : "componentes/administrador/mostrar_modulos.php",
                        type : 'POST',
                        dataType : 'html',
                        data : { 'id_emisor': emisor},
                    }).done(function(resultado){
                        $("#mostrar_tabla").html(resultado);
                    });
                });
            }
            if(resultado == "incorrecto")
            {
                Swal.fire({
                    icon: "error",
                    title: "Modulo no asignado",
                    html: "No se logro realizar la asignaci&oacute;n del modulo",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
            if(resultado == "repetido")
            {
                Swal.fire({
                    icon: "warning",
                    title: "Modulo ya asignado",
                    html: "El modulo que seleccionaste ya se encuentra relacionado al emisor",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }
}

function ver_modulos()
{
    var id_emisor = $("#emisor").val();

    if(id_emisor != 0)
    {
        $.ajax({
            cache: false,
            url : "componentes/administrador/mostrar_modulos.php",
            type : 'POST',
            dataType : 'html',
            data : { 'id_emisor': id_emisor},
        }).done(function(resultado){
            $("#mostrar_tabla").html(resultado);
        });
    }
}

function actualizar_estatus_modulo(id_partida, id_emisor, estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/administrador/actualizar_estatus_emisor_modulo.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_partida': id_partida, 'id_emisor': id_emisor, 'estatus': estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            $.ajax({
                cache: false,
                url : "componentes/administrador/mostrar_modulos.php",
                type : 'POST',
                dataType : 'html',
                data : { 'id_emisor': id_emisor},
            }).done(function(resultado){
                $("#mostrar_tabla").html(resultado);
            });
        });
    }); 
}
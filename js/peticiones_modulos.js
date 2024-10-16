function gestionar_modulos()
{
    var tipo_gestion = $('#tipo_gestion').val();
    var modulo = $('#modulo').val();

    if(modulo.length == 0)
    {
        $("#modulo").addClass('is-invalid');
        return false;
    }

    $.ajax({
        cache: false,
        url : "componentes/administrador/gestionar_modulo.php",
        type : 'POST',
        dataType : 'html',
        data : { 'id_modulo': tipo_gestion, 'modulo': modulo},
    }).done(function(resultado){
        Swal.fire({
            icon: "success",
            title: "Modulo Registrado",
            html: "La informaci&oacute;n se registro exitosamente",
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = "modulos.php";
        });
    });
}

function editar_modulo(id_modulo, nombre_modulo)
{
    $("#leyenda").html("Modificar nombre del modulo");
    $("#tipo_gestion").val(id_modulo);
    $("#modulo").val(nombre_modulo);

    $("html, body").animate({ scrollTop: 0 }, 600);
}

function actualizar_estatus_modulo(id_modulo, estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/administrador/actualizar_estatus_modulo.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_modulo': id_modulo, 'estatus': estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'modulos.php';
        });
    }); 
}
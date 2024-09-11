function gestionar_timbres()
{
    var bandera = 1;
    var emisor = $('#emisor').val();
    var timbres = $('#timbres').val();
    var abono = $('input:radio[id=tipo_usuario]:checked').val()
    var quitar = $('input:radio[id=tipo_usuario2]:checked').val()

    if(emisor == 0)
    {
        $("#emisor").addClass('is-invalid');
        bandera = 2;
    }
    if(timbres.length == 0 || timbres == 0)
    {
        $("#timbres").addClass('is-invalid');
        bandera = 2;
    }

    if(bandera == 1)
    {
        $.ajax({
            cache: false,
            url : "componentes/administrador/gestionar_timbres.php",
            type : 'POST',
            dataType : 'html',
            data : { 'id_emisor': emisor, 'timbres': timbres, 'abono': abono, 'quitar': quitar},
        }).done(function(resultado){
            Swal.fire({
                icon: "success",
                title: "Timbres registrados",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = "timbres.php";
            });
        });
    }
}

function ver_timbres()
{
    var id_emisor = $("#emisor").val();

    if(id_emisor != 0)
    {
        $.ajax({
            cache: false,
            url : "componentes/administrador/mostrar_timbres.php",
            type : 'POST',
            dataType : 'html',
            data : { 'id_emisor': id_emisor},
        }).done(function(resultado){
            $("#mostrar_tabla").html(resultado);
        });
    }
}
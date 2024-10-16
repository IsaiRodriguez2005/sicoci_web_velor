function gestionar_documentos()
{
    var bandera = 1;
    var tipo_gestion = $('#tipo_gestion').val();
    var documento = $('#documento').val();
    var modulo = $('#modulo').val();

    if(documento.length == 0)
    {
        $("#documento").addClass('is-invalid');
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
            url : "componentes/administrador/gestionar_documento.php",
            type : 'POST',
            dataType : 'html',
            data : { 'id_documento': tipo_gestion, 'documento': documento, 'modulo': modulo},
        }).done(function(resultado){
            Swal.fire({
                icon: "success",
                title: "Documento Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = "documentos.php";
            });
        });
    }
}

function editar_documento(id_documento, nombre_documento, id_modulo)
{
    $("#leyenda").html("Modificar nombre del documento");
    $("#tipo_gestion").val(id_documento);
    $("#documento").val(nombre_documento);
    $("#modulo").val(id_modulo);

    $("html, body").animate({ scrollTop: 0 }, 600);
}

function actualizar_estatus_docto(id_documento, estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/administrador/actualizar_estatus_documento.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_documento': id_documento, 'estatus': estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'documentos.php';
        });
    }); 
}
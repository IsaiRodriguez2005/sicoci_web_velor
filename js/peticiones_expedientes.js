function actualizar_lista_clientes(){
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar_list_clientes.php",
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        //console.log(resultado)
        $("#clientes").html(resultado);
        $("#cliente_form").removeClass('is-invalid');
    })
}

function buscar_cliente(nombre_social) {

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/buscar_clientes.php',
        type: 'POST',
        data: { 'nombre_social': nombre_social.value }
    }).done(function (data) {
        //console.log(data)
        $("#id_cliente2").val(data);
    })
}

function mostrar_expedientes_clientes(){
    //alert($("#id_cliente2").val())

    id_cliente = $("#id_cliente2").val();
    if(Number(id_cliente)){

        $.ajax({
            cache: false,
            url: 'componentes/catalogos/buscar_clientes.php',
            type: 'POST',
            data: { 'id_cliente': id_cliente }
        }).done(function (data) {
            //console.log(data)
            $("#id_cliente2").val(data);
        })

    } else {
        alert('El Usuario no existe');
    }
    
}
function mostrar_stock_timbres()
{
    $.ajax({
        cache: false,
        url : "componentes/administrador/stock_minimo.php",
        type : 'POST',
        dataType : 'html',
    }).done(function(resultado){
        $("#emisores_stock").modal("show");
        $("#mostrar_emisores_stock").html(resultado);
    });
}

function mostrar_emisores_inactivos()
{
    $.ajax({
        cache: false,
        url : "componentes/administrador/emisores_inactivos.php",
        type : 'POST',
        dataType : 'html',
    }).done(function(resultado){
        $("#emisores_inactivos").modal("show");
        $("#mostrar_emisores_inactivos").html(resultado);
    });
}
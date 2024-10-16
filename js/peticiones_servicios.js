function deshabilitar()
{
    var valorSeleccionado = $('#tipo').val();
    if (valorSeleccionado === '1') { // Cuando se selecciona "Servicio"

        // valores 0 para el error de la base de datos
        $("#stock").val(0);
        $("#stock_minimo").val(0);

        $('#stock').prop('disabled', true);
        $('#stock_minimo').prop('disabled', true);
    } else if (valorSeleccionado === '2') { // Cuando se selecciona "Producto"

        // valor nulo para regresar el placeholder
        if( !$("#stock").val())
        {
            $("#stock").val(null);
        }
        if( !$("#stock_minimo").val())
        {
            $("#stock_minimo").val(null);
        }

        
        $('#stock').prop('disabled', false);
        $('#stock_minimo').prop('disabled', false);
    } else {
        // En caso de que no se haya seleccionado ninguna opción válida
        $('#stock').prop('disabled', false);
        $('#stock_minimo').prop('disabled', false);
    }
}


function activar_iva()
{
    $("#iva").val("16");
    $("#iva").prop("disabled", false);

    $("#retencion").val("0");
    $("#retencion").prop("disabled", false);
}

function desactivar_iva()
{
    $("#iva").val("0");
    $("#iva").prop("disabled", "disabled");

    $("#retencion").val("0");
    $("#retencion").prop("disabled", "disabled");
}

function buscar_clave(tipo)
{
    if(tipo == 1)
    {
        $("#modal-clave-producto").modal("show");
    }
    else
    {
        $("#modal-clave-medida").modal("show");
    }
}

function buscar_producto()
{
    var dato = $("#buscar_dato").val();
    var tipo = $("#buscar_por").val();
    $("#clave_producto").removeClass('is-invalid');
    
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/buscar_producto.php',
        type : 'POST',
        dataType : 'html',
        data : { 'dato': dato, 'tipo': tipo},
    }).done(function(resultado){
        $("#tabla_claves").html(resultado);
    });   
}

function buscar_medida()
{
    var dato = $("#buscar_dato2").val();
    var tipo = $("#buscar_por2").val();
    $("#clave_medida").removeClass('is-invalid');
    
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/buscar_medida.php',
        type : 'POST',
        dataType : 'html',
        data : { 'dato': dato, 'tipo': tipo},
    }).done(function(resultado){
        $("#tabla_medidas").html(resultado);
    });   
}

function relacionar_producto(clave)
{
    $("#clave_producto").val(clave);
    $("#modal-clave-producto").modal("hide");
}

function relacionar_medida(clave)
{
    $("#clave_medida").val(clave);
    $("#modal-clave-medida").modal("hide");
}

function gestionar_producto()
{
    var bandera = 1;
    var id_poducto = $("#tipo_gestion").val();
    var nombre = $("#nombre").val();
    var tipo = $("#tipo").val();
    var stock = $("#stock").val();
    var stock_minimo = $("#stock_minimo").val();
    var precio = $("#precio").val();
    var iva = $("#iva").val();

    if(nombre.length == 0)
    {
        $("#nombre").addClass('is-invalid');
        bandera = 2;
    }
    if(!tipo)
    {
        $("#tipo").addClass('is-invalid');
        bandera = 2;
    }

    // si elige SERVICIO no seran requeridos ni mandara alerta
    if(precio.length == 0 || precio == 0)
    {
        $("#precio").addClass('is-invalid');
        bandera = 2;
    }
    if(!iva)
    {
        $("#iva").addClass('is-invalid');
        bandera = 2;
    }

    if(tipo == '2')
    {
        if(!stock_minimo)
        {
            $("#stock_minimo").addClass('is-invalid');
            bandera = 2;
        }
        if(!stock_minimo)
        {
            $("#stock_minimo").addClass('is-invalid');
            bandera = 2;
        }
    }


    if(bandera == 1)
    {
        $.ajax({
            cache: false,
            url : 'componentes/catalogos/registrar_producto.php',
            type : 'POST',
            dataType : 'html',
            data : { 'tipo_gestion': id_poducto, 'nombre': nombre, 'tipo': tipo, 'stock': stock, 'stock_minimo': stock_minimo, 'precio': precio, 'iva': iva},
        }).done(function(resultado){
            if(resultado == "ok")
            {
                Swal.fire({
                    icon: "success",
                    title: "Se registro el producto/servicio",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            else if(resultado == "actualizado")
                {
                    Swal.fire({
                        icon: "success",
                        title: "Actializado",
                        html: "La informaci&oacute;n se actualizo exitosamente",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        window.location = 'servicios.php';
                    });
                }
            else
            {
                Swal.fire({
                    icon: "error",
                    //title: "No se registro el producto/servicio",
                    title: resultado,
                    showConfirmButton: true,
                    //timer: 1500
                });
            }
        });   
    }
}

function editar_producto(id_producto, nombre, tipo, stock, stock_minimo, precio, iva)
{
    $("#leyenda").html("Modificar datos del producto/servicio");
    $("html, body").animate({ scrollTop: 0 }, 600);


    $("#tipo_gestion").val(id_producto);
    $("#nombre").val(nombre);
    
    $("#tipo").val(tipo);
    $("#stock").val(stock);
    $("#stock_minimo").val(stock_minimo);
    $("#precio").val(precio);
    $("#iva").val(iva);

    deshabilitar();

    $("#modal_productos").modal("hide");
}

function actualizar_estatus_producto( id_poducto, codigo_estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/actualizar_estatus_producto.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_producto': id_poducto, 'codigo_estatus': codigo_estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'servicios.php';
        });
    }); 
}

function eliminar_producto(id_producto)
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/eliminar_producto.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_producto': id_producto},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Producto/Servicio eliminado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'servicios.php';
        });
    }); 
}


function ver_catalogo()
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/cargar_productos.php',
        type : 'POST',
        dataType : 'html',
    }).done(function(resultado){
        $("#vista_productos").html(resultado);
        $("#modal_productos").modal("show");
    }); 
}
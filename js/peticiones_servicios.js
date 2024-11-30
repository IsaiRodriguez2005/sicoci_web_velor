function deshabilitar() {
    var valorSeleccionado = $('#tipo').val();
    if (valorSeleccionado === '1') { // Cuando se selecciona "Servicio"

        // valores 0 para el error de la base de datos
        $("#stock").val(0);
        $("#stock_minimo").val(0);

        $('#stock').prop('disabled', true);
        $('#stock_minimo').prop('disabled', true);
    } else if (valorSeleccionado === '2') { // Cuando se selecciona "Producto"

        // valor nulo para regresar el placeholder
        if (!$("#stock").val()) {
            $("#stock").val(null);
        }
        if (!$("#stock_minimo").val()) {
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
function activar_iva() {
    $("#iva").val("16");
    $("#iva").prop("disabled", false);

    $("#retencion").val("0");
    $("#retencion").prop("disabled", false);
}
function desactivar_iva() {
    $("#iva").val("0");
    $("#iva").prop("disabled", "disabled");

    $("#retencion").val("0");
    $("#retencion").prop("disabled", "disabled");
}

function buscar_clave(tipo) {
    if (tipo == 1) {
        $("#modal-clave-producto").modal("show");
    }
    else {
        $("#modal-clave-medida").modal("show");
    }
}

function buscar_producto() {
    var dato = $("#buscar_dato").val();
    var tipo = $("#buscar_por").val();
    $("#clave_producto").removeClass('is-invalid');

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/buscar_producto.php',
        type: 'POST',
        dataType: 'html',
        data: { 'dato': dato, 'tipo': tipo },
    }).done(function (resultado) {
        $("#tabla_claves").html(resultado);
    });
}

function buscar_medida() {
    var dato = $("#buscar_dato2").val();
    var tipo = $("#buscar_por2").val();
    $("#clave_medida").removeClass('is-invalid');

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/buscar_medida.php',
        type: 'POST',
        dataType: 'html',
        data: { 'dato': dato, 'tipo': tipo },
    }).done(function (resultado) {
        $("#tabla_medidas").html(resultado);
    });
}

function relacionar_producto(clave) {
    $("#clave_producto").val(clave);
    $("#modal-clave-producto").modal("hide");
}

function relacionar_medida(clave) {
    $("#clave_medida").val(clave);
    $("#modal-clave-medida").modal("hide");
}

function gestionar_producto() {

    const id_producto = $("#tipo_gestion").val();
    const nombre = $("#nombre").val();
    const tipo = $("#tipo").val();
    const stock = $("#stock").val();
    const stock_minimo = $("#stock_minimo").val();
    const precio = $("#precio").val();
    const iva = $("#iva").val();
    const camposFaltantes = [];

    if (!nombre) {
        $("#nombre").addClass('is-invalid');
        camposFaltantes.push('Nombre del Producto');
    }

    if (!tipo) {
        $("#tipo").addClass('is-invalid');
        camposFaltantes.push('Tipo de Producto');
    }

    if (!precio || precio == 0) {
        $("#precio").addClass('is-invalid');
        camposFaltantes.push('Precio');
    }

    if (!iva) {
        $("#iva").addClass('is-invalid');
        camposFaltantes.push('IVA');
    }

    if (tipo == '2') {
        if (!stock) {
            $("#stock").addClass('is-invalid');
            camposFaltantes.push('Stock');
        }

        if (!stock_minimo) {
            $("#stock_minimo").addClass('is-invalid');
            camposFaltantes.push('Stock Mínimo');
        }
    }

    if (camposFaltantes.length > 0) {
        Swal.fire({
            icon: "warning",
            title: "Por favor, complete los siguientes campos obligatorios:",
            html: camposFaltantes.join(", "),
            showConfirmButton: true,
        });
        return;
    }


    $.ajax({
        cache: false,
        url: 'componentes/catalogos/registrar_producto.php',
        type: 'POST',
        dataType: 'html',
        data: { 'tipo_gestion': id_producto, 'nombre': nombre, 'tipo': tipo, 'stock': stock, 'stock_minimo': stock_minimo, 'precio': precio, 'iva': iva },
    }).done(function (resultado) {
        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Se registro el producto/servicio",
                showConfirmButton: false,
                timer: 1500
            }).then(function () {
                window.location = 'servicios.php';
            });
        }
        else if (resultado == "actualizado") {
            Swal.fire({
                icon: "success",
                title: "Actializado",
                html: "La informaci&oacute;n se actualizo exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = 'servicios.php';
            });
        }
        else {
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

function editar_producto(id_producto, nombre, tipo, stock, stock_minimo, precio, iva) {
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

function actualizar_estatus_producto(id_poducto, codigo_estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar_estatus_producto.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_producto': id_poducto, 'codigo_estatus': codigo_estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            recargar_tabla_prod_serv(id_poducto, 2);
        });
    });
}

function eliminar_producto(id_producto) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/eliminar_producto.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_producto': id_producto },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Producto/Servicio eliminado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            window.location = 'servicios.php';
        });
    });
}


function ver_catalogo() {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_productos.php',
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#vista_productos").html(resultado);
        $("#modal_productos").modal("show");
    });
}

function recargar_tabla_prod_serv(id_producto, tipo = 1) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_productos.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'id_producto': id_producto,
            'tipo': tipo
        },
    }).done(function (resultado) {
        $("#tr_prod_" + id_producto).replaceWith(resultado);
    });
}

function calcular_precio_neto() {
    //* esat funcion, calcula de precio bruto a precio neto 
    const iva = Number($("#iva").val());
    const precioBruto = Number($("#precio_bruto").val());
    const inputPrecio = $("#precio");

    if(precioBruto && precioBruto != 0){
        const precioNeto = (100 * precioBruto) / (100 + iva);
        inputPrecio.val(precioNeto);
    } else {
        inputPrecio.val('');
    }
}

function calcular_precio_bruto() {
    //* esta funcion, calcula de precio neto a precio bruto
    const iva = Number($("#iva").val());
    const precioNeto = Number($("#precio").val());
    const inputPrecio = $("#precio_bruto");

    if(precioNeto && precioNeto != 0){
        const precioBruto = ((100 + iva) * precioNeto) / 100;
        inputPrecio.val(precioBruto);
    } else {
        inputPrecio.val('');
    }

}

function app_iva() {
    //* esta funcion, se basa en dependiendo de lo que haya seleccionado o rellenado primero el usuario
    //* ya sea por pn o pb.

    const precioBruto = Number($("#precio_bruto").val());
    const precioNeto = Number($("#precio").val());

    if (precioNeto && precioBruto) {
        calcular_precio_bruto();
    } else if (precioBruto) {
        calcular_precio_neto();
    } else if (precioNeto) {
        calcular_precio_bruto();
    }

}
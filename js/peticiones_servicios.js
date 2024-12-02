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

    const claveProductoServicio = $("#clave_sat").val();
    const claveUnidadMedida = $("#clave_unidad_medida").val();

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

    if (!claveProductoServicio) {
        $("#search_clave_sat").addClass('is-invalid');
        camposFaltantes.push('Clave de Producto/Servicio');
    }
    if (!claveUnidadMedida) {
        $("#search_clave_unidad_medida").addClass('is-invalid');
        camposFaltantes.push('Unidad de medida');
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
        data: {
            'tipo_gestion': id_producto,
            'nombre': nombre,
            'tipo': tipo,
            'stock': stock,
            'stock_minimo': stock_minimo,
            'precio': precio,
            'iva': iva,
            'clave_producto': claveProductoServicio,
            'clave_medida': claveUnidadMedida,
        },
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

async function editar_producto(id_producto, nombre, tipo, stock, stock_minimo, precio, iva, claveProSAT, claveMedSAT) {
    $("#leyenda").html("Modificar datos del producto/servicio");
    $("html, body").animate({ scrollTop: 0 }, 600);


    $("#tipo_gestion").val(id_producto);
    $("#nombre").val(nombre);

    $("#tipo").val(tipo);
    $("#stock").val(stock);
    $("#stock_minimo").val(stock_minimo);
    $("#precio").val(precio);
    $("#iva").val(iva);

    await cargar_para_editar_claves(claveProSAT, claveMedSAT);

    deshabilitar();

    $("#modal_productos").modal("hide");
}

async function cargar_para_editar_claves(claveProd, claveMed) {
    return await $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/datos/claves_producto_sat.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'claveProducto': claveProd,
            'claveMedida': claveMed
        },
    }).done(function (resultado) {
        const { medida: {
                        clave_medida, 
                        descr_medida
                }, 
                producto: {
                        clave_producto,
                        descr_producto
                }
            } = resultado;

        const inputProducto = $("#search_clave_sat");
        const hiddenInputProducto = $("#clave_sat");
        const inputMedida = $("#search_clave_unidad_medida");
        const hiddenInputMedida = $("#clave_unidad_medida");

        inputProducto.val(`[${clave_producto}]${descr_producto}`);
        inputMedida.val(`[${clave_producto}]${descr_medida}`);
        hiddenInputProducto.val(clave_producto);
        hiddenInputMedida.val(clave_medida);
    });
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

    if (precioBruto && precioBruto != 0) {
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

    if (precioNeto && precioNeto != 0) {
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


//TODO: funciones de datos del SAT

//! CLAVES DEL SAT  [ PRODUCTOS/SERVICIOS ]
async function cargar_claves_sat(descrip) {
    //* esta funcion solamente devuelve las claves en json 
    const data = await $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/claves_sat.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'funcion': 'cargarClavesSAT',
            'descripcion': descrip,
        },
    });
    const { success, claves } = data;
    if (success) {
        return claves;
    }
}

function actualizar_lista_claves(claves) {

    const ul = $("#suggestions_calve_sat");
    ul.empty(); // limpiamos

    if (!claves) {
        ul.addClass('hidden');
        return;
    }

    claves.forEach(clave => {
        const li = $(`<li data-value="${clave.clave}">[${clave.clave}] ${clave.descripcion}</li>`);
        ul.append(li);
    });

    ul.removeClass('hidden');
}

function filtrar_lista_clave_sat() {

    const input = $("#search_clave_sat");
    cargar_claves_sat(input.val()).then(actualizar_lista_claves);
    const filter = input.val().toLowerCase();
    const ul = $("#suggestions_calve_sat");
    const li = ul.find('li');
    let hasVisibleItems = false;

    li.each(function () {
        const textValue = $(this).text().toLowerCase(); // Obtén el texto del <li>
        if (textValue.indexOf(filter) > -1) {
            $(this).show(); // Muestra el <li> si coincide con el filtro
            hasVisibleItems = true;
        } else {
            $(this).hide(); // Oculta el <li> si no coincide
        }
    });

    // Oculta la lista completa si no hay elementos visibles
    ul.toggleClass('hidden', !hasVisibleItems);
}

$("#suggestions_calve_sat").on("click", "li", function () {
    const input = $("#search_clave_sat");
    const hiddenInput = $("#clave_sat");
    const selectedValue = $(this).data("value"); // Obtiene el valor del atributo 'data-value' del <li>
    const selectedText = $(this).text(); // Obtiene el valor del atributo 'data-value' del <li>

    input.val(selectedText);
    hiddenInput.val(selectedValue);

    $("#suggestions_calve_sat").addClass("hidden"); // Oculta la lista de sugerencias
});

$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-container").length) {
        $("#suggestions_calve_sat").addClass("hidden");
    }
});

//! CLAVES DEL SAT  [ UNIDAD DE MEDIDA ]

async function cargar_claves_unidad_medida(descrip) {
    //* esta funcion solamente devuelve las claves en json 
    const data = await $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/claves_sat.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'funcion': 'cargarClavesUnidadMedidaSAT',
            'descripcion': descrip,
        },
    });
    const { success, claves } = data;
    if (success) {
        return claves;
    }
}

function actualizar_lista_unidad_medida(claves) {

    const ul = $("#suggestions_clave_unidad_medida");
    ul.empty(); // limpiamos

    if (!claves) {
        ul.addClass('hidden');
        return;
    }

    claves.forEach(clave => {
        const li = $(`<li data-value="${clave.clave}">[${clave.clave}] ${clave.descripcion}</li>`);
        ul.append(li);
    });

    ul.removeClass('hidden');
}

function filtrar_lista_unidad_medida_sat() {

    const input = $("#search_clave_unidad_medida");
    cargar_claves_unidad_medida(input.val()).then(actualizar_lista_unidad_medida);
    const filter = input.val().toLowerCase();
    const ul = $("#suggestions_clave_unidad_medida");
    const li = ul.find('li');
    let hasVisibleItems = false;

    li.each(function () {
        const textValue = $(this).text().toLowerCase(); // Obtén el texto del <li>
        if (textValue.indexOf(filter) > -1) {
            $(this).show(); // Muestra el <li> si coincide con el filtro
            hasVisibleItems = true;
        } else {
            $(this).hide(); // Oculta el <li> si no coincide
        }
    });

    // Oculta la lista completa si no hay elementos visibles
    ul.toggleClass('hidden', !hasVisibleItems);
}

$("#suggestions_clave_unidad_medida").on("click", "li", function () {
    const input = $("#search_clave_unidad_medida");
    const hiddenInput = $("#clave_unidad_medida");
    const selectedValue = $(this).data("value"); // Obtiene el valor del atributo 'data-value' del <li>
    const selectedText = $(this).text(); // Obtiene el valor del atributo 'data-value' del <li>

    input.val(selectedText);
    hiddenInput.val(selectedValue);

    $("#suggestions_clave_unidad_medida").addClass("hidden"); // Oculta la lista de sugerencias
});

$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-container").length) {
        $("#suggestions_clave_unidad_medida").addClass("hidden");
    }
});
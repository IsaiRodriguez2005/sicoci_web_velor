//TODO: funciones de apollo
function pantallaCarga(title, text) {
    return Swal.fire({
        title: title,
        text: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}
function mensajeSuccess() {
    return Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'El producto se agregó correctamente al ticket.',
    });
}
function mensajeError(mensaje, title = null,) {

    if (title == null) title = 'Error';

    return Swal.fire({
        icon: 'error',
        title: title,
        text: mensaje,
    });
}

//todo: [INICIA] Ejecutar la función automáticamente al cargar la página
document.addEventListener('DOMContentLoaded', procesarDatosTicket);

async function procesarDatosTicket() {
    //* Obtener los parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);

    const folioTicket = urlParams.get('folio_ticket');
    const idDocumento = urlParams.get('id_documento');

    if (!folioTicket || !idDocumento) {
        console.error('Faltan parámetros en la URL.');
        return;
    }

    cargarDatosTicket(folioTicket, idDocumento);
    await cargarTablaProductosTicket();

    const data_productos = await cargar_productos_servicios();

    const { productos, success } = data_productos;

    if (success) {
        const ul = $("#suggestions");
        ul.empty();
        productos.forEach(producto => {
            const li = `<li data-value="${producto.id_producto}">${producto.nombre}</li>`;
            ul.append(li);
        });
    }

}

async function cargarDatosTicket(folio, idDocumento) {

    const datosTicket = await obtenerDatosTicket(folio, idDocumento);
    const {
        clave_serie,
        folio_ticket,
        nombre_cliente,
        total,
        estatus
    } = datosTicket;

    let textEstatus = obtenerTxtEstatus(estatus);


    //* folios
    $("#text_serie").html(`Serie: ${clave_serie}`);
    $("#text_folio_ticket").html(folio_ticket);
    $("#text_folio_href").html(`#${clave_serie}${folio_ticket}`);
    //* datos del cliente
    $("#text_cliente").html('<b>CLIENTE: </b>' + nombre_cliente);

    //* datos totales
    $("#text_total").text(`$${total}`);
    $("#text_cobrar").text(`$${total}`);
    $("#text_total_tabla").text(`$${total}`);

    //* datos estados     
    $("#texto_estado").html(`<span class="font-weight-bold">Estado:</span> ${textEstatus}`);

}

async function obtenerDatosTicket(folioTicket, idDocumento) {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'getTextosTickets',
                'idDocumento': idDocumento,
                'folioTicket': folioTicket,
            },
            dataType: "json",
        });

        const { success, datosTicket } = respuesta;

        if (!success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message || "Ocurrió un error al procesar la solicitud"
            });
            throw new Error("Error en la respuesta: " + (respuesta.error || "Desconocido"));
        }


        return datosTicket;
    } catch (error) {
        console.error("Error en la petición:", error);

        Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo realizar la solicitud. Intenta nuevamente."
        });

        throw error;
    }
}

async function cargar_productos_servicios() {
    const resProductos = await $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/productos.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'funcion': 'cargarProdutos',
        },
    });

    return resProductos;
}

function obtenerTxtEstatus(estatus) {
    switch (estatus) {
        case 1:
            return textEstatus = 'APERTURADO';
        case 2:
            return textEstatus = 'GENERADO';
        case 3:
            return textEstatus = 'CANCELADO';
        case 4:
            return textEstatus = 'COBRADO';
    }
}

//TODO: funciones del buscador de [productos]

let currentFocus = -1; // Índice del elemento actualmente enfocado

function filtrar_lista() {
    const input = $("#search");
    const filter = input.val().toLowerCase();
    const ul = $("#suggestions");
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

    // Reinicia el índice si la lista está visible
    if (hasVisibleItems) {
        currentFocus = -1;
    }
}

// Navegar y seleccionar sugerencias usando el teclado
$("#search").on("keydown", function (e) {
    const ul = $("#suggestions");
    const li = ul.find('li:visible'); // Solo elementos visibles
    const liCount = li.length;

    if (liCount === 0) return; // Salir si no hay elementos visibles

    if (e.key === "ArrowDown") {
        e.preventDefault(); // Evita el scroll de la página
        currentFocus = (currentFocus + 1) % liCount; // Incrementa el índice
        highlightSuggestion(li); // Resalta el elemento actual
    } else if (e.key === "ArrowUp") {
        e.preventDefault();
        currentFocus = (currentFocus - 1 + liCount) % liCount; // Decrementa el índice
        highlightSuggestion(li);
    } else if (e.key === "Enter") {
        e.preventDefault();
        if (currentFocus > -1) {
            $(li[currentFocus]).click(); // Simula el clic en el elemento actual
        }
    }
});

// Resaltar sugerencia seleccionada
function highlightSuggestion(li) {
    li.removeClass("active"); // Elimina la clase activa de todos
    if (currentFocus > -1) {
        $(li[currentFocus]).addClass("active"); // Agrega clase activa al actual
        // Opcional: Desplaza la lista para mostrar el elemento seleccionado
        li[currentFocus].scrollIntoView({ block: "nearest" });
    }
}

$("#suggestions").on("click keydown", "li", function (e) {
    if (e.type === "click" || (e.type === "keydown" && e.key === "Enter")) {
        // Aquí manejamos tanto el clic como la tecla Enter

        const input = $("#search");
        const inputCantidad = $("#cantidad_producto");

        const hiddenInput = $("#id_producto");
        const selectedValue = $(this).data("value"); // Obtiene el valor del atributo 'data-value' del <li>
        const selectedText = $(this).text(); // Obtiene el texto del <li>

        // Establecer valores en los inputs correspondientes
        input.val(selectedText);
        hiddenInput.val(selectedValue);

        // Ocultar sugerencias
        $("#suggestions").addClass("hidden");

        inputCantidad.select();
    }
});

// Manejar "Enter" en el input de cantidad
$("#cantidad_producto").on("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault(); // Prevenir comportamiento predeterminado
        agregarProducto(); // Llamar a la función agregarProducto
    }
});


// Opcional: Ocultar la lista de sugerencias si se hace clic fuera del contenedor
$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-container").length) {
        $("#suggestions").addClass("hidden");
    }
});

/*
// function cargar_info_producto(idProducto) {
//    esta funcion carga la info de los productos y dehabilita los campos para editarlos en la compra
//     $.ajax({
//         cache: false,
//         url: 'componentes/tickets/productos_servicios/productos.php',
//         type: 'POST',
//         dataType: 'json',
//         data: {
//             'funcion': 'cargarProductoPorId',
//             'id_producto': idProducto
//         },
//     }).done(function (resultado) {
//         const { producto, success } = resultado;

//         if (success) {
//             const inputPrecioNeto = $("#precio_neto");
//             const inputIVA = $("#iva");
//             const inputPrecioBruto = $("#precio_bruto");

//             inputPrecioNeto.prop("disabled", false);
//             inputIVA.prop("disabled", false);
//             inputPrecioBruto.prop("disabled", false);

//             const {
//                 iva,
//                 precio
//             } = producto[0];

//             inputPrecioNeto.val(precio);
//             inputIVA.val(iva);
//             calcular_precio_bruto();
//             app_total();
//         }
//     });
// }
*/

//TODO: funciones para agregar productos

async function agregarProducto() {

    const urlParams = new URLSearchParams(window.location.search);
    const productoId = $("#id_producto").val();
    const cantidad = $("#cantidad_producto").val();
    const folioTicket = urlParams.get('folio_ticket');
    const idDocumento = urlParams.get('id_documento');

    try {
        pantallaCarga('Cargando...', 'Por favor espera mientras se procesa tu solicitud.');

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'agregarProductoTicket',
                'productoId': productoId,
                'cantidad': cantidad,
                'folioTicket': folioTicket,
                'idDocumento': idDocumento,
            },
            dataType: "json",
        });

        const { success, producto, mensaje } = respuesta;
        Swal.close();

        if (!success) {
            mensajeError('Revisa la tabla de articulos', mensaje);
            return;
        }

        limpiarTablaVacia();
        agregarTuplaTablaTicket(producto);
        await limpiarInputsProductos();
        await cargarDatosTicket(folioTicket, idDocumento);

        mensajeSuccess();

    } catch (error) {
        Swal.close();
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(error);
    }
}

async function cargarTablaProductosTicket() {
    const productos = await obtenerProdutosTicket();
    const tbody = $('#table_productos_ticket tbody');

    tbody.empty();

    productos.forEach(producto => {
        agregarTuplaTablaTicket(producto, tbody);
    });

    
}

async function obtenerProdutosTicket() {
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get('folio_ticket');
    const idDocumento = urlParams.get('id_documento');

    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'getProductosTicket',
                'folioTicket': folioTicket,
                'idDocumento': idDocumento,
            },
            dataType: "json",
        });

        const { success, productTicket, mensaje } = respuesta;
        if (!success) {
            mensajeError(mensaje);
            return;
        }
        return productTicket;
    } catch (error) {
        Swal.close();
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(error);
    }
}

function agregarTuplaTablaTicket(producto, tbody = null) {

    if (!tbody) {
        tbody = $('#table_productos_ticket tbody');
    }

    const { id_producto, cantidad, nombreProducto, precio, importe } = producto;

    const cantidadFormat = parseFloat(cantidad).toFixed(2);
    const precioFormat = parseFloat(precio).toFixed(2);
    const importeFormat = parseFloat(importe).toFixed(2);

    const fila = `
                    <tr id="pro_tick_${id_producto}" class="gradeX">
                        <td class="p-t-0 p-b-0 text-center">
                            <button class="btn btn-danger btn-sm modalBorrar" 
                                            style="cursor: pointer;" 
                                            producto="${id_producto}" 
                                            onclick="eliminar_producto(${id_producto})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                        <td class="text-center">${cantidadFormat}</td>
                        <td class="text-center">${nombreProducto}</td>
                        <td class="text-center">$${precioFormat}</td>
                        <td class="text-center">$${importeFormat}</td>
                    </tr>
                `;
    tbody.append(fila);
}

function inizializar_tabla(idTabla) {
    $('#' + idTabla).DataTable().destroy();
    $('#' + idTabla).DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        responsive: true,
        deferRender: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
}

function limpiarTablaVacia() {
    const filaVacia = $(".dataTables_empty");
    if (filaVacia.length) {
        filaVacia.remove();
    }
}

function limpiarInputsProductos() {
    return new Promise((resolve) => {
        const input = $("#search");
        $("#id_producto").val("");
        $("#search").val("");
        $("#cantidad_producto").val("1");

        input.select();
        resolve();
    });
}

//TODO: eliminar productos y cancelaciones

async function eliminar_producto(idProducto) {
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get('folio_ticket');
    const idDocumento = urlParams.get('id_documento');

    try {
        pantallaCarga('Cargando...', 'Por favor espera mientras se elimina el producto.');

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'eliminarProductoTicket',
                'productoId': idProducto,
                'folioTicket': folioTicket,
                'idDocumento': idDocumento,
            },
            dataType: "json",
        });

        const { success, borrado } = respuesta;

        Swal.close();

        if (!success && !borrado) {
            mensajeError('Revisa la tabla de articulos', mensaje);
            return;
        }
        eliminar_tupla_tabla_ticket(idProducto)
        await cargarDatosTicket(folioTicket, idDocumento);
        mensajeSuccess();
    } catch (error) {
        Swal.close();
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(error);
    }
}

function eliminar_tupla_tabla_ticket(id_producto) {

    const fila = document.querySelector(`#pro_tick_${id_producto}`);
    if (fila) {
        fila.remove();
    }
}
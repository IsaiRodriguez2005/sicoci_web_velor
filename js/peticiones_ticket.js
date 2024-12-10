async function procesarDatosTicket() {
    //* Obtener los parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);

    const folioTicket = urlParams.get('folio_ticket');
    const idDocumento = urlParams.get('id_documento');
    const serieTicket = urlParams.get('serie_ticket');
    const idCita = urlParams.get('id_cita');
    const idCliente = urlParams.get('id_cliente');

    if (!folioTicket || !serieTicket || !idCita || !idCliente || !idDocumento) {
        console.error('Faltan parámetros en la URL.');
        return;
    }

    cargarDatosTicket(folioTicket, serieTicket, idCita, idCliente, idDocumento);

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

async function cargarDatosTicket(folio, serieTicket, idCita, idCliente, idDocumento) {

    const datosTicket = await obtenerDatosTicket(folio, serieTicket, idCita, idDocumento);
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

async function obtenerDatosTicket(folioTicket, serieTicket, idCita, idDocumento) {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'getTextosTickets',
                'serieTicket': serieTicket,
                'folioCita': idCita,
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

let currentFocus = -1; // Índice del elemento actualmente enfocado

// Filtrar lista de sugerencias al escribir en el campo de entrada
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

// Maneja el evento de clic en un elemento <li> para seleccionar una sugerencia
$("#suggestions").on("click", "li", function () {
    const input = $("#search");
    const hiddenInput = $("#id_producto");
    const selectedValue = $(this).data("value"); // Obtiene el valor del atributo 'data-value' del <li>
    const selectedText = $(this).text(); // Obtiene el texto del <li>

    input.val(selectedText);
    hiddenInput.val(selectedValue);
    // cargar_info_producto(selectedValue);

    $("#suggestions").addClass("hidden"); // Oculta la lista de sugerencias
});

// Opcional: Ocultar la lista de sugerencias si se hace clic fuera del contenedor
$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-container").length) {
        $("#suggestions").addClass("hidden");
    }
});



function cargar_info_producto(idProducto) {
    //* esta funcion carga la info de los productos y dehabilita los campos para editarlos en la compra
    $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/productos.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'funcion': 'cargarProductoPorId',
            'id_producto': idProducto
        },
    }).done(function (resultado) {
        const { producto, success } = resultado;

        if (success) {
            const inputPrecioNeto = $("#precio_neto");
            const inputIVA = $("#iva");
            const inputPrecioBruto = $("#precio_bruto");

            inputPrecioNeto.prop("disabled", false);
            inputIVA.prop("disabled", false);
            inputPrecioBruto.prop("disabled", false);

            const {
                iva,
                precio
            } = producto[0];

            inputPrecioNeto.val(precio);
            inputIVA.val(iva);
            calcular_precio_bruto();
            app_total();
        }
    });
}


async function agregarProducto() {
    // Obtén los valores de los inputs
    const productoId = $("#id_producto").val();
    const cantidad = $("#cantidad_producto").val();
    const folioTicket = urlParams.get('folio_ticket');
    const idDocumento = urlParams.get('id_documento');
    console.log([productoId, cantidad]);

    try {
        // Muestra la pantalla de carga
        Swal.fire({
            title: 'Cargando...',
            text: 'Por favor espera mientras se procesa tu solicitud.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Realiza la petición AJAX
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

        // Oculta la pantalla de carga
        Swal.close();

        // Maneja la respuesta
        if (respuesta.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'El producto se agregó correctamente al ticket.',
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al agregar el producto. Por favor, inténtalo nuevamente.',
            });
        }
    } catch (error) {
        // Oculta la pantalla de carga y maneja el error
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.',
        });
        console.error(error);
    }
}

//! Ejecutar la función automáticamente al cargar la página
document.addEventListener('DOMContentLoaded', procesarDatosTicket);

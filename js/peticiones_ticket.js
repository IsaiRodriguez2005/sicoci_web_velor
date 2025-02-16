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
        },
    });
}
function ocultarPantallaCarga() {
    Swal.close();
}
function mensajeSuccess(mensaje, title = "Éxito") {
    return Swal.fire({
        icon: "success",
        title: title,
        text: mensaje,
    });
}
function mensajeError(mensaje, title = null) {
    if (title == null) title = "Error";

    return Swal.fire({
        icon: "error",
        title: title,
        text: mensaje,
    });
}
function cerrarModal(modal) {

    $("#" + modal).modal().hide();
}
function abrirModal(modal) {
    $("#" + modal).modal().show();
}
function limpiarBackdrops() {
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open");
    $("body").css("padding-right", "");
}

//todo: Ejecutar la función automáticamente al cargar la página

//* Declarar variables globales
let folioTicket = null;
let idDocumento = null;
//? token para hacer consultas de tipos de cambio, siempre seran exactos
//? https://www.banxico.org.mx/SieAPIRest/service/v1/token
const tokenBM = `0bd8d4fa68bb29fc8f02b9c2d4a9a83919dfe423056179ef4851a05de2ceb78d`;
const serieUSD = "SF43718";
const serieCAD = "SF60632";
const urlBancoMexicoFetch = (serie, token) => {
    return `https://www.banxico.org.mx/SieAPIRest/service/v1/series/${serie}/datos/oportuno?token=${token}`;
}

//* Inicializar las variables globales al cargar la página
document.addEventListener("DOMContentLoaded", () => {

    const urlParams = new URLSearchParams(window.location.search);
    folioTicket = urlParams.get("folio_ticket");
    idDocumento = urlParams.get("id_documento");

    if (!folioTicket || !idDocumento) {
        console.error("Faltan parámetros en la URL.");
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Parámetros de ticket no encontrados en la URL.",
        });
    } else {
        procesarDatosTicket();
    }
});

async function procesarDatosTicket() {
    //* Obtener los parámetros de la URL

    pantallaCarga("Cargando datos...");

    if (!folioTicket || !idDocumento) {
        console.error("Faltan parámetros en la URL.");
        return;
    }

    await cargarTablaProductosTicket();
    await cargarDatosTicket(folioTicket, idDocumento);
    const data_productos = await cargar_productos_servicios();

    const { productos, success } = data_productos;

    if (success) {
        const ul = $("#suggestions");
        ul.empty();
        productos.forEach((producto) => {
            const li = `<li data-value="${producto.id_producto}">${producto.nombre}</li>`;
            ul.append(li);
        });
    }

    ocultarPantallaCarga();
}

async function cargarDatosTicket(folio, idDocumento) {
    const datosTicket = await obtenerDatosTicket(folio, idDocumento);
    const { estatus } = datosTicket;
    // console.log(datosTicket);

    //? diferentes acciones dependiendo el estatus:
    switch (estatus) {
        case 1:
            ticketAperturado(datosTicket);
            break;
        case 2:
            textEstatus = "GENERADO";
            bgColorClass = "bg-warning";
            break;
        case 3:
            ticketCancelado(datosTicket);
            break;
        case 4:
            ticketPagado(datosTicket);
            break;
        default:
            textEstatus = "DESCONOCIDO";
            bgColorClass = "bg-secondary";
    }
}

async function obtenerDatosTicket(folioTicket, idDocumento) {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                funcion: "getTextosTickets",
                idDocumento: idDocumento,
                folioTicket: folioTicket,
            },
            dataType: "json",
        });

        const { success, datosTicket } = respuesta;

        if (!success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text:
                    response.message ||
                    "Ocurrió un error al procesar la solicitud",
            });
            throw new Error(
                "Error en la respuesta: " + (respuesta.error || "Desconocido")
            );
        }

        return datosTicket;
    } catch (error) {
        console.error("Error en la petición:", error);

        Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo realizar la solicitud. Intenta nuevamente.",
        });

        throw error;
    }
}

async function cargar_productos_servicios() {
    const resProductos = await $.ajax({
        cache: false,
        url: "componentes/tickets/productos_servicios/productos.php",
        type: "POST",
        dataType: "json",
        data: {
            funcion: "cargarProdutos",
        },
    });

    return resProductos;
}

//TODO: formatos de ticket dependiendo del estado del mismo

function ticketAperturado(datos) {
    pantallaCarga("Cargando datos ticket...", "");
    const {
        clave_serie,
        folio_ticket,
        nombre_cliente,
        total,
        total_articulos,
        estatus,
        id_cliente,
        tctrans,
        tcefect,
        total_cobrado,
        total_descuento,
    } = datos;

    let totalPorCobrar = parseFloat(total) - parseFloat(total_cobrado);

    let { textEstatus, bgColorClass } = obtenerTxtEstatus(estatus);

    //* cabecera
    $("#text_serie").html(`Serie: ${clave_serie}`);
    $("#text_folio_ticket").html(folio_ticket);

    //* datos estados
    $("#texto_estado")
        .removeClass("bg-primary bg-warning bg-danger bg-success bg-secondary")
        .addClass(bgColorClass)
        .html(`<span class="font-weight-bold">Estado:</span> ${textEstatus}`);

    //* cliente
    $("#text_cliente").html("<b>CLIENTE: </b>" + nombre_cliente);
    $("#btn_eliminar_cliente").addClass("hidden");
    if (id_cliente !== 0) {
        $("#btn_eliminar_cliente").removeClass("hidden");
    }
    //* Tarjeta de datos de la compra
    $("#text_folio_href").html(`#${clave_serie}${folio_ticket}`);
    $("#total_articulos").text(`${Number(total_articulos)}`);
    $("#total_articulos").text(`${Number(total_articulos)}`);
    $("#tot_descuento").text(`$${total_descuento}`);
    $("#text_total").text(`$${total}`);
    // si ya abono
    if (parseFloat(tcefect) > 0 || parseFloat(tctrans) > 0)
        $(".separadores_pagos").removeClass("hidden");
    if (parseFloat(tcefect) > 0) {
        $("#cantidad_efectivo").text(`$${tcefect}`);
        $("#tr_efectivo").removeClass();
    }
    if (parseFloat(tctrans) > 0) {
        $("#cantidad_transferencia").text(`$${tctrans}`);
        $("#tr_transferencia").removeClass();
    }
    $("#total_cobrado").text(`$${parseFloat(total_cobrado).toFixed(2)}`);
    $("#text_cobrar").text(`$${totalPorCobrar.toFixed(2)}`);
    $("#text_total_tabla").text(`$${total}`);

    ocultarPantallaCarga();
}
function ticketPagado(datos) {
    pantallaCarga("Cargando datos ticket...", "");

    //?  oculatamos el modal
    $("#cobrar").modal('hide');

    //? habilitar campos
    const btnImprimirTicket = $("#btn_impirmir_ticket");
    const btnImprimirNota = $("#btn_imprimir_nota_venta");
    const btnFacturarTicket = $("#btn_facturar_ticket");

    btnImprimirTicket.removeClass("hidden");
    btnImprimirNota.removeClass("hidden");
    btnFacturarTicket.removeClass("hidden");

    //? deshabilitamos campos
    deshabilitarCamposTicket();
    borrar_columna_acciones();

    const {
        clave_serie,
        folio_ticket,
        nombre_cliente,
        total,
        total_articulos,
        estatus,
        id_cliente,
        tctrans,
        tcefect,
        total_cobrado,
        total_descuento,
    } = datos;

    let { textEstatus, bgColorClass } = obtenerTxtEstatus(estatus);

    //? cabecera
    $("#text_serie").html(`Serie: ${clave_serie}`);
    $("#text_folio_ticket").html(folio_ticket);

    //? datos estados
    $("#texto_estado")
        .removeClass("bg-primary bg-warning bg-danger bg-success bg-secondary")
        .addClass(bgColorClass)
        .html(`<span class="font-weight-bold">Estado:</span> ${textEstatus}`);

    //? cliente
    $("#text_cliente").html("<b>CLIENTE: </b>" + nombre_cliente);
    $("#btn_eliminar_cliente").addClass("hidden");
    if (id_cliente !== 0) {
        $("#btn_eliminar_cliente").removeClass("hidden");
    }
    //? Tarjeta de datos de la compra
    $("#text_folio_href").html(`#${clave_serie}${folio_ticket}`);
    $("#total_articulos").text(`${Number(total_articulos)}`);
    $("#total_articulos").text(`${Number(total_articulos)}`);
    $("#tot_descuento").text(`$${total_descuento}`);
    $("#text_total").text(`$${total}`);

    //? si ya abono
    if (parseFloat(tcefect) > 0 || parseFloat(tctrans) > 0)
        $(".separadores_pagos").removeClass("hidden");
    if (parseFloat(tcefect) > 0) {
        $("#cantidad_efectivo").text(`$${tcefect}`);
        $("#tr_efectivo").removeClass();
    }
    if (parseFloat(tctrans) > 0) {
        $("#cantidad_transferencia").text(`$${tctrans}`);
        $("#tr_transferencia").removeClass();
    }
    $("#total_cobrado").text(`$${parseFloat(total_cobrado).toFixed(2)}`);
    $("#text_total_cobrar").text(`PAGO:`);
    $("#text_cobrar").text(`$${total}`);
    $("#text_total_tabla").text(`$${total}`);

    ocultarPantallaCarga();
}
function ticketCancelado(datos) {
    pantallaCarga("Cargando datos ticket...", "");

    //? deshabilitamos campos
    deshabilitarCamposTicket();
    borrar_columna_acciones();

    const {
        clave_serie,
        folio_ticket,
        nombre_cliente,
        total,
        total_articulos,
        estatus,
    } = datos;

    let { textEstatus, bgColorClass } = obtenerTxtEstatus(estatus);

    //* folios
    $("#text_serie").html(`Serie: ${clave_serie}`);
    $("#text_folio_ticket").html(folio_ticket);
    $("#text_folio_href").html(`#${clave_serie}${folio_ticket}`);
    //* datos del cliente
    $("#text_cliente").html("<b>CLIENTE: </b>" + nombre_cliente);

    //* datos totales
    $("#total_articulos").text(`${Number(total_articulos)}`);
    $("#text_total").text(`$${total}`);
    $("#text_total_cobrar").text(`Total:`);
    $("#text_cobrar").text(`$${total}`);
    $("#text_total_tabla").text(`$${total}`);

    //* datos estados
    $("#texto_estado")
        .removeClass("bg-primary bg-warning bg-danger bg-success bg-secondary")
        .addClass(bgColorClass)
        .html(`<span class="font-weight-bold">Estado:</span> ${textEstatus}`);

    ocultarPantallaCarga();
}
function deshabilitarCamposTicket() {
    //? deshabilitamos campos
    const inputSearch = $("#search");
    const inputCantidad = $("#cantidad_producto");
    const botonesAcciones = $("#botones_acciones");
    const btnCnvenio = $("#btn_convenio");
    const contAgregarProductos = $("#cont_agregar_productos");
    const btnsCanbiarClientes = $("#cont-buttons-clientes");

    inputSearch.prop("disabled", true);
    inputCantidad.prop("disabled", true);
    botonesAcciones.addClass("hidden");
    btnCnvenio.addClass("hidden");
    contAgregarProductos.addClass("hidden");
    btnsCanbiarClientes.addClass("hidden");
}
function borrar_columna_acciones() {
    //? se deshabilitan los botones de acciones y se esconden
    $("#table_productos_ticket tbody .btn_acciones_tabla").each(function () {
        $(this).addClass("hidden");
        $(this).prop("disabled", true).css({
            opacity: "0.5",
            cursor: "not-allowed",
        });
    });

    const columAcciones = $("#btn_acciones_productos").index();

    //? se borra encabezado de la columna
    $("#table_productos_ticket thead tr th").eq(columAcciones).remove();

    //? se borran los td de la columna
    $("#table_productos_ticket tbody tr").each(function () {
        $(this).find("td").eq(columAcciones).remove();
    });

    //? se borran las celdas de la columna
    $("#table_productos_ticket tfoot tr").each(function () {
        $(this).find("td, th").eq(columAcciones).remove();
    });
}

function obtenerTxtEstatus(estatus) {
    switch (estatus) {
        case 1:
            textEstatus = "APERTURADO";
            bgColorClass = "bg-primary";
            break;
        case 2:
            textEstatus = "GENERADO";
            bgColorClass = "bg-warning";
            break;
        case 3:
            textEstatus = "CANCELADO";
            bgColorClass = "bg-danger";
            break;
        case 4:
            textEstatus = "COBRADO";
            bgColorClass = "bg-success";
            break;
        default:
            textEstatus = "DESCONOCIDO";
            bgColorClass = "bg-secondary";
    }

    return {
        textEstatus,
        bgColorClass,
    };
}

//TODO: funciones para agregar o modificar el cliente
async function eliminar_cliente_ticket() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/peticiones/clientes.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "eliminarClienteTicket",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        const { success, mensaje } = respuesta;

        ocultarPantallaCarga();

        if (!success) {
            mensajeError("Error inesperado", mensaje);
            return;
        }

        // cargamos los datos
        await cargarDatosTicket(folioTicket, idDocumento);
        await cargarTablaProductosTicket();

        mensajeSuccess(mensaje);

        // oculatamos modal
        btn_cerrar_cliente();
        $("#cambiarCliente").modal("hide");
    } catch (error) {
        console.error(error);
    }
}

async function cambiar_cliente() {
    const idCliente = $("#id_cliente_modal").val();
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/peticiones/clientes.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "agregarClienteTicket",
                idCliente: idCliente,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        const { success, mensaje } = respuesta;

        ocultarPantallaCarga();

        if (!success) {
            mensajeError("Error inesperado", mensaje);
            return;
        }

        // cargamos los datos
        await cargarDatosTicket(folioTicket, idDocumento);
        await cargarTablaProductosTicket();

        mensajeSuccess(mensaje);

        // oculatamos modal
        btn_cerrar_cliente();
        $("#cambiarCliente").modal("hide");
    } catch (error) {
        console.error(error);
    }
}

async function btn_cambiar_cliente() {
    await cargar_clientes_modal();
}

function btn_cerrar_cliente() {
    $("#id_cliente_modal").val("");
    $("#search_clientes").val("");
}

async function cargar_clientes_modal() {
    const clientes = await traer_clientes();
    const listUl = $("#list_clientes");

    let tuplas = "";

    clientes.forEach((clie) => {
        let { nombre, id } = clie;

        tuplas += `
      <li class="list-group-item" data-id="${id}">${nombre}</li>
    `;
    });

    listUl.html(tuplas);
}

async function traer_clientes() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/peticiones/clientes.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerClientes",
            },
        });

        const { success, data, mensaje } = respuesta;

        if (!success) {
            throw new Error(mensaje);
        }

        return data;
    } catch (error) {
        console.error(error);
    }
}

//TODO: funciones para agregar convenios
function info_convenio(select) {
    const option = select.options[select.selectedIndex];
    const tipo = option.getAttribute("data-tipo");
    const descuento = option.getAttribute("data-descuento");

    $("#tipoConvenio").val(tipo);
    $("#descuentoConvenio").val(descuento);
}

async function btn_agregar_convenio(idProducto) {
    $("#listConvenios").val("");
    $("#tipoConvenio").val("");
    $("#descuentoConvenio").val("");
    await cargar_select_convenios();
    $("#id_producto_convenio").val(idProducto);
}

async function cargar_select_convenios() {
    const convenios = await traer_convenios();
    const selectConvenios = $("#listConvenios");

    let options = `<option value="" selected disabled>Selecciona un convenio</option>`;
    convenios.forEach((conv) => {
        let { cost_consul, id_convenio, nombre, pct_consul, tipo } = conv;
        let props;

        if (Number(tipo) == 1) {
            props = `data-tipo="Monto fijo" data-descuento="$${cost_consul}"`;
        } else {
            props = `data-tipo="Porcentaje" data-descuento="%${pct_consul}"`;
        }

        options += `<option ${props} value="${id_convenio}">${nombre}</option>`;
    });

    selectConvenios.html(options);
}

async function traer_convenios() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/peticiones/convenios.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerConvenios",
            },
        });

        const { success, data, mensaje } = respuesta;

        if (!success) {
            throw new Error(mensaje);
        }

        return data;
    } catch (error) {
        console.error(error);
    }
}

async function agregar_convenio() {
    const idConvenio = $("#listConvenios").val();
    const idProducto = $("#id_producto_convenio").val();

    try {
        pantallaCarga(
            "Cargando...",
            "Por favor espera mientras se procesa tu solicitud."
        );

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/convenios.php",
            type: "POST",
            data: {
                funcion: "agregarConvenioTicket",
                idProducto: idProducto,
                idConvenio: idConvenio,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
            dataType: "json",
        });

        const { success, mensaje } = respuesta;

        ocultarPantallaCarga();

        if (!success) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }

        // cargamos los datos
        await cargarDatosTicket(folioTicket, idDocumento);
        await cargarTablaProductosTicket();

        mensajeSuccess(mensaje);

        // oculatamos modal
        $("#agregarConvenio").modal("hide");
    } catch (error) {
        ocultarPantallaCarga();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

async function borrar_convenio(idProducto) {
    try {
        pantallaCarga(
            "Cargando...",
            "Por favor espera mientras se procesa tu solicitud."
        );

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/convenios.php",
            type: "POST",
            data: {
                funcion: "eliminarConvenioTicket",
                idProducto: idProducto,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
            dataType: "json",
        });

        const { success, mensaje } = respuesta;

        ocultarPantallaCarga();

        if (!success) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }

        await cargarDatosTicket(folioTicket, idDocumento);
        await cargarTablaProductosTicket();

        mensajeSuccess(mensaje);
    } catch (error) {
        ocultarPantallaCarga();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

//TODO: funciones para agregar productos
async function agregarProducto() {
    const productoId = $("#id_producto").val();
    const cantidad = $("#cantidad_producto").val();

    try {
        pantallaCarga(
            "Cargando...",
            "Por favor espera mientras se procesa tu solicitud."
        );

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                funcion: "agregarProductoTicket",
                productoId: productoId,
                cantidad: cantidad,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
            dataType: "json",
        });

        const { success, producto, mensaje } = respuesta;

        if (!success) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }

        limpiarTablaVacia();
        agregarTuplaTablaTicket(producto);
        await limpiarInputsProductos();
        await cargarDatosTicket(folioTicket, idDocumento);
        mensajeSuccess("El producto se agregó correctamente al ticket.");

        ocultarPantallaCarga();
    } catch (error) {
        ocultarPantallaCarga();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

async function cargarTablaProductosTicket() {
    const productos = await obtenerProdutosTicket();
    const tbody = $("#table_productos_ticket tbody");

    tbody.empty();

    productos.forEach((producto) => {
        agregarTuplaTablaTicket(producto, tbody);
    });
}

async function obtenerProdutosTicket() {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                funcion: "getProductosTicket",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
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
        ocultarPantallaCarga();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

function agregarTuplaTablaTicket(producto, tbody = null) {
    if (!tbody) {
        tbody = $("#table_productos_ticket tbody");
    }

    let { id_producto, cantidad, nombreProducto, precio, importe, descuento } =
        producto;

    let cantidadFormat = parseFloat(cantidad).toFixed(2);
    let precioFormat = parseFloat(precio).toFixed(2);
    let nuevoImporte = parseFloat(importe) - parseFloat(descuento);
    let importeFormat = parseFloat(nuevoImporte).toFixed(2);
    let descuentoFormat = parseFloat(descuento).toFixed(2);

    //? agregar convenio
    let btnConvenio = `
            <button class="btn btn-warning btn-sm btn_acciones_tabla" 
                    style="cursor: pointer;" 
                    producto="${id_producto}" 
                    title="Agregar convenio"
                    data-toggle="modal" data-target="#agregarConvenio" 
                    onclick="btn_agregar_convenio(${id_producto})">
                <i class="fas fa-user-check"></i>
            </button>`;

    if (parseFloat(descuento) > 0) {
        //? eliminar convenio
        btnConvenio = `
            <button class="btn btn-secondary btn-sm btn_acciones_tabla" 
                    style="cursor: pointer;" 
                    producto="${id_producto}" 
                    title="Borrar convenio" 
                    onclick="borrar_convenio(${id_producto})">
                <i class="fas fa-user-times"></i>
            </button>
        `;
    }

    let fila = `
                    <tr id="pro_tick_${id_producto}" class="gradeX">
                        <td class="p-t-0 p-b-0 text-center">
                            <button class="btn btn-danger btn-sm btn_acciones_tabla" 
                                    style="cursor: pointer;" 
                                    title="Borrar producto"
                                    producto="${id_producto}" 
                                    onclick="eliminar_producto(${id_producto})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            ${btnConvenio}
                        </td>
                        <td class="text-center">${cantidadFormat}</td>
                        <td class="text-center">${nombreProducto}</td>
                        <td class="text-center">$${precioFormat}</td>
                        <td class="text-center">$${descuentoFormat}</td>
                        <td class="text-center">$${importeFormat}</td>
                    </tr>
                `;
    tbody.append(fila);
}

function inizializar_tabla(idTabla) {
    $("#" + idTabla)
        .DataTable()
        .destroy();
    $("#" + idTabla).DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        responsive: true,
        deferRender: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json",
        },
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
    try {
        pantallaCarga(
            "Cargando...",
            "Por favor espera mientras se elimina el producto."
        );

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                funcion: "eliminarProductoTicket",
                productoId: idProducto,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
            dataType: "json",
        });

        const { success, borrado } = respuesta;

        if (!success && !borrado) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }
        eliminar_tupla_tabla_ticket(idProducto);
        await cargarDatosTicket(folioTicket, idDocumento);
        mensajeSuccess("Producto eliminado del ticket corretamente.");

        ocultarPantallaCarga();
    } catch (error) {
        ocultarPantallaCarga();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

function eliminar_tupla_tabla_ticket(id_producto) {
    const fila = document.querySelector(`#pro_tick_${id_producto}`);
    if (fila) {
        fila.remove();
    }
}

async function cancelar_ticket() {
    let modalCancelar = $("#borrarcancelar");
    modalCancelar.modal("hide");

    const claveTrab = $("#contrasenia_cancelacion").val();

    try {
        pantallaCarga(
            "Cargando...",
            "Por favor espera mientras se elimina el producto."
        );

        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                funcion: "cancelarTicket",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
                claveTrab: claveTrab,
            },
            dataType: "json",
        });

        const { success, cancelado, mensaje } = respuesta;

        if (!success && !cancelado) {
            // mensajeError("Error al cancelar el ticket:", mensaje);
            mensajeError("Error al cancelar el ticket", mensaje);
            return;
        }

        mensajeSuccess("Ticket cancelado correctamente");

        await cargarDatosTicket(folioTicket, idDocumento);

        ocultarPantallaCarga();
    } catch (error) {
        ocultarPantallaCarga();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

//TODO: acciones con las teclas [F]

document.addEventListener("keydown", function (event) {
    if (event.key === "F9") {
        event.preventDefault();
        activarModalCancelarTicket();
    }
    if (event.key === "F7") {
        event.preventDefault();
        activarModalCobrar();
    }
});

function activarModalCancelarTicket() {
    $("#borrarcancelar").modal("show");
}
async function activarModalCobrar() {
    $("#cobrar").modal("show");
    await cargar_select_metodos_pagos();
}

//todo: funciones para cobrar [tickets]

async function cargar_datos_cobrar_ticket() {
    await cargar_select_metodos_pagos();
    await cargar_faltante_cobrar();
}

async function cobrar_ticket() {
    if (!validar_form_cobrar()) return;

    const metoPag = $("#select_metodo_pago").val();
    const cantPago = $("#input_efectivo").val();

    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/pagos/cobrar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "registrarPago",
                metoPago: metoPag,
                cantPago: cantPago,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        let { success, data, mensaje } = respuesta;

        if (success) {
            let { cambio, message } = data;
            // cargamos los datos
            cargarDatosTicket(folioTicket, idDocumento);
            limpiar_form_cobrar();
            rellenar_tabla_pagos();

            mensajeSuccess(message);
            return;
        }

        mensajeError("", mensaje);
        return;
    } catch (error) {
        console.error(error);
    }
}

async function cargar_select_metodos_pagos() {
    const metodosPago = await traer_metodos_pago();
    const selectMetPag = $("#select_metodo_pago");
    await rellenar_tabla_pagos();

    let options =
        "<option value='' selected disabled>Selecciona un m&eacute;todo</option>";

    metodosPago.forEach((mp) => {
        let { clave_forma, descripcion, id_forma } = mp;

        options += `
            <option value="${id_forma}">[${clave_forma}] ${descripcion}</option>
        `;
    });

    selectMetPag.html(options);
}

async function cargar_faltante_cobrar() {
    let dataCobrar = await obtener_faltante_cobrar();
    let { falta_cobrar } = dataCobrar;
    $("#input_efectivo").val(falta_cobrar.toFixed(2));

}
async function obtener_faltante_cobrar() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/pagos/cobrar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerFaltanteCobrar",
                idDocumento: idDocumento,
                folioTicket: folioTicket,
            },
        });

        const { success, data, mensaje } = respuesta;

        if (!success) {
            throw new Error(mensaje);
        }

        return data;
    } catch (error) {
        console.error(error);
    }
}

async function traer_metodos_pago() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/pagos/cobrar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerFormasPago",
            },
        });

        const { success, data, mensaje } = respuesta;

        if (!success) {
            throw new Error(mensaje);
        }

        return data;
    } catch (error) {
        console.error(error);
    }
}

function validar_form_cobrar() {
    let selectMetoPag = $("#select_metodo_pago");
    let inpCantPago = $("#input_efectivo");
    let metoPag = selectMetoPag.val();
    let cantPago = inpCantPago.val();

    if (!metoPag) {
        selectMetoPag.addClass("is-invalid");
    }
    if (!cantPago) {
        inpCantPago.addClass("is-invalid");
    }

    if (!metoPag || !cantPago) {
        return false;
    }

    return true;
}

async function rellenar_tabla_pagos() {
    await cargar_faltante_cobrar();
    const pagos = await cargar_pagos_ticket();
    const tablaPagosBody = $("#tabla_pagos tbody");

    tablaPagosBody.empty();

    pagos.forEach((pago) => {
        // Crear fila con los datos del pago
        const fila = `
                <tr>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="eliminarPago(${pago.id_pago
            })">
                            Eliminar
                        </button>
                    </td>
                    <td>${pago.descripcion}</td>
                    <td>$${parseFloat(pago.monto).toFixed(2)}</td>
                    <td>${pago.fecha_pago}</td>
                </tr>
            `;
        // Agregar la fila al cuerpo de la tabla
        tablaPagosBody.append(fila);
    });
}

async function cargar_pagos_ticket() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/pagos/cobrar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerPagosTicket",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        const { success, data, mensaje } = respuesta;

        if (!success) {
            throw new Error(mensaje);
        }

        return data;
    } catch (error) {
        console.error(error);
    }
}

function limpiar_form_cobrar() {
    $("#select_metodo_pago").val("");
    $("#input_efectivo").val("");
}

//todo: impresiones de tickets

function imprimirTicket() {
    const url = `componentes/formatos_pdf/ventas/ticket.php?folio_ticket=${folioTicket}&id_documento=${idDocumento}`;
    window.open(url, '_blank');
}
function imprimirNotaVenta() {
    const url = `componentes/formatos_pdf/ventas/nota_venta.php?folio_ticket=${folioTicket}&id_documento=${idDocumento}`;
    window.open(url, '_blank');
}

//todo: generar factura
function modalCambiarCliente() {

    cerrarModal('generaFactura');

    modificarModalCambiarCliente();

    btn_cambiar_cliente();

    abrirModal('cambiarCliente');
}

function facturarTicket() {
    abrirModal('generaFactura');
}

function modificarModalCambiarCliente() {
    const btnModificar = $("#btn_cambiar_cliente_modal");
    const btnCancelar = $("#btnCerrarClientesCambiar");

    if (btnModificar.length) {
        btnModificar.removeAttr("onclick");

        btnModificar.off("click");
        btnModificar.on("click", function () {
            console.log("Nueva función");
            cambiar_cliente_fac();
        });
    } else {
        console.error("El botón #btn_cambiar_cliente_modal no existe");
    }

    if (btnCancelar.length) {

        btnCancelar.removeAttr("onclick");

        btnCancelar.off("click");
        btnCancelar.on("click", function () {
            regresarAModalFac();
        });
    } else {
        console.error("El botón #btn_cambiar_cliente_modal no existe");
    }
}

function regresarAModalFac() {
    btn_cerrar_cliente()
    cerrarModal('cambiarCliente');
    abrirModal('generaFactura');
}

async function getClienteTicket() {

    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/peticiones/clientes.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "getClienteTicket",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        const { success, error, data } = respuesta;

        if (!success) {
            mensajeError("Error inesperado", error);
            return;
        }

        return data[0];

    } catch (error) {
        console.error(error);
    }
}

async function cambiarTextoCliente() {
    const cliente = await getClienteTicket();

    const txt = $("#text_cliente");
    txt.html("<b>CLIENTE: </b>" + cliente.cliente);
    cambiarValInpuitNombre(cliente.cliente);
}

function cambiarValInpuitNombre(cliente) {
    const inputCliente = $("#cliente_factura_editar");
    inputCliente.val(cliente);
}

async function cambiar_cliente_fac() {
    const idCliente = $("#id_cliente_modal").val();

    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/peticiones/clientes.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "agregarClienteTicket",
                idCliente: idCliente,
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        const { success, mensaje } = respuesta;

        cargarInfoFormFac();
        ocultarPantallaCarga();

        if (!success) {
            mensajeError("Error inesperado", mensaje);
            return;
        }

        // cargamos los datos
        await cambiarTextoCliente();

        regresarAModalFac();

        mensajeSuccess(mensaje);

    } catch (error) {
        console.error(error);
    }
}

$('#generaFactura').on('show.bs.modal', async function (e) {
    await cargarInfoFormFac();
});

async function cargarInfoFormFac() {
    await cargarNombreClienteInput();
    await cargar_perfiles_facturacion_modal();
    await cargar_cat_cfdi();
    await cargar_cat_metodos_pago();
    await cargar_cat_formas_pago();
}

async function cargarNombreClienteInput() {
    const cliente = await getClienteTicket();
    cambiarValInpuitNombre(cliente.cliente);
}

//? catalogo cfdi
async function cargar_cat_cfdi() {
    const data = await traer_cat_uso_cfdi();
    const select = $("#uso_cfdi");
    let options = '';

    if (data.length == 0) {
        options += `
                    <option value=''>Sin opciones uso CFDI</option>
                `;
        select.html(options);
        return;
    }

    options += `
                    <option value="" selected disabled>Selecciona Uso CFDI</option>
                `;

    data.forEach(cfdi => {
        options += `
                    <option 
                        value=${cfdi.clave_uso}
                        data-id="${cfdi.id}" 
                        data-descripcion="${cfdi.descripcion}" 
                        data-fisica="${cfdi.fisica}" 
                        data-moral="${cfdi.moral}" 
                        data-regimen-fiscal="${cfdi.regimen_fiscal}" 
                    >[${cfdi.clave_uso}] ${cfdi.descripcion}</option>
                `;
    });
    select.html(options);
}
async function traer_cat_uso_cfdi() {
    try {
        const regimen = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerCatUsoCFDI",
            },
        });
        let { success, data, mensaje } = regimen;

        if (success) {
            return data;
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}
function obtenerAtributosUsoCFDI(selectId) {

    const select = $(`#${selectId}`);

    if (select.length === 0) {
        return;
    }

    const selectedOption = select.find(":selected");

    if (selectedOption.length === 0) {
        console.error("No hay ninguna opción seleccionada.");
        return null;
    }

    const atributos = {
        clave: selectedOption.val(),
        id: selectedOption.data('id'),
        descripcion: selectedOption.data('descripcion'),
        fisica: selectedOption.data('fisica'),
        moral: selectedOption.data('moral'),
        regimen_fiscal: selectedOption.data('regimen-fiscal'),
    };

    return atributos;

}
async function cargarUsoCFDI() {
    const cfdi = obtenerAtributosUsoCFDI('uso_cfdi');
}
//? catalogo metodos pago
async function cargar_cat_metodos_pago() {
    const data = await traer_cat_metodos_pago();
    const select = $("#metodo_pago_dg");
    let options = '';

    if (data.length == 0) {
        options += `
                    <option value=''>Sin opciones Metodos de Pago</option>
                `;
        select.html(options);
        return;
    }

    options += `
                    <option value="" selected disabled>Selecciona un metodo de pago</option>
                `;

    data.forEach(metodo => {
        options += `
                    <option 
                        value=${metodo.clave_metodo}
                        data-id="${metodo.id}" 
                        data-descripcion="${metodo.descripcion}" 
                    >[${metodo.clave_metodo}] ${metodo.descripcion}</option>
                `;
    });
    select.html(options);
}
async function traer_cat_metodos_pago() {
    try {
        const regimen = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerCatMetodosPago",
            },
        });
        let { success, data, mensaje } = regimen;

        if (success) {
            return data;
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}
//? catalogo formas de pago
async function cargar_cat_formas_pago() {
    const data = await traer_cat_formas_pago();
    const select = $("#forma_pago_dg");
    let options = '';

    if (data.length == 0) {
        options += `
                    <option value=''>Sin opciones Formas de Pago</option>
                `;
        select.html(options);
        return;
    }

    options += `
                    <option value="" selected disabled>Selecciona una forma de pago</option>
                `;

    data.forEach(formaPago => {
        options += `
                    <option 
                        value=${formaPago.clave_forma}
                        data-id="${formaPago.id}" 
                        data-descripcion="${formaPago.descripcion}" 
                    >[${formaPago.clave_forma}] ${formaPago.descripcion}</option>
                `;
    });
    select.html(options);
}
async function traer_cat_formas_pago() {
    try {
        const regimen = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerCatFormasPago",
            },
        });
        let { success, data, mensaje } = regimen;

        if (success) {
            return data;
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}
//? perfiles facturacion
async function traer_perfiles_facturacion() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerPerfilesFacturacionCliente",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
            },
        });

        let { success, data, mensaje } = respuesta;

        if (success) {
            return data;
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}
async function cargar_perfiles_facturacion_modal() {
    const data = await traer_perfiles_facturacion();
    const select = $("#perfiles_facturacion");
    let options = '';

    if (data.length == 0) {
        options += `
                    <option value=''>Sin perfiles de facturacion</option>
                `;
        select.html(options);
        return;
    }

    options += `
                    <option value='' selected disabled>Selecciona un perfil de facturacion</option>
                `;

    data.forEach(perfil => {
        options += `
                    <option 
                        value=${perfil.id_perfil}
                        data-rfc="${perfil.rfc}" 
                        data-nombre-social="${perfil.nombre_social}" 
                        data-calle="${perfil.calle}" 
                        data-no-exterior="${perfil.no_exterior}" 
                        data-no-interior="${perfil.no_interior}" 
                        data-codigo-postal="${perfil.codigo_postal}" 
                        data-colonia="${perfil.colonia}" 
                        data-municipio="${perfil.municipio}" 
                        data-estado="${perfil.estado}" 
                        data-pais="${perfil.pais}" 
                        data-regimen-fiscal="${perfil.regimen_fiscal}" 
                        data-metodo-pago="${perfil.metodo_pago}" 
                        data-forma-pago="${perfil.forma_pago}" 
                        data-uso-cfdi="${perfil.uso_cfdi}"
                        data-id-cliente="${perfil.id_cliente}"
                    >${perfil.nombre_social}</option>
                `;
    });
    select.html(options);
}
function obtenerAtributosPerfil(selectId) {
    const select = $(`#${selectId}`);

    if (select.length === 0) {
        return;
    }

    const selectedOption = select.find(":selected");

    if (selectedOption.length === 0) {
        console.error("No hay ninguna opción seleccionada.");
        return null;
    }

    const atributos = {
        idPerfil: selectedOption.val(),
        rfc: selectedOption.data('rfc'),
        nombreSocial: selectedOption.data('nombre-social'),
        calle: selectedOption.data('calle'),
        noExterior: selectedOption.data('no-exterior'),
        noInterior: selectedOption.data('no-interior'),
        codigoPostal: selectedOption.data('codigo-postal'),
        colonia: selectedOption.data('colonia'),
        municipio: selectedOption.data('municipio'),
        estado: selectedOption.data('estado'),
        pais: selectedOption.data('pais'),
        regimenFiscal: selectedOption.data('regimen-fiscal'),
        metodoPago: selectedOption.data('metodo-pago'),
        formaPago: selectedOption.data('forma-pago'),
        usoCfdi: selectedOption.data('uso-cfdi'),
        id_cliente: selectedOption.data('id-cliente')
    };

    return atributos;

}
//? cargar datos
async function cargarInformacionPerfil() {
    const perfil = obtenerAtributosPerfil('perfiles_facturacion');
    const regimenDesc = await cargarRegimenFisacal(perfil.regimenFiscal);
    const { calle, codigo_postal, colonia, estado, municipio, no_exterior, no_interior, pais, clave_localidad } = await traerDireccionFormText(perfil.idPerfil);
    const direccion = `${calle} ${no_exterior} ${no_interior ? no_interior : ''}  COL. ${colonia} CP ${codigo_postal}, ${municipio}, ${estado}, ${pais}`

    $("#nombre_cliente_dg").val(perfil.nombreSocial);
    $("#id_cliente_dg").val(perfil.id_cliente);
    $("#rfc_cliente_dg").val(perfil.rfc);
    $("#clave_regimen_cliente_dg").val(perfil.regimenFiscal);
    $("#regimen_cliente").val(regimenDesc);

    $("#calle_dg").val(perfil.calle);
    $("#no_exterior_dg").val(perfil.noExterior);
    $("#no_interior_dg").val(perfil.noInterior);
    $("#colonia_dg").val(perfil.colonia);
    $("#codigo_postal_dg").val(perfil.codigoPostal);
    $("#localidad_dg").val(clave_localidad);
    $("#municipio_dg").val(perfil.municipio);
    $("#estado_dg").val(perfil.estado);
    $("#pais_dg").val(perfil.pais);
    $("#direccion_dg").val(direccion);
    $("#uso_cfdi").val(perfil.usoCfdi.toUpperCase());
    $("#metodo_pago_dg").val(perfil.metodoPago.toUpperCase());
    $("#forma_pago_dg").val(perfil.formaPago.toUpperCase());
}
const cargarRegimenFisacal = async (claveRegimen) => {
    try {
        const regimen = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerRegimenFiscalPorClave",
                claveRegimen: claveRegimen,
            },
        });
        let { success, data: { descripcion }, mensaje } = regimen;

        if (success) {
            return descripcion;
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}
const traerDireccionFormText = async (perfilFac) => {
    //? esta funcion, se basa mas que nada en claves, 
    //? no en texto para devolver la cadena
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "traerDireccionFormText",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
                idPerfil: perfilFac,
            },
        });

        let { success, data, mensaje } = respuesta;

        if (success) {
            return data[0];
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}

//? tipos de cambio
async function obtenerTipoCambioCAD() {
    try {
        const response = await fetch(urlBancoMexicoFetch(serieCAD, tokenBM));
        const data = await response.json();

        const tipoCambio = data.bmx.series[0].datos[0].dato;
        return tipoCambio;
    } catch (error) {
        console.error("Error al obtener el tipo de cambio:", error);
        return "No disponible";
    }
}
async function obtenerTipoCambioUSD() {
    try {
        const response = await fetch(urlBancoMexicoFetch(serieUSD, tokenBM));
        const data = await response.json();

        const tipoCambio = data.bmx.series[0].datos[0].dato;
        return tipoCambio;
    } catch (error) {
        console.error("Error al obtener el tipo de cambio:", error);
        return "No disponible";
    }
}
async function cambiarTipoMoneda() {
    const tipoMoneda = $("#tipo_moneda").val();
    let tipoCambio = 1;

    if (tipoMoneda === 'CAD') {
        tipoCambio = await obtenerTipoCambioCAD();
    }
    if (tipoMoneda === 'USD') {
        tipoCambio = await obtenerTipoCambioUSD();
    }

    $("#tipo_cambio").val(tipoCambio);
}

//?funcion de timbrar
function validarForm(){
    let perfil = $("#perfiles_facturacion");

    if(!perfil.val() || perfil.val() == ''){
        perfil.addClass("is-invalid");
    }
}
async function timbrarFactura() {

    validarForm();

    $("form").submit(async function (event) {
        event.preventDefault();
        const datos = $("#form-facturar").serializeArray();
        let datosObjeto = {};

        $.each(datos, function(_, campo){
            datosObjeto[campo.name] = campo.value;
        });
        await fetchTimbrarFactura(datosObjeto);
        console.log(datos);
    });
}

async function fetchTimbrarFactura(datos){
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: "componentes/tickets/factura/facturar_ticket.php",
            type: "POST",
            dataType: "json",
            data: {
                funcion: "facturarTicket",
                folioTicket: folioTicket,
                idDocumento: idDocumento,
                dataTim: datos,
            },
        });

        let { success, data, mensaje } = respuesta;

        if (success) {
            return data;
        }

        mensajeError("", mensaje);
        return;

    } catch (error) {
        console.error(error);
    }
}
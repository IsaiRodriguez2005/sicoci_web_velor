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
function mensajeSuccess(mensaje) {
    return Swal.fire({
        icon: "success",
        title: "Éxito",
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

//todo: Ejecutar la función automáticamente al cargar la página
document.addEventListener("DOMContentLoaded", procesarDatosTicket);

async function procesarDatosTicket() {
    //* Obtener los parámetros de la URL

    pantallaCarga("Cargando datos...");

    const urlParams = new URLSearchParams(window.location.search);

    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");

    if (!folioTicket || !idDocumento) {
        console.error("Faltan parámetros en la URL.");
        return;
    }

    cargarDatosTicket(folioTicket, idDocumento);
    await cargarTablaProductosTicket();

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

    Swal.close();
}

async function cargarDatosTicket(folio, idDocumento) {
    const datosTicket = await obtenerDatosTicket(folio, idDocumento);

    const { estatus } = datosTicket;

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
            textEstatus = "COBRADO";
            bgColorClass = "bg-success";
            break;
        default:
            textEstatus = "DESCONOCIDO";
            bgColorClass = "bg-secondary";
    }
    /* 
    
            */
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
    const {
        clave_serie,
        folio_ticket,
        nombre_cliente,
        total,
        total_articulos,
        estatus,
        id_cliente
    } = datos;

    let { textEstatus, bgColorClass } = obtenerTxtEstatus(estatus);

    //* folios
    $("#text_serie").html(`Serie: ${clave_serie}`);
    $("#text_folio_ticket").html(folio_ticket);
    $("#text_folio_href").html(`#${clave_serie}${folio_ticket}`);
    //* datos del cliente
    $("#btn_eliminar_cliente").addClass("hidden");
    if (id_cliente !== 0){
        $("#btn_eliminar_cliente").removeClass('hidden');
    }
    $("#text_cliente").html("<b>CLIENTE: </b>" + nombre_cliente);

    //* datos totales
    $("#total_articulos").text(`${Number(total_articulos)}`);
    $("#text_total").text(`$${total}`);
    $("#text_cobrar").text(`$${total}`);
    $("#text_total_tabla").text(`$${total}`);

    //* datos estados
    $("#texto_estado")
        .removeClass("bg-primary bg-warning bg-danger bg-success bg-secondary")
        .addClass(bgColorClass)
        .html(`<span class="font-weight-bold">Estado:</span> ${textEstatus}`);
}
function ticketCancelado(datos) {
    //? deshabilitamos campos
    const inputSearch = $("#search");
    const inputCantidad = $("#cantidad_producto");
    const botonesAcciones = $("#botones_acciones");
    const btnCnvenio = $("#btn_convenio");
    const contAgregarProductos = $("#cont_agregar_productos");

    inputSearch.prop("disabled", true);
    inputCantidad.prop("disabled", true);
    botonesAcciones.addClass("hidden");
    btnCnvenio.addClass("hidden");
    contAgregarProductos.addClass("hidden");

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
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");
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

        Swal.close();

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
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");

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

        Swal.close();

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
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");
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

        Swal.close();

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
        Swal.close();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

async function borrar_convenio(idProducto) {
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");

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

        Swal.close();

        if (!success) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }

        await cargarDatosTicket(folioTicket, idDocumento);
        await cargarTablaProductosTicket();

        mensajeSuccess(mensaje);
    } catch (error) {
        Swal.close();
        mensajeError(
            "Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente."
        );
        console.error(error);
    }
}

//TODO: funciones para agregar productos
async function agregarProducto() {
    const urlParams = new URLSearchParams(window.location.search);
    const productoId = $("#id_producto").val();
    const cantidad = $("#cantidad_producto").val();
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");

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
        Swal.close();

        if (!success) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }

        limpiarTablaVacia();
        agregarTuplaTablaTicket(producto);
        await limpiarInputsProductos();
        await cargarDatosTicket(folioTicket, idDocumento);

        mensajeSuccess("El producto se agregó correctamente al ticket.");
    } catch (error) {
        Swal.close();
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
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");

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
        Swal.close();
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
            <button class="btn btn-warning btn-sm" 
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
            <button class="btn btn-secondary btn-sm" 
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
                            <button class="btn btn-danger btn-sm" 
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
    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");

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

        Swal.close();

        if (!success && !borrado) {
            mensajeError("Revisa la tabla de articulos", mensaje);
            return;
        }
        eliminar_tupla_tabla_ticket(idProducto);
        await cargarDatosTicket(folioTicket, idDocumento);
        mensajeSuccess("Producto eliminado del ticket corretamente.");
    } catch (error) {
        Swal.close();
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

    const urlParams = new URLSearchParams(window.location.search);
    const folioTicket = urlParams.get("folio_ticket");
    const idDocumento = urlParams.get("id_documento");
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

        Swal.close();

        if (!success && !cancelado) {
            mensajeError("Error al cancelar el ticket:", mensaje);
            return;
        }

        mensajeSuccess("Ticket cancelado correctamente");

        await cargarDatosTicket(folioTicket, idDocumento);
    } catch (error) {
        Swal.close();
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
});

function activarModalCancelarTicket() {
    $("#borrarcancelar").modal("show");
}

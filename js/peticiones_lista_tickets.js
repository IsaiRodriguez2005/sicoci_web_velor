//todo: [Apoyo]
function redieccionarURL(url) {
    Swal.fire({
        title: 'Cargando...',
        html: 'Espere un momento mientras procesamos su solicitud.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    //* Redirigir después de un breve tiempo
    setTimeout(() => {
        window.location.href = url;
    }, 1000);
}
function obtenerTxtEstatus(estatus) {
    let textEstatus, bgColorClass;

    switch (estatus) {
        case 1:
            textEstatus = 'APERTURADO';
            bgColorClass = 'badge badge-primary';
            break;
        case 2:
            textEstatus = 'GENERADO';
            bgColorClass = 'badge badge-warning';
            break;
        case 3:
            textEstatus = 'CANCELADO';
            bgColorClass = 'badge badge-danger';
            break;
        case 4:
            textEstatus = 'COBRADO';
            bgColorClass = 'badge badge-success';
            break;
        default:
            textEstatus = 'DESCONOCIDO';
            bgColorClass = 'badge badge-secondary';
    }

    // Devolver HTML completo para mostrar en la tabla
    return `
        <td class="text-center text-sm" style="white-space: nowrap; overflow-x: auto;">
            <span class="${bgColorClass}" style="width: 100%; color: white;">${textEstatus}</span>
        </td>
    `;
}
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
function mensajeSuccess(mensaje) {
    return Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
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

//todo: [Cagar automaticamente]
document.addEventListener('DOMContentLoaded', async () => {
    pantallaCarga('Cargando datos...');

    await cargar_series_select();
    await cargar_tickets_tabla();

    Swal.close();
});

//todo: [Nuevo ticket]
async function nueva_venta() {
    const modal = $("#modal_opciones_series_tickets");
    const { success, folios } = await cargar_existecias_series();

    if (success) {

        if (folios.length == 1) {
            seleccionar_serie(folios[0].id)
        }

        modal.modal('show');
        rellenar_tbody_seies_tickets(folios);
    }
}

function rellenar_tbody_seies_tickets(series) {

    const tbody = $("#mostrar_series_tickets");

    tbody.html('');
    series.forEach(ticket => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" title="Seleccionar Serie" onclick="seleccionar_serie(
                                    ${ticket.id}
                                )">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center">${ticket.id}</td>
                    <td class="text-center">${ticket.documento}</td>
                    <td class="text-center">${ticket.serie ? ticket.serie : 'N/A'}</td>
                    <td class="text-center">${ticket.codigoPostal}</td>
                    <td class="text-center">
                        <span class="badge ${ticket.estatus == 1 ? 'badge-success' : 'badge-secondary'}" style="width: 100%; color:white;">
                            ${ticket.estatus == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
        `;
        tbody.append(row);
    });

    inizializar_tabla('tabla_series_tickets');
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

async function seleccionar_serie(idSerie) {

    const url = await get_url_ticket(idSerie);

    if (!url) {
        throw new Error("Error al obtener la url");
    }

    redieccionarURL(url);
}
async function get_url_ticket(serieTicket) {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'getURLticketSinCita',
                'serieTicket': serieTicket
            },
            dataType: "json",
        });

        const { success, url } = respuesta;

        if (!success) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: response.message || "Ocurrió un error al procesar la solicitud"
            });
            throw new Error("Error en la respuesta: " + (respuesta.message || "Desconocido"));
        }

        return url;
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
//todo: [Tickets]
async function cargar_tickets_tabla() {
    const tickets = await cargar_tickets_hoy();
    const tbody = document.querySelector('table tbody');
    let filas = '';

    if (!tickets) {

        filas += `
            <tr>
                <td colspan="100%" class="text-center">No hay tickets</td>
            </tr>
        `;
        tbody.innerHTML = filas;

        Swal.close();
        return;
    }



    tickets.forEach(ticket => {
        let { fecha_emision, estatus, folio_ticket, id_documento, total, urlTicket, nombre_cliente, clave_serie } = ticket;

        filas += `
            <tr class='odd'>
                <td class="text-center text-sm" style="white-space: nowrap; overflow-x: auto;"><a href='${urlTicket}' class='text-primary'>${clave_serie}${folio_ticket}</a></td>
                <td class="text-center text-sm" style="white-space: nowrap; overflow-x: auto;">${fecha_emision}</td>
                <td class="text-center text-sm" style="white-space: nowrap; overflow-x: auto;">${nombre_cliente}</td>
                ${obtenerTxtEstatus(estatus)}
                <td class="text-center text-sm" style="white-space: nowrap; overflow-x: auto;">$${parseFloat(total).toFixed(2)}</td>
            </tr>
        `;
    });
    tbody.innerHTML = filas;

    inizializar_tabla('table_tickets_list');
    
}
async function cargar_tickets_hoy() {
    try {

        const respuesta = await $.ajax({
            url: "componentes/tickets/existencias/tickets.php",
            type: "POST",
            data: {
                'funcion': 'traerTicketsTabla',
            },
            dataType: "json",
        });

        const { success, tickets } = respuesta;

        if (!success) {
            // mensajeError('Aún no hay tickets.', 'Vaya!')
            return;
        }

        return tickets;

    } catch (e) {
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(e);
    }
}
async function buscar_ticket() {

    const inputFolio = $("#folio_input");
    const selectSerie = $("#series_select");
    const valueInputFolio = inputFolio.val();
    const valueSelectSerie = selectSerie.val();

    if (!valueInputFolio && !valueSelectSerie) {
        if (!valueInputFolio) {
            $("#folio_input").addClass('is-invalid');
        }
        if (!valueSelectSerie) {
            $("#series_select").addClass('is-invalid');
        }
        mensajeError('Introduce los datos necesarios...', 'Datos insuficientes')
    }
    try {

        const respuesta = await $.ajax({
            url: "componentes/tickets/existencias/tickets.php",
            type: "POST",
            data: {
                'funcion': 'searchTickets',
                'idSerie': valueSelectSerie,
                'idFolio': valueInputFolio,
            },
            dataType: "json",
        });

        const { exists, urlTicket, mensaje, error } = respuesta;

        if (error) {
            mensajeError(error);
            return;
        }
        // limpiamos inputs  
        inputFolio.val('');
        selectSerie.val('');

        pantallaCarga(mensaje);
        redieccionarURL(urlTicket);

    } catch (e) {
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(e);
    }

}


//todo: [Series]
async function cargar_series_select() {
    const selectSeries = $("#series_select");

    const series = await cargar_series();

    let options = '<option value="" selected disabled>Selecciona una serie</option>';
    series.forEach(ser => {
        const { id_partida, serie } = ser;
        options += `<option value="${id_partida}">${serie}</option>`;
    });

    selectSeries.html(options);
}
async function cargar_series() {

    try {

        const respuesta = await $.ajax({
            url: "componentes/tickets/existencias/series_tickets.php",
            type: "POST",
            data: {
                'funcion': 'cargarSeriesSelect',
            },
            dataType: "json",
        });

        const { success, series } = respuesta;

        if (!success) {
            mensajeError('Error al cargar las series.');
            return;
        }

        return series;

    } catch (e) {
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(e);
    }

}
async function cargar_existecias_series() {
    try {

        const respuesta = await $.ajax({
            url: "componentes/tickets/existencias/series_tickets.php",
            type: "POST",
            data: {
                'funcion': 'existenciaSeriesTickets',
                'id_documento': 7
            },
            dataType: "json",
        });

        const { success } = respuesta;

        if (!success) {
            mensajeError('Error al cargar las series.');
            return;
        }

        return respuesta;

    } catch (e) {
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(e);
    }
}

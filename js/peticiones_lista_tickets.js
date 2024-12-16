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

document.addEventListener('DOMContentLoaded', async () => {
    pantallaCarga('Cargando datos...');

    await cargar_series_select();
    await cargar_tickets_tabla();

    Swal.close();
});
async function cargar_tickets_tabla() {
    const tickets = await cargar_tickets_hoy();

    let filas = '';

    tickets.forEach(ticket => {
        let { fecha_emision, estatus, folio_ticket, id_documento, total, urlTicket, nombre_cliente } = ticket;
        txtEst = obtenerTxtEstatus(estatus);
        
        filas += `
            <tr>
                <td><a href='${urlTicket}' class='text-primary'>${id_documento}${folio_ticket}</a></td>
                <td>${fecha_emision}</td>
                <td>${nombre_cliente}</td>
                <td class='${txtEst.bgColorClass} text-white text-center'>${txtEst.textEstatus}</td>
                <td>$${parseFloat(total).toFixed(2)}</td>
            </tr>
        `;
    });

    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = filas;
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
            mensajeError('Error al cargar los tickets.');
            return;
        }

        return tickets;

    } catch (e) {
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(e);
    }
}

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

async function buscar_ticket() {

    const valueInputFolio = $("#folio_input").val();
    const valueSelectSerie = $("#series_select").val();

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

        pantallaCarga(mensaje);

        redieccionarURL(urlTicket);

    } catch (e) {
        mensajeError('Ocurrió un error inesperado. Verifica tu conexión o inténtalo nuevamente.')
        console.error(e);
    }

}

async function get_url_ticket(serieTicket, folioCita, idCliente) {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "GET",
            data: {
                'funcion': 'getURLticket',
                'serieTicket': serieTicket,
                'folioCita': folioCita,
                'idCliente': idCliente
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
    switch (estatus) {
        case 1:
            textEstatus = 'APERTURADO';
            bgColorClass = 'bg-primary';
            break;
        case 2:
            textEstatus = 'GENERADO';
            bgColorClass = 'bg-warning';
            break;
        case 3:
            textEstatus = 'CANCELADO';
            bgColorClass = 'bg-danger';
            break;
        case 4:
            textEstatus = 'COBRADO';
            bgColorClass = 'bg-success';
            break;
        default:
            textEstatus = 'DESCONOCIDO';
            bgColorClass = 'bg-secondary';
    }

    return {
        textEstatus,
        bgColorClass
    };
}
function procesarDatosTicket() {
    //* Obtener los parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);

    const folioTicket = urlParams.get('folio_ticket');
    const serieTicket = urlParams.get('serie_ticket');
    const idCita = urlParams.get('id_cita');
    const idCliente = urlParams.get('id_cliente');
    const idDocumento = urlParams.get('id_documento');

    if (!folioTicket || !serieTicket || !idCita || !idCliente || !idDocumento) {
        console.error('Faltan parámetros en la URL.');
        return;
    }

    console.log('Folio Ticket:', folioTicket);
    console.log('Serie Ticket:', serieTicket);
    console.log('ID Cita:', idCita);
    console.log('ID Cliente:', idCliente);
    console.log('ID Documento:', idDocumento);

    cargarDatosTicket(folioTicket, serieTicket, idCita, idCliente, idDocumento);
}

async function cargarTextos(folioTicket, serieTicket, idCita, idCliente, idDocumento) {
    try {
        const respuesta = await $.ajax({
            url: "componentes/tickets/peticiones/tickets.php",
            type: "POST",
            data: {
                'funcion': 'getTextosTickets',
                'serieTicket': serieTicket,
                'folioCita': idCita,
                'idCliente': idCliente,
                'idDocumento': idDocumento,
                'folioTicket': folioTicket,
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

function cargarDatosTicket(folioTicket, serieTicket, idCita, idCliente, idDocumento) {
    console.log(`Procesando ticket ${folioTicket} de la serie ${serieTicket}`);
    // Aquí podrías realizar una solicitud AJAX para obtener más datos
}

//! Ejecutar la función automáticamente al cargar la página
document.addEventListener('DOMContentLoaded', procesarDatosTicket);

<?php
session_start();
require("../../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.php';
            </script>
        ";
} else {

    if (isset($_POST['funcion'])) {
        if ($_POST['funcion'] == 'getTextosTickets') {
            $idEmisor = $_SESSION['id_emisor'];
            obtenerTextosTicket($_POST, $idEmisor, $conexion);
        }
    }

    if (isset($_GET['funcion'])) {
        //* esta validacion, detecta si esta la accion son las funciones 
        if ($_GET['funcion'] == 'getURLticket') {

            if (!isset($_SESSION['id_emisor'], $_GET['serieTicket'], $_GET['folioCita'], $_GET['idCliente'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Parámetros insuficientes.'
                ]);
                exit();
            }
            $idEmisor = $_SESSION['id_emisor'];
            $serieTicket = $_GET['serieTicket'];
            $folioCita = $_GET['folioCita'];
            $idCliente = $_GET['idCliente'];

            $ticketAper = aperturarTicket($serieTicket, $folioCita, $idCliente, $idEmisor, $conexion);
            if (isset($ticketAper['error'])) {
                echo json_encode([
                    'success' => false,
                    'error' => $ticketAper['error']
                ]);
                exit();
            }
            $url = construct_URL_ticket($ticketAper);
            echo json_encode([
                'success' => true,
                'url' => $url
            ]);
            exit();
        }
    }
}
function obtenerTextosTicket($post, $idEmisor, $conexion)
{
    $serieTicket = $post['serieTicket'] ?? null;
    $folioCita = $post['folioCita'] ?? null;
    $idCliente = $post['idCliente'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $folioTicket = $post['folioTicket'] ?? null;

    $query = "SELECT

                    FROM emisores_tickets et
                        INNER JOIN emisores_series es ON es.id_partida = et.serie_ticket AND es.id_emisor = et.id_emisor
                        In";
}
function construct_URL_ticket($ticketAperturado)
{
    $folioTicket = $ticketAperturado['folio_ticket'];
    $serieTicket = $ticketAperturado['serie_ticket'];
    $idCita = $ticketAperturado['id_cita'];
    $idCliente = $ticketAperturado['id_cliente'];
    $idDocumento = $ticketAperturado['id_documento'];

    //* Construir la URL con parámetros de consulta
    $url = 'ticket.php?' . http_build_query([
        'folio_ticket' => $folioTicket,
        'serie_ticket' => $serieTicket,
        'id_cita' => $idCita,
        'id_cliente' => $idCliente,
        'id_documento' => $idDocumento
    ]);

    return $url;
}
function aperturarTicket($serieTicket, $folioCita, $idCliente, $idEmisor, $conexion)
{
    //* Obtener datos de la serie del ticket
    $datosTicket = obtenerDatosSerieTicket($serieTicket, $conexion, $idEmisor);
    if (isset($datosTicket['error'])) {
        return ['error' => $datosTicket['error']];
    }

    $idDocumento = $datosTicket['id_documento'];
    $folioTicket = $datosTicket['folio'];

    //* Validar si ya existe un ticket para la cita
    $existeTicket = comprobarExistenciaTicketCita($conexion, $idEmisor, $idDocumento, $folioCita);
    if (isset($existeTicket['error'])) {
        return ['error' => $existeTicket['error']];
    }
    if ($existeTicket['exists']) {
        return $existeTicket['ticket'];
    }

    //* Obtener el último folio disponible
    $ultimoFolioTicket = obtenerUltimoId($conexion, $idEmisor, $folioTicket, $idDocumento, $serieTicket);

    if (isset($ultimoFolioTicket['error'])) {
        return ['error' => $ultimoFolioTicket['error']];
    }

    $query = "INSERT INTO emisores_tickets (
        id_emisor, 
        id_documento, 
        folio_ticket, 
        serie_ticket, 
        id_cita, 
        id_cliente, 
        total, 
        estatus, 
        estatus_factura
    ) VALUES (?, ?, ?, ?, ?, ?, 0.00, 1, 1)";

    $stmt = mysqli_prepare($conexion, $query);
    if (!$stmt) {
        return ['error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)];
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iiiiii",
        $idEmisor,
        $idDocumento,
        $ultimoFolioTicket,
        $serieTicket,
        $folioCita,
        $idCliente
    );

    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        return ['error' => 'Error al insertar el ticket: ' . mysqli_error($conexion)];
    }

    //* Obtener la tupla recién creada
    return obtenerDatosTicketAperturado($ultimoFolioTicket, $idDocumento, $conexion, $idEmisor);
}
function obtenerUltimoId($conexion, $idEmisor, $folioTicket, $idDocumento, $serieTicket)
{
    $query = "SELECT COALESCE(MAX(folio_ticket), ?) AS no_registro 
                    FROM emisores_tickets 
                    WHERE id_emisor = ? AND id_documento = ? AND serie_ticket = ?;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return ['error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)];
    }
    mysqli_stmt_bind_param($stmt, "iiii", $folioTicket, $idEmisor, $idDocumento, $serieTicket);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return ['error' => 'Error al obtener los resultados: ' . mysqli_error($conexion)];
    }
    $max = mysqli_fetch_assoc($result);
    $ultimo = $max['no_registro'] + 1;

    // Cerrar el statement
    mysqli_stmt_close($stmt);

    return $ultimo;
}
function obtenerDatosSerieTicket($serieTicket, $conexion, $idEmisor)
{

    $query = "SELECT id_documento, serie, folio FROM emisores_series WHERE id_partida = ? AND id_emisor = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $serieTicket,
        $idEmisor
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return ['error' => 'Error al obtener los resultados: ' . mysqli_error($conexion)];
    }

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return $datos ?: [];
}
function obtenerDatosTicketAperturado($ultimoFolioTicket, $idDocumento, $conexion, $idEmisor)
{
    $query = "SELECT * FROM emisores_tickets WHERE folio_ticket = ? AND id_emisor = ? AND id_documento=?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $ultimoFolioTicket,
        $idEmisor,
        $idDocumento
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return ['error' => 'Error al obtener los resultados: ' . mysqli_error($conexion)];
    }

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return $datos ?: [];
}
function comprobarExistenciaTicketCita($conexion, $idEmisor, $idDocumento, $idCita)
{
    $query = "SELECT * FROM emisores_tickets WHERE id_cita = ? AND id_emisor = ? AND id_documento = ? AND estatus != 3;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'exists' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'ticket' => null
        ];
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $idCita,
        $idEmisor,
        $idDocumento
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return [
            'exists' => false,
            'error' => 'Error al obtener los resultados: ' . mysqli_error($conexion),
            'ticket' => null
        ];
    }

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return [
        'exists' => !empty($datos),
        'ticket' => $datos ?: null
    ];
}

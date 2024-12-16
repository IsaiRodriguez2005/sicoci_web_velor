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
        //* esta validacion, detecta si esta la accion son las funciones 
        if ($_POST['funcion'] == 'searchTickets') {

            $idEmisor = $_SESSION['id_emisor'];

            if (!isset($_POST['idSerie']) || !isset($_POST['idFolio'])) {
                echo json_encode([
                    'exists' => false,
                    'error' => 'Parámetros insuficientes: idSerie y/o idfolio faltan.',
                    'ticket' => null
                ]);
                exit;
            }

            $datos = buscarTicket($_POST, $idEmisor, $conexion);

            if (isset($datos['error'])) {
                echo json_encode([
                    'error' => $datos['error'],
                ]);
                exit;
            }

            echo json_encode($datos);
            exit;
        }

        if ($_POST['funcion'] == 'traerTicketsTabla') {

            $idEmisor = $_SESSION['id_emisor'];

            $datos = traerTicketsTabla($idEmisor, $conexion);

            if (isset($datos['error'])) {
                echo json_encode([
                    'error' => $datos['error'],
                ]);
                exit;
            }

            echo json_encode($datos);
            exit;
        }
    }
}

function traerTicketsTabla($idEmisor, $conexion)
{

    $diaHoy = new DateTime('today midnight');
    $diaMañana = new DateTime('tomorrow midnight');
    $diaHoyForma = $diaHoy->format('Y-m-d H:i:s');
    $diaMañanaForma = $diaMañana->format('Y-m-d H:i:s');

    $query = "SELECT DISTINCT
                    t.folio_ticket,
                    t.id_documento, 
                    t.total,
                    t.fecha_emision,
                    t.estatus,
                    c.nombre_cliente 
                FROM emisores_tickets t 
                INNER JOIN emisores_clientes c ON c.id_emisor = t.id_emisor AND c.id_cliente = t.id_cliente
                WHERE t.id_emisor = ? AND t.fecha_emision BETWEEN ? AND ?;";

    $stmt = mysqli_prepare($conexion, $query);
    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'tickets' => null
        ];
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $idEmisor,
        $diaHoyForma,
        $diaMañanaForma
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return [
            'success' => false,
            'error' => 'Error al obtener los resultados: ' . mysqli_error($conexion),
            'tickets' => null
        ];
    }

    $tickets = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $url = construct_URL_ticket($row);
        $row['urlTicket'] = $url;
        $tickets[] = $row;
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => !empty($tickets),
        'tickets' => $tickets
    ];
}

function buscarTicket($post, $idEmisor, $conexion)
{
    $idDocumento = $post['idSerie'];
    $folioTicket = $post['idFolio'];

    $query = "SELECT 
                    t.folio_ticket,
                    t.id_documento
                FROM emisores_tickets t 
                INNER JOIN emisores_clientes c ON c.id_emisor = t.id_emisor
                WHERE t.id_emisor = ? AND t.id_documento = ? AND t.folio_ticket = ?;";

    $stmt = mysqli_prepare($conexion, $query);
    if (!$stmt) {
        return [
            'exists' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'ticket' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "iii", $idEmisor, $idDocumento, $folioTicket);
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

    if (empty($datos)) {
        return [
            'exists' => false,
            'error' => 'El ticket no existe o no coincide con los parámetros proporcionados.',
            'ticket' => null
        ];
    }

    $url = construct_URL_ticket($datos);

    return [
        'exists' => !empty($datos),
        'urlTicket' => $url,
        'mensaje' => !empty($datos) ? 'Ticket encontrado con éxito.' : 'No se encontró el ticket.'
    ];
}

function construct_URL_ticket($ticketAperturado)
{
    $folioTicket = $ticketAperturado['folio_ticket'];
    $idDocumento = $ticketAperturado['id_documento'];

    //* Construir la URL con parámetros de consulta
    $url = 'ticket.php?' . http_build_query([
        'folio_ticket' => $folioTicket,
        'id_documento' => $idDocumento
    ]);
    return $url;
}

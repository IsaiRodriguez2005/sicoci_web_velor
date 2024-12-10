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
            $datos = obtenerTextosTicket($_POST, $idEmisor, $conexion);
            if (isset($datos['error'])) {
                echo json_encode([
                    'success' => false,
                    'datosTicket' => $datos['error']
                ]);
                exit;
            }
            echo json_encode([
                'success' => true,
                'datosTicket' => $datos
            ]);
            exit;
        }
        if ($_POST['funcion'] == 'agregarProductoTicket') {
            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = agregarPorductoATicket($_POST, $idEmisor, $conexion);

            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'datosTicket' => $respuesta['error']
                ]);
                exit;
            }
            echo json_encode([
                'success' => true,
                'producto' => $respuesta
            ]);
            exit;
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
    $idDocumento = $post['idDocumento'] ?? null;
    $folioTicket = $post['folioTicket'] ?? null;

    $query = "SELECT
                    es.serie AS clave_serie,
                    ec.nombre_cliente,
                    et.folio_ticket,
                    et.total,
                    et.estatus
                    FROM emisores_tickets et
                        INNER JOIN emisores_series es ON es.id_partida = et.id_documento AND es.id_emisor = et.id_emisor
                        INNER JOIN emisores_agenda ea ON ea.id_folio = et.id_cita AND ea.id_emisor = et.id_emisor 
                        INNER JOIN emisores_clientes ec ON ec.id_cliente = et.id_cliente AND ec.id_emisor = et.id_emisor
                        WHERE et.id_emisor = ? AND et.folio_ticket = ? AND et.id_documento = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $idEmisor,
        $folioTicket,
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

function obtenerUltimoId($conexion, $idEmisor, $folioTicket, $idSerieTicket, $serieTicket)
{
    $query = "SELECT COALESCE(MAX(folio_ticket), ?) AS no_registro 
                    FROM emisores_tickets 
                    WHERE id_emisor = ? AND id_documento = ? AND serie_ticket = ?;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return ['error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)];
    }
    mysqli_stmt_bind_param($stmt, "iiii", $folioTicket, $idEmisor, $idSerieTicket, $serieTicket);
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
function obtenerDatosTicketAperturado($ultimoFolioTicket, $idSerieTicket, $conexion, $idEmisor)
{
    $query = "SELECT * FROM emisores_tickets WHERE folio_ticket = ? AND id_emisor = ? AND id_documento=?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $ultimoFolioTicket,
        $idEmisor,
        $idSerieTicket
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
function comprobarExistenciaTicketCita($conexion, $idEmisor, $idSerieTicket, $idCita)
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
        $idSerieTicket
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


//! Funciones para agregar productos
function agregarPorductoATicket($post, $idEmisor, $conexion)
{
    $folioTicket = $post['folioTicket'];
    $idDocumento = $post['idDocumento'];
    $idProducto = $post['productoId'];
    $cantidad = $post['cantidad'];
    $descripcion = $post['descripcion'] ?? null;

    //* obtener datos necesarios de la tabla de productos
    $datosProd = obtenerDatosPorducto($idProducto, $idEmisor, $conexion);
    $precio = $datosProd['precio'];

    $ivaPorcentaje = $datosProd['iva'];
    $ivaMonto = calcularMontoIVA($ivaPorcentaje, $precio);
    $precioUnitario = $precio + $ivaMonto;
    $importe = $precioUnitario * $cantidad;

    $ultimo = obtenerUltimoIdTicketsDetalles($idEmisor, $conexion, $idDocumento, $folioTicket);
    return;

    $query = "INSERT INTO emisores_tickets_detalles (
                                                        `id_partida`, 
                                                        `id_emisor`, 
                                                        `id_documento`, 
                                                        `folio_ticket`, 
                                                        `id_producto`, 
                                                        `cantidad`, 
                                                        `descripcion`, 
                                                        `precio_unitario`, 
                                                        `importe`, 
                                                        `iva_porcentaje`, 
                                                        `iva_monto`
                                                        ) VALUES (?,?,?,?,?,?,?,?,?,?,?);";
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
        "iiiiifsffff",
        $ultimo,
        $idEmisor,
        $idDocumento,
        $folioTicket,
        $idProducto,
        $cantidad,
        $descripcion,
        $precioUnitafrio,
        $importe,
        $ivaPorcentaje,
        $ivaMonto
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        return ['error' => 'Error al insertar el ticket: ' . mysqli_error($conexion)];
    }

    return true;
}
function obtenerUltimoIdTicketsDetalles($idEmisor, $conexion, $idDocumento, $folioTicket)
{
    $query = "SELECT COALESCE(MAX(id_partida), 0) AS no_registro 
                    FROM emisores_tickets_detalles 
                    WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ?;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return ['error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)];
    }
    mysqli_stmt_bind_param($stmt, "iii", $idEmisor, $idDocumento, $folioTicket);
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
function obtenerDatosPorducto($idProducto, $idEmisor, $conexion)
{
    $query = "SELECT precio, iva FROM productos_servicios WHERE id_producto = ? AND id_emisor = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $idProducto,
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

function calcularMontoIVA($iva, $precio)
{
    return ($iva * $precio) / 100;
}

//! peticiones desde AGENDA
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
function aperturarTicket($serieTicket, $folioCita, $idCliente, $idEmisor, $conexion)
{
    //* Obtener datos de la serie del ticket
    $datosTicket = obtenerDatosSerieTicket($serieTicket, $conexion, $idEmisor);
    if (isset($datosTicket['error'])) {
        return ['error' => $datosTicket['error']];
    }
    $folioTicket = $datosTicket['folio'];
    $serie = $datosTicket['serie'];

    //* Validar si ya existe un ticket para la cita
    $existeTicket = comprobarExistenciaTicketCita($conexion, $idEmisor, $serieTicket, $folioCita);
    if (isset($existeTicket['error'])) {
        return ['error' => $existeTicket['error']];
    }
    if ($existeTicket['exists']) {
        return $existeTicket['ticket'];
    }

    //* Obtener el último folio disponible
    $ultimoFolioTicket = obtenerUltimoId($conexion, $idEmisor, $folioTicket, $serieTicket, $serieTicket);

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
        "iiisii",
        $idEmisor,
        $serieTicket,
        $ultimoFolioTicket,
        $serie,
        $folioCita,
        $idCliente
    );

    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        return ['error' => 'Error al insertar el ticket: ' . mysqli_error($conexion)];
    }

    //* Obtener la tupla recién creada
    return obtenerDatosTicketAperturado($ultimoFolioTicket, $serieTicket, $conexion, $idEmisor);
}

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
        //todo: Datos de los tickets
        if ($_POST['funcion'] == 'getProductosTicket') {
            $idEmisor = $_SESSION['id_emisor'];
            $datos = obtenerProductosTicket($_POST, $idEmisor, $conexion);
            if (isset($datos['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $datos['error']
                ]);
                exit;
            }
            echo json_encode([
                'success' => true,
                'productTicket' => $datos
            ]);
            exit;
        }
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

        //todo: comprobaciones de tickets
        if ($_POST['funcion'] == 'yaExisteTicketDeLaCita') {

            if (!isset($_POST['folio_cita'], $_POST['id_cliente'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Faltan datos obligatorios en la solicitud.'
                ]);
                exit;
            }
            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = comprobarExistenciaDeTicketDeCita($_POST, $idEmisor, $conexion);
            if (isset($respuesta['exists'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            if (!$respuesta['tiene']) {
                echo json_encode([
                    'success' => true,
                    'tiene' => $respuesta['tiene'],
                ]);
                exit;
            }

            $url = construct_URL_ticket($respuesta['ticket']);

            echo json_encode([
                'success' => true,
                'tiene' => $respuesta['tiene'],
                'urlTicket' => $url
            ]);
            exit;
        }

        //todo: agregar y eliminar productos del ticket
        if ($_POST['funcion'] == 'agregarProductoTicket') {
            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = agregarPorductoATicket($_POST, $idEmisor, $conexion);

            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }
            if (isset($respuesta['existe'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'El producto ya existe en el ticket'
                ]);
                exit;
            }
            echo json_encode([
                'success' => true,
                'producto' => $respuesta
            ]);
            exit;
        }
        if ($_POST['funcion'] == 'eliminarProductoTicket') {
            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = eliminarPorductoDelTicket($_POST, $idEmisor, $conexion);

            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'borrado' => $respuesta
            ]);
            exit;
        }

        //todo: cancelaciones de ticket
        if ($_POST['funcion'] == 'cancelarTicket') {
            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = cancelarTicket($_POST, $idEmisor, $conexion);
            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'cancelado' => $respuesta
            ]);
            exit;
        }

        //todo: aperturar ticket
        if ($_POST['funcion'] == 'getURLticketSinCita') {
            
            if (!isset($_SESSION['id_emisor'], $_POST['serieTicket'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Parámetros insuficientes.'
                ]);
                exit();
            }
            $idEmisor = $_SESSION['id_emisor'];

            $ticketAper = aperturarTicketSinCita($_POST, $idEmisor, $conexion);
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
            $folioCita = $_GET['folioCita'] ?? null;
            $idCliente = $_GET['idCliente'] ?? null;

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
//! funciones de lsta de tickets
function aperturarTicketSinCita($post, $idEmisor, $conexion)
{
    $idDocumento = $post['serieTicket'];
    $folioCita = null;
    $idCliente = null;

    //* Obtener datos de la serie del ticket
    $datosSerie = obtenerDatosSerieTicket($idDocumento, $conexion, $idEmisor);
    if (isset($datosSerie['error'])) {
        return ['error' => $datosSerie['error']];
    }
    $folioTicket = $datosSerie['folio'];
    $serie = $datosSerie['serie'];

    //* Obtener el último folio disponible
    $ultimoFolioTicket = obtenerUltimoId($conexion, $idEmisor, $folioTicket, $idDocumento);

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
        $idDocumento,
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
    return obtenerDatosTicketAperturado($ultimoFolioTicket, $idDocumento, $conexion, $idEmisor);
}
//! funciones para eliminar productos y cancelaciones de tickets
function cancelarTicket($post, $idEmisor, $conexion)
{

    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $contrasenia = $post['claveTrab'] ?? null;
    $estatus = 3;

    if (!$folioTicket || !$idDocumento || !$contrasenia) {
        return [
            'exists' => false,
            'error' => 'Faltan datos requeridos para procesar la solicitud.'
        ];
    }

    $existeTicket = comprobarExistenciaDeTicket($post, $idEmisor, $conexion);
    if (isset($existeTicket['error'])) {
        return [
            'error' => $existeTicket['error']
        ];
    }
    if (!$existeTicket['exist']) {
        return [
            'error' => $existeTicket['error']
        ];
    }

    $contraseñaValida = comprobarAccionUsuario($contrasenia, $_SESSION, $conexion);
    if (isset($contraseñaValidat['error'])) {
        return [
            'error' => $contraseñaValida['error']
        ];
    }
    if (!$contraseñaValida['success']) {
        return [
            'error' => 'Credenciales incorrectos'
        ];
    }

    $query = "UPDATE emisores_tickets 
                            SET estatus = ? 
                            WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ? AND estatus != 3;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'exists' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)
        ];
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iiii",
        $estatus,
        $idEmisor,
        $idDocumento,
        $folioTicket
    );

    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        return [
            'error' => 'Error al cancelar el ticket: ' . mysqli_error($conexion),
        ];
    }

    return true;
}
function eliminarPorductoDelTicket($post, $idEmisor, $conexion)
{
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $idProducto = $post['productoId'] ?? null;

    if (!$folioTicket || !$idDocumento || !$idProducto) {
        return [
            'exists' => false,
            'error' => 'Faltan datos requeridos para procesar la solicitud.'
        ];
    }

    $existeProducto = comprobarExistenciProductoEnTicket($idProducto, $idDocumento, $folioTicket, $idEmisor, $conexion);

    if (isset($existeProducto['error'])) {
        return ['error' => $existeProducto['error']];
    }
    if (!$existeProducto['exists']) {
        return [
            'error' => 'El producto no existe en la compra, o el id no coincide con un poducto existente.'
        ];
    }

    $query = "DELETE FROM emisores_tickets_detalles WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ? AND id_producto = ?;";
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
        "iiii",
        $idEmisor,
        $idDocumento,
        $folioTicket,
        $idProducto,
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        return [
            'error' => 'Error al insertar el ticket: ' . mysqli_error($conexion),
        ];
    }

    return true;
}
//! comprobaciones de acciones
function comprobarAccionUsuario($contrasenia, $session, $conexion)
{
    $idUsuario = $session['id_usuario'];
    $idEmisor = $session['id_emisor'];

    $query = "SELECT `password` FROM usuarios WHERE id_emisor = ? AND id_usuario = ?;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)
        ];
    }

    mysqli_stmt_bind_param($stmt, "ii", $idEmisor, $idUsuario);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_error($conexion)
        ];
    }

    //? Obtener resultado
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        return [
            'aceptar' => false,
            'error' => 'Error al obtener el resultado: ' . mysqli_error($conexion)
        ];
    }

    $datos = mysqli_fetch_assoc($result);

    //? Cerrar stmt
    mysqli_stmt_close($stmt);

    // Verificar la contraseña
    if (!($contrasenia == $datos['password'])) {
        return [
            'success' => false,
            'error' => 'Usuario o contraseña incorrectos.'
        ];
    }

    // Retornar éxito
    return [
        'success' => true,
        'error' => null
    ];
}

//! comprobaciones de tickets
function comprobarExistenciaDeTicket($post, $idEmisor, $conexion)
{
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;

    if (!$folioTicket || !$idDocumento) {
        return [
            'exist' => false,
            'error' => 'Faltan datos requeridos para procesar la solicitud.'
        ];
    }

    $query = "SELECT folio_ticket FROM emisores_tickets WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ? AND estatus != 3;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'exist' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)
        ];
    }

    mysqli_stmt_bind_param($stmt, "iii", $idEmisor, $idDocumento, $folioTicket);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [
            'exist' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_error($conexion)
        ];
    }

    //? Obtener resultado
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        return [
            'exist' => false,
            'error' => 'Error al obtener el resultado: ' . mysqli_error($conexion)
        ];
    }

    $datos = mysqli_fetch_assoc($result);

    //? Cerrar stmt
    mysqli_stmt_close($stmt);

    return [
        'exist' => !empty($datos),
        'error' => null
    ];
}
function comprobarExistenciaDeTicketDeCita($post, $idEmisor, $conexion)
{
    $idCita = $post['folio_cita'];
    $idCliente = $post['id_cliente'];

    $query = "SELECT * FROM emisores_tickets WHERE id_cita = ? AND id_emisor = ? AND id_cliente = ? AND estatus NOT IN (3);";
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
        $idCliente
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return [
            'exists' => true,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_error($conexion),
            'ticket' => null
        ];
    }

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$datos) {
        return [
            'tiene' => false,
            'ticket' => null
        ];
    }

    return [
        'tiene' => true,
        'ticket' => $datos
    ];
}
//! Funciones para obtener datos
function obtenerTextosTicket($post, $idEmisor, $conexion)
{
    $idDocumento = $post['idDocumento'] ?? null;
    $folioTicket = $post['folioTicket'] ?? null;

    $query = "SELECT
                    es.serie AS clave_serie,
                    COALESCE(ec.nombre_cliente, 'PÚBLICO EN GENERAL') AS nombre_cliente,
                    et.folio_ticket,
                    et.total,
                    et.estatus,
                    COALESCE((
                        SELECT SUM(cantidad) 
                        FROM emisores_tickets_detalles 
                        WHERE id_emisor = ? AND folio_ticket = ? AND id_documento = ?
                    ), 0) AS total_articulos 
                    FROM emisores_tickets et
                        INNER JOIN emisores_series es ON es.id_partida = et.id_documento AND es.id_emisor = et.id_emisor
                        LEFT JOIN emisores_agenda ea ON ea.id_folio = et.id_cita AND ea.id_emisor = et.id_emisor 
                        LEFT JOIN emisores_clientes ec ON ec.id_cliente = et.id_cliente AND ec.id_emisor = et.id_emisor
                        WHERE et.id_emisor = ? AND et.folio_ticket = ? AND et.id_documento = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iiiiii",
        $idEmisor,
        $folioTicket,
        $idDocumento,
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
function obtenerProductosTicket($post, $idEmisor, $conexion)
{
    $idDocumento = $post['idDocumento'] ?? null;
    $folioTicket = $post['folioTicket'] ?? null;

    $query = "SELECT
                    ed.id_partida,
                    ed.id_producto,
                    ed.cantidad,
                    ps.nombre as nombreProducto,
                    (ed.precio_unitario * (100 + ed.iva_porcentaje) / 100) as precio,
                    ed.importe
                    FROM emisores_tickets_detalles ed
                        INNER JOIN emisores_tickets et 
                            ON ed.id_emisor = et.id_emisor 
                            AND ed.id_documento = et.id_documento 
                            AND ed.folio_ticket = et.folio_ticket
                        INNER JOIN productos_servicios ps ON ps.id_emisor = ed.id_emisor AND ps.id_producto = ed.id_producto
                        WHERE ed.id_emisor = ? AND ed.folio_ticket = ? AND ed.id_documento = ?
                        GROUP BY ed.cantidad, ps.nombre, ed.precio_unitario, ed.importe;";
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

    $datos = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        $datos[] = $fila; // Agrega cada fila al arreglo
    }

    mysqli_stmt_close($stmt);
    return $datos ?: [];
}

//! Funciones para agregar productos
function agregarPorductoATicket($post, $idEmisor, $conexion)
{
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $idProducto = $post['productoId'] ?? null;
    $cantidad = $post['cantidad'] ?? null;
    $descripcion = $post['descripcion'] ?? null;

    $ultimo = obtenerUltimoIdTicketsDetalles($idEmisor, $conexion, $idDocumento, $folioTicket);

    if (!$folioTicket || !$idDocumento || !$idProducto || !$cantidad) {
        return [
            'exists' => false,
            'error' => 'Faltan datos requeridos para procesar la solicitud.'
        ];
    }

    $existeProducto = comprobarExistenciProductoEnTicket($idProducto, $idDocumento, $folioTicket, $idEmisor, $conexion);
    if (isset($existeProducto['error'])) {
        return ['error' => $existeProducto['error']];
    }
    if ($existeProducto['exists']) {
        return [
            'error' => $existeProducto['mensaje']
        ];
    }
    //* obtener datos necesarios de la tabla de productos
    $datosProd = obtenerDatosPorducto($idProducto, $idEmisor, $conexion);
    if (!$datosProd) {
        return [
            'exists' => false,
            'error' => 'No se encontraron datos del producto con ID: ' . $idProducto
        ];
    }
    $precio = $datosProd['precio'];

    $ivaPorcentaje = $datosProd['iva'];
    $ivaMonto = calcularMontoIVA($ivaPorcentaje, $precio);
    $precioUnitario = $precio + $ivaMonto;
    $importe = $precioUnitario * $cantidad;



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
        "iiiiidsdddd",
        $ultimo,
        $idEmisor,
        $idDocumento,
        $folioTicket,
        $idProducto,
        $cantidad,
        $descripcion,
        $precio,
        $importe,
        $ivaPorcentaje,
        $ivaMonto
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        return [
            'error' => 'Error al insertar el ticket: ' . mysqli_error($conexion),
        ];
    }

    return obtenerProductoDeLaCompra($ultimo, $idDocumento, $folioTicket, $idEmisor, $conexion);
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
function comprobarExistenciProductoEnTicket($idProducto, $idDocumento, $folioTicket, $idEmisor, $conexion)
{
    $query = "SELECT id_producto FROM emisores_tickets_detalles WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ? AND id_producto = ?;";
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
        "iiii",
        $idEmisor,
        $idDocumento,
        $folioTicket,
        $idProducto
    );
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return [
            'exists' => false,
            'error' => 'Error al obtener los resultados: ' . mysqli_error($conexion),
        ];
    }

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return [
        'exists' => !empty($datos),
        'mensaje' => 'El producto ya existe en el ticket'
    ];
}
function obtenerProductoDeLaCompra($idPartida, $idDocumento, $folioTicket, $idEmisor, $conexion)
{
    $query = "SELECT    ed.id_producto,
                        ed.cantidad,
                        ps.nombre as nombreProducto,
                        (ed.precio_unitario * (100 + ed.iva_porcentaje) / 100) as precio,
                        ed.importe
                        FROM emisores_tickets_detalles ed 
                            INNER JOIN productos_servicios ps ON ps.id_emisor = ed.id_emisor AND ps.id_producto = ed.id_producto
                            WHERE ed.id_partida = ? AND ed.id_emisor = ? AND ed.id_documento = ? AND ed.folio_ticket = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iiii",
        $idPartida,
        $idEmisor,
        $idDocumento,
        $folioTicket
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
function aperturarTicket($idDocumento, $folioCita, $idCliente, $idEmisor, $conexion)
{
    //* Obtener datos de la serie del ticket
    $datosSerie = obtenerDatosSerieTicket($idDocumento, $conexion, $idEmisor);
    if (isset($datosSerie['error'])) {
        return ['error' => $datosSerie['error']];
    }
    $folioTicket = $datosSerie['folio'];
    $serie = $datosSerie['serie'];

    //* Validar si ya existe un ticket para la cita
    $existeTicket = comprobarExistenciaTicketCita($conexion, $idEmisor, $idDocumento, $folioCita);
    if (isset($existeTicket['error'])) {
        return ['error' => $existeTicket['error']];
    }
    if ($existeTicket['exists']) {
        return $existeTicket['ticket'];
    }

    //* Obtener el último folio disponible
    $ultimoFolioTicket = obtenerUltimoId($conexion, $idEmisor, $folioTicket, $idDocumento);

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
        $idDocumento,
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
    return obtenerDatosTicketAperturado($ultimoFolioTicket, $idDocumento, $conexion, $idEmisor);
}
function obtenerUltimoId($conexion, $idEmisor, $folioTicket, $idSerieTicket)
{
    $query = "SELECT COALESCE(MAX(folio_ticket), ?) AS no_registro 
                    FROM emisores_tickets 
                    WHERE id_emisor = ? AND id_documento = ?;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return ['error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)];
    }
    mysqli_stmt_bind_param($stmt, "iii", $folioTicket, $idEmisor, $idSerieTicket);
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

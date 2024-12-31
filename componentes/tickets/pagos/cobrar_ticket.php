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
        //todo: pagos
        if ($_POST['funcion'] == 'traerFormasPago') {
            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = traerFormasPago($conexion);

            if (!isset($respuesta['success'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'data' => $respuesta['data']
            ]);
            exit;
        }
        if ($_POST['funcion'] == 'registrarPago') {

            $respuesta = registrarPago($_POST, $_SESSION,$conexion);

            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'data' => $respuesta
            ]);
            exit;
        }
        if ($_POST['funcion'] == 'traerPagosTicket') {

            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = traerPagosTickets($_POST, $idEmisor, $conexion);

            if (!isset($respuesta['success'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'data' => $respuesta['data']
            ]);
            exit;
        }
    }
}
//! funciones pagos
function registrarPago($post, $session, $conexion){
    
    if (!isset($post['idDocumento'], $post['folioTicket'], $post['cantPago'], $post['metoPago'])) {
        return [
            'success' => false,
            'error' => 'Datos incompletos para registrar el pago'
        ];
    }

    //? datos necesarios
    $idEmisor = $session['id_emisor'];
    $ultimo = obtenerUltimoIDpago($post, $idEmisor, $conexion);
    $idDocumento = $post['idDocumento'];
    $folioTicket = $post['folioTicket'];
    $metoPago = $post['metoPago'];
    $cantPago = floatval($post['cantPago']);
    $moneda = 'MXN';
    $cobrado_por = $session['nombre_usuario'];

    //? comprobar si necesitara cambio 
    $resultadoCambio = necesitaCambio($post, $idEmisor, $conexion);
    if (!$resultadoCambio['success']) {
        return $resultadoCambio; // Devuelve el error de necesitaCambio
    }

    //? separamos la data del resultado
    $dataCambio = $resultadoCambio['data'];
    $montoARegistrar = $dataCambio['monto_a_registrar'];
    $cambio = $dataCambio['cambio'];

    //? si es trasnferencia electronica, comprueba si el pago es mayor al ticket
    if($metoPago == 3 && $cambio > 0){
        return [
            'success' => true,
            'error' => 'La transferencia electrónica no puede ser mayor al precio del ticket.'
        ];
    }

    //? montos pendientes de ticket o si ya esta completamnte pagado
    $montoPendiente = $dataCambio['falta_cobrar'] - $montoARegistrar;

    if ($montoARegistrar <= 0) {
        return [
            'success' => false,
            'error' => 'El ticket ya está completamente pagado.'
        ];
    }

    $query = "INSERT INTO `emisores_tickets_pagos`(
                                                    `id_pago`, 
                                                    `id_emisor`, 
                                                    `id_documento`, 
                                                    `folio_ticket`, 
                                                    `forma_pago_id`, 
                                                    `moneda`, 
                                                    `monto`, 
                                                    `cobrado_por`
                                                ) VALUES (?,?,?,?,?,?,?,?);";

    $resultado = ejecutarConsultaPreparada(
        $conexion,
        $query,
        "iiiiisds",
        [$ultimo, $idEmisor, $idDocumento, $folioTicket, $metoPago, $moneda, $montoARegistrar, $cobrado_por]
    );

    if (!$resultado['success']) {
        return $resultado;
    }

    if ($montoPendiente <= 0) {
        $resultadoEstatus = cambiarEstatusTicket(4, $post, $idEmisor, $conexion);
        if (!$resultadoEstatus['success']) {
            return $resultadoEstatus; // Devuelve error de cambiarEstatusTicket si falla
        }
    }

    return [
        'success' => true,
        'message' => 'Pago registrado con éxito.',
        'monto_registrado' => $montoARegistrar,
        'cambio' => $cambio,
        'monto_pendiente' => $dataCambio['falta_cobrar'] - $montoARegistrar
    ];
}
function cambiarEstatusTicket($estatus, $post, $idEmisor, $conexion){
    $idDocumento = $post['idDocumento'];
    $folioTicket = $post['folioTicket'];

    $query = "UPDATE emisores_tickets 
                    SET estatus = ? 
                    WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ?;";
    
    $resultado = ejecutarConsultaPreparada(
        $conexion,
        $query,
        "iiii",
        [$estatus,$idEmisor, $idDocumento, $folioTicket]
    );

    if (!$resultado['success']) {
        return $resultado;
    }

    return [
        'success'=> true,
        'mensaje' => 'Ticket pagado'
    ];
}
function traerPagosTickets($post, $idEmisor, $conexion){

    $idDocumento = $post['idDocumento'];
    $folioTicket = $post['folioTicket'];

    $query = "SELECT 
                    t.id_pago,
                    t.monto, 
                    f.descripcion,
                    t.fecha_pago
                FROM emisores_tickets_pagos t
                INNER JOIN _cat_sat_forma_pago f ON f.id_forma = t.forma_pago_id
                WHERE id_emisor = ? AND id_documento = ? AND folio_ticket = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param(
        $stmt, 
        "iii",
        $idEmisor,
        $idDocumento,
        $folioTicket
    );

    if (!mysqli_stmt_execute($stmt)) {
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt),
            'data' => null
        ];
    }

    $result = mysqli_stmt_get_result($stmt);

    $datos = [];
    if ($result) {
        while ($fila = mysqli_fetch_assoc($result)) {
            $datos[] = $fila;
        }
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => !empty($datos),
        'data' => $datos,
        'error' => null
    ];
}
function obtenerUltimoIDpago($post, $idEmisor, $conexion){

    $idDocumento = $post['idDocumento'];
    $folioTicket = $post['folioTicket'];

    $query = "SELECT COALESCE(MAX(id_pago), 0) AS no_registro 
                    FROM emisores_tickets_pagos 
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

//! funciones formas de pago
function traerFormasPago($conexion)
{
    $estatus = 1;

    $query = "SELECT 
                    id_forma,
                    clave_forma,
                    descripcion
                FROM _cat_sat_forma_pago
                WHERE estatus = ? AND id_forma IN (1,3);";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "i",$estatus);

    if (!mysqli_stmt_execute($stmt)) {
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt),
            'data' => null
        ];
    }

    $result = mysqli_stmt_get_result($stmt);

    $datos = [];
    if ($result) {
        while ($fila = mysqli_fetch_assoc($result)) {
            $datos[] = $fila;
        }
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => !empty($datos),
        'data' => $datos,
        'error' => null
    ];
}

//! cambios 
function necesitaCambio($post, $idEmisor, $conexion){

    if (!isset($post['idDocumento'], $post['folioTicket'], $post['cantPago'])) {
        return [
            'success' => false,
            'error' => 'Datos incompletos para calcular el cambio.'
        ];
    }

    $idDocumento = $post['idDocumento'];
    $folioTicket = $post['folioTicket'];
    $cantPago = floatval($post['cantPago']);

    $query = "SELECT 
                    COALESCE(SUM(tp.monto), 0) AS total_cobrado, 
                    t.total AS total_ticket, 
                    (t.total - COALESCE(SUM(tp.monto), 0)) AS falta_cobrar 
                FROM emisores_tickets t
                LEFT JOIN emisores_tickets_pagos tp 
                    ON t.id_emisor = tp.id_emisor 
                    AND t.id_documento = tp.id_documento 
                    AND t.folio_ticket = tp.folio_ticket 
                WHERE t.id_emisor = ? AND t.id_documento = ? AND t.folio_ticket = ?;";
    $resultado = ejecutarConsultaPreparada(
        $conexion,
        $query,
        "iii",
        [$idEmisor, $idDocumento, $folioTicket]
    );

    if (!$resultado['success']) {
        return $resultado; // Devolver el error de `ejecutarConsultaPreparada`
    }

    $result = $resultado['data'];

    if ($fila = mysqli_fetch_assoc($result)) {

        $totalTicket = floatval($fila['total_ticket']);
        $totalCobrado = floatval($fila['total_cobrado']);
        $faltaCobrar = floatval($fila['falta_cobrar']);
        $montoARegistrar = min($cantPago, $faltaCobrar);
        $cambio = max(0, $cantPago - $faltaCobrar);


        return [
            'success' => true,
            'data' => [
                'total_cobrado' => $totalCobrado,
                'total_ticket' => $totalTicket,
                'falta_cobrar' => $faltaCobrar,
                'monto_a_registrar' => $montoARegistrar,
                'cambio' => $cambio
            ]
        ];
    }

    return [
        'success' => false,
        'error' => 'No se encontró información para el ticket especificado.'
    ];
}
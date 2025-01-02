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
        //todo: datos convenios
        if ($_POST['funcion'] == 'traerConvenios') {

            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = traerConvenios($idEmisor, $conexion);

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

        //todo: agregar y eliminar convenios ticket
        if ($_POST['funcion'] == 'eliminarConvenioTicket') {
            $idEmisor = $_SESSION['id_emisor'];

            $respuesta = eliminarConvenioProducto($_POST, $idEmisor, $conexion);

            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'mensaje' => $respuesta['mensaje']
            ]);
            exit;
        }
        if ($_POST['funcion'] == 'agregarConvenioTicket') {
            $idEmisor = $_SESSION['id_emisor'];

            $respuesta = agregarConvenioProducto($_POST, $idEmisor, $conexion);

            if (isset($respuesta['error'])) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $respuesta['error']
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'mensaje' => $respuesta['mensaje']
            ]);
            exit;
        }
    }
}

//! funciones convenios

function eliminarConvenioProducto($post, $idEmisor, $conexion)
{

    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $idProducto = $post['idProducto'] ?? null;

    $idConvenio = null;
    $tipoConvenio = null;
    $cantidadDescuento = 0.00;
    $descuento = 0.00;

    $query = "UPDATE emisores_tickets_detalles 
                SET 
                    id_convenio = ?,
                    tipo_convenio = ?,
                    cantidad_descuento = ?,
                    descuento = ?
                WHERE id_emisor = ? AND folio_ticket = ? AND id_documento = ? AND id_producto = ?;";
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
        "iiddiiii",
        $idConvenio,
        $tipoConvenio,
        $cantidadDescuento,
        $descuento,
        $idEmisor,
        $folioTicket,
        $idDocumento,
        $idProducto
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt),
            'ticket' => null
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'error' => null,
        'mensaje' => 'Actualización realizada con éxito'
    ];
}
function agregarConvenioProducto($post, $idEmisor, $conexion)
{

    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $idProducto = $post['idProducto'] ?? null;
    $idConvenio = $post['idConvenio'] ?? null;

    $infoConvenio = taerInfoConvenio($idConvenio, $idEmisor, $conexion);
    if(!isset($infoConvenio['success'])){
        return [
            'success'=> true,
            'error' => $infoConvenio['error']
        ];
    }

    
    $infoProductoTicket = obtenerProductoDeLaCompra($idProducto, $idDocumento, $folioTicket, $idEmisor, $conexion);
    if(!isset($infoConvenio['success'])){
        return [
            'success'=> true,
            'error' => $infoConvenio['error']
        ];
    }

    //? calculamos el descuento dependiendo de el tipo de descuento 
    $tipoConvenio = $infoConvenio['data']['tipo'];
    if($tipoConvenio == 1) {
        //! cantidad
        $cantidadDescuento = $infoConvenio['data']['cost_consul'];
        $descuento = $cantidadDescuento;
    }
    if($tipoConvenio == 2) {
        //! porcentaje
        $cantidadDescuento = $infoConvenio['data']['pct_consul'];
        $descuento = (($cantidadDescuento / 100) * $infoProductoTicket['importe']);
    }
    

    $query = "UPDATE emisores_tickets_detalles 
                SET 
                    id_convenio = ?,
                    tipo_convenio = ?,
                    cantidad_descuento = ?,
                    descuento = ?
                WHERE id_emisor = ? AND folio_ticket = ? AND id_documento = ? AND id_producto = ?;";
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
        "iiddiiii",
        $idConvenio,
        $tipoConvenio,
        $cantidadDescuento,
        $descuento,
        $idEmisor,
        $folioTicket,
        $idDocumento,
        $idProducto
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt),
            'ticket' => null
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'error' => null,
        'mensaje' => 'Actualización realizada con éxito'
    ];
}

function taerInfoConvenio($idConvenio, $idEmisor, $conexion)
{
    $estatus = 1;

    $query = "SELECT 
                    id_convenio, 
                    nombre, 
                    tipo, 
                    pct_consul, 
                    cost_consul
                FROM emisores_convenios
                WHERE id_emisor = ? AND id_convenio = ? AND estatus = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "iii", $idEmisor, $idConvenio, $estatus);

    if (!mysqli_stmt_execute($stmt)) {
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt),
            'data' => null
        ];
    }

    $result = mysqli_stmt_get_result($stmt);

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return [
        'success' => !empty($datos),
        'data' => $datos,
        'error' => null
    ];
}

function traerConvenios($idEmisor, $conexion)
{
    $estatus = 1;

    $query = "SELECT 
                    id_convenio, 
                    nombre, 
                    tipo, 
                    pct_consul, 
                    cost_consul
                FROM emisores_convenios
                WHERE id_emisor = ? AND estatus = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "ii", $idEmisor, $estatus);

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

//! funciones productos
function obtenerProductoDeLaCompra($idProducto, $idDocumento, $folioTicket, $idEmisor, $conexion)
{
    $query = "SELECT    ed.id_producto,
                        ed.cantidad,
                        ps.nombre as nombreProducto,
                        (ed.precio_unitario * (100 + ed.iva_porcentaje) / 100) as precio,
                        ed.importe
                        FROM emisores_tickets_detalles ed 
                            INNER JOIN productos_servicios ps ON ps.id_emisor = ed.id_emisor AND ps.id_producto = ed.id_producto
                            WHERE ed.id_producto = ? AND ed.id_emisor = ? AND ed.id_documento = ? AND ed.folio_ticket = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "iiii",
        $idProducto,
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
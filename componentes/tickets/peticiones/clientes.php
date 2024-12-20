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
        //todo: datos clientes
        if ($_POST['funcion'] == 'traerClientes') {

            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = traerClientes($idEmisor, $conexion);

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

        //todo: agregar y eliminar cliente
        if ($_POST['funcion'] == 'agregarClienteTicket') {
            $idEmisor = $_SESSION['id_emisor'];

            $respuesta = agregarClienteTicket($_POST, $idEmisor, $conexion);

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
        if ($_POST['funcion'] == 'eliminarClienteTicket') {
            $idEmisor = $_SESSION['id_emisor'];

            $respuesta = eliminarClienteTicket($_POST, $idEmisor, $conexion);

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

//! funciones clientes
function eliminarClienteTicket($post, $idEmisor, $conexion){
    $idCliente = null;
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;

    $query = "UPDATE emisores_tickets
                SET 
                    id_cliente = ?
                WHERE id_emisor = ? AND folio_ticket = ? AND id_documento = ? ;";
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
        $idCliente,
        $idEmisor,
        $folioTicket,
        $idDocumento
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt)
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'error' => null,
        'mensaje' => 'Actualización realizada con éxito'
    ];
}
function agregarClienteTicket($post, $idEmisor, $conexion){

    $idCliente = $post['idCliente'] ?? null;
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;

    $query = "UPDATE emisores_tickets
                SET 
                    id_cliente = ?
                WHERE id_emisor = ? AND folio_ticket = ? AND id_documento = ? ;";
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
        $idCliente,
        $idEmisor,
        $folioTicket,
        $idDocumento
    );
    //* Ejecutar la consulta de inserción
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt)
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'error' => null,
        'mensaje' => 'Actualización realizada con éxito'
    ];
}
function traerClientes($idEmisor, $conexion)
{
    $estatus = 1;

    $query = "SELECT 
                    id_cliente as id,
                    nombre_cliente as nombre
                FROM emisores_clientes
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

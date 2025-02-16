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

        if ($_POST['funcion'] == 'traerPerfilesFacturacionCliente') {

            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = traerPerfilesFacturacionCliente($_POST, $idEmisor, $conexion);

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
        if ($_POST['funcion'] == 'traerRegimenFiscalPorClave') {

            $respuesta = traerRegimenFiscalPorClave($_POST, $conexion);

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
        if ($_POST['funcion'] == 'traerDireccionFormText') {

            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = traerDireccionFormText($idEmisor, $_POST, $conexion);

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
        if ($_POST['funcion'] == 'traerCatUsoCFDI') {

            $respuesta = traerCatUsoCFDI($conexion);

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
        if ($_POST['funcion'] == 'traerCatMetodosPago') {

            $respuesta = traerCatMetodosPago($conexion);

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
        if ($_POST['funcion'] == 'traerCatFormasPago') {

            $respuesta = traerCatFormasPago($conexion);

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
        if ($_POST['funcion'] == 'facturarTicket') {

            $idEmisor = $_SESSION['id_emisor'];
            $respuesta = facturarTicket($_POST, $idEmisor, $conexion);

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

//! funciones 
function facturarTicket($post, $idEmisor, $conexion)
{
    $folioTicket = $post['folioTicket'];
    $idDocumento = $post['idDocumento'];
    $perfilFiscal = $post['dataTim'];
    $datosTicket = obtenerDatosTicket(['idDocumento' => $idDocumento, 'folioTicket' => $folioTicket], $idEmisor, $conexion);


    // $sql = "INSERT INTO ";
    // echo '<pre>';
    // var_dump($datosTicket);
    // echo '</pre>';
}
function obtenerDatosTicket($post, $idEmisor, $conexion)
{
    $idDocumento = $post['idDocumento'];
    $folioTicket = $post['folioTicket'];

    $query = "SELECT 
                    es.serie AS clave_serie,
                    et.total, 
                    COALESCE(et.id_cliente, 0) AS id_cliente,
                    COALESCE((SELECT SUM(etd.descuento)
                        FROM emisores_tickets_detalles etd
                        WHERE etd.folio_ticket = et.folio_ticket 
                                AND etd.id_emisor = et.id_emisor 
                                AND etd.id_documento = et.id_documento
                    ),0) AS total_descuento,
                    COALESCE(
                    (SELECT SUM(tp.monto)
                        FROM emisores_tickets_pagos tp
                        WHERE tp.folio_ticket = et.folio_ticket 
                            AND tp.id_emisor = et.id_emisor 
                            AND tp.id_documento = et.id_documento
                            AND tp.forma_pago_id = 1
                    ), 0) AS tcefect,
                    COALESCE(
                    (SELECT SUM(tp.monto)
                        FROM emisores_tickets_pagos tp
                        WHERE tp.folio_ticket = et.folio_ticket 
                            AND tp.id_emisor = et.id_emisor 
                            AND tp.id_documento = et.id_documento
                            AND tp.forma_pago_id = 3
                    ), 0) AS tctrans,
                    COALESCE(
                    (SELECT SUM(tp.monto)
                        FROM emisores_tickets_pagos tp
                        WHERE tp.folio_ticket = et.folio_ticket 
                            AND tp.id_emisor = et.id_emisor 
                            AND tp.id_documento = et.id_documento
                    ), 0) AS total_cobrado
                FROM emisores_tickets et 
                    INNER JOIN emisores_series es 
                        ON es.id_partida = et.id_documento 
                            AND es.id_emisor = et.id_emisor 
                    LEFT JOIN emisores_agenda ea 
                        ON ea.id_folio = et.id_cita 
                            AND ea.id_emisor = et.id_emisor 
                    LEFT JOIN emisores_clientes ec 
                        ON ec.id_cliente = et.id_cliente 
                            AND ec.id_emisor = et.id_emisor
                    LEFT JOIN emisores_tickets_detalles etd 
                        ON etd.folio_ticket = et.folio_ticket 
                            AND etd.id_emisor = et.id_emisor 
                            AND etd.id_documento = et.id_documento
                    LEFT JOIN emisores_tickets_pagos tp 
                        ON tp.folio_ticket = et.folio_ticket 
                            AND tp.id_emisor = et.id_emisor 
                            AND tp.id_documento = et.id_documento
                WHERE et.id_emisor = ? AND et.folio_ticket = ? AND et.id_documento = ?;";

    $stmt = mysqli_prepare($conexion, $query);
    if (!$stmt) {
        return ['error' => 'Error en la preparación de la consulta: ' . mysqli_error($conexion)];
    }
    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $idEmisor,
        $folioTicket,
        $idDocumento
    );

    if (!mysqli_stmt_execute($stmt)) {
        return ['error' => 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt)];
    }

    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        return ['error' => 'Error al obtener los resultados: ' . mysqli_error($conexion)];
    }

    $datos = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return $datos ?: [];
}
function traerPerfilesFacturacionCliente($post, $idEmisor, $conexion)
{
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;

    $query = "SELECT 
                    pf.id_cliente,
                    pf.id_perfil,
                    pf.rfc,
                    pf.nombre_social,
                    pf.calle,
                    pf.no_exterior,
                    pf.no_interior,
                    pf.codigo_postal,
                    pf.colonia,
                    pf.municipio,
                    pf.estado,
                    pf.pais,
                    pf.regimen_fiscal,
                    pf.metodo_pago,
                    pf.forma_pago,
                    pf.uso_cfdi
                FROM emisores_tickets t 
                INNER JOIN emisores_clientes_facturacion pf ON 
                    pf.id_cliente = t.id_cliente AND pf.id_emisor = t.id_emisor
                WHERE t.id_emisor = ? AND t.folio_ticket = ? AND t.id_documento = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "iii", $idEmisor, $folioTicket, $idDocumento);

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
function traerCatFormasPago($conexion)
{

    $estado = [1, 2];
    $query = "SELECT 
                    id_forma as id,
                    clave_forma,
                    descripcion
                FROM _cat_sat_forma_pago
                WHERE estatus = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "i", $estado);

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
function traerCatMetodosPago($conexion)
{

    $estado = [1, 2];
    $query = "SELECT 
                    id_metodo as id,
                    clave_metodo,
                    descripcion
                FROM _cat_sat_metodo_pago
                WHERE estatus = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "i", $estado);

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
function traerCatUsoCFDI($conexion)
{

    $estado = [1, 2];
    $query = "SELECT 
                    id_uso as id,
                    clave_uso,
                    descripcion,
                    fisica, moral,
                    regimen_fiscal
                FROM _cat_sat_uso_cfdi
                WHERE estatus = ?;";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "i", $estado);

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
function traerRegimenFiscalPorClave($post, $conexion)
{
    $claveReg = $post['claveRegimen'];

    $query = "SELECT  distinct descripcion
                FROM _cat_sat_regimen_fiscal
                WHERE clave_regimen = ?";

    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "s", $claveReg);

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

function traerDireccionFormText($idEmisor, $post, $conexion)
{
    $folioTicket = $post['folioTicket'] ?? null;
    $idDocumento = $post['idDocumento'] ?? null;
    $idPerfil = $post['idPerfil'];
    $query = "SELECT DISTINCT
                    pf.calle,
                    pf.no_exterior,
                    pf.no_interior,
                    pf.codigo_postal,
                    csl.clave_localidad AS clavelocalidad,
                    csl.nombre_localidad AS localidad,
                    csc.nombre_colonia AS colonia,
                    csm.nombre_municipio AS municipio,
                    cse.nombre_estado AS estado,
                    csp.descripcion AS pais
                FROM emisores_tickets t 
                INNER JOIN emisores_clientes_facturacion pf 
                    ON pf.id_cliente = t.id_cliente AND pf.id_emisor = t.id_emisor
                INNER JOIN _cat_sat_colonias csc 
                    ON csc.clave_colonia = pf.colonia AND csc.codigo_postal = pf.codigo_postal
                INNER JOIN _cat_sat_municipios csm 
                    ON csm.clave_municipio = pf.municipio AND csm.clave_estado = pf.estado
                INNER JOIN _cat_sat_estados cse 
                    ON  cse.clave_estado = pf.estado 
                INNER JOIN _cat_sat_pais csp 
                    ON csp.clave_pais = pf.pais AND csp.clave_pais = pf.pais
                LEFT JOIN _cat_sat_codigos_postales cscp
                    ON cscp.codigo_postal = pf.codigo_postal
                LEFT JOIN _cat_sat_localidades csl 
                    ON csl.clave_localidad = cscp.clave_localidad AND csl.clave_estado = pf.estado
                WHERE t.id_emisor = ? 
                AND t.folio_ticket = ?
                AND t.id_documento = ? 
                AND pf.id_perfil = ?;";
    $stmt = mysqli_prepare($conexion, $query);

    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion),
            'data' => null
        ];
    }

    mysqli_stmt_bind_param($stmt, "iiii", $idEmisor, $folioTicket, $idDocumento, $idPerfil);

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

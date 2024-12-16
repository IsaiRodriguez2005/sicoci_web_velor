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
        if ($_POST['funcion'] == 'existenciaSeriesTickets') {
            $idEmisor = $_SESSION['id_emisor'];
            $idDocumento = $_POST['id_documento'];
            echo existenciaSeriesTickets($idEmisor, $idDocumento, $conexion);
        }
        if ($_POST['funcion'] == 'cargarSeriesSelect') {
            $idEmisor = $_SESSION['id_emisor'];
            $datos = cargarSeriesSelect($idEmisor, $conexion);
            echo json_encode($datos);
        }
    }
}
function cargarSeriesSelect($idEmisor, $conexion)
{
    $idDocumento = 7;

    $query = "SELECT
                    id_partida,
                    serie
                FROM emisores_series 
                WHERE id_emisor = ? AND id_documento = ?;";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $idEmisor,
        $idDocumento
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return ['error' => 'Error al obtener los resultados: ' . mysqli_error($conexion)];
    }

    $series = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $series[] = $row;
    }

    mysqli_stmt_close($stmt);

    return [
        'success' => !empty($series),
        'series' => $series
    ];
}
function existenciaSeriesTickets($idEmisor, $idDocumento, $conexion)
{
    $sqlCliente = "SELECT 
                            s.id_partida as id, 
                            s.id_documento,
                            s.serie,
                            s.codigo_postal as codigoPostal,
                            s.estatus,
                            d.nombre_documento as documento
                        FROM emisores_series s INNER JOIN _cat_erp_documentos d ON d.id_documento = s.id_documento
                        WHERE s.id_emisor = ? AND s.id_documento = ? AND d.estatus = 1;";
    $stmt = mysqli_prepare($conexion, $sqlCliente);
    mysqli_stmt_bind_param($stmt, "ii", $idEmisor, $idDocumento);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $folios = [];

    while ($serie = mysqli_fetch_assoc($result)) {
        $folios[] = $serie;
    }

    mysqli_stmt_close($stmt);

    return json_encode([
        "success" => !empty($folios),
        "folios" => $folios
    ]);
}

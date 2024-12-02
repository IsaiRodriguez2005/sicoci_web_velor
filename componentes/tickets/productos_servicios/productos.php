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
        if ($_POST['funcion'] == 'cargarProdutos') {
            $idEmisor = $_SESSION['id_emisor'];
            echo traer_productos_servicios($idEmisor, $conexion);
        }

        if ($_POST['funcion'] == 'cargarProductoPorId') {
            $idEmisor = $_SESSION['id_emisor'];
            $idProducto = $_POST['id_producto'];
            echo traer_producto_servicio_por_id($idProducto, $idEmisor, $conexion);
        }
    }
}

function traer_productos_servicios($idEmisor, $conexion)
{
    $sqlCliente = "SELECT 
                            id_producto,
                            nombre,
                            precio,
                            iva
                        FROM productos_servicios 
                        WHERE id_emisor = ? AND estatus = 1 AND ((tipo = 2 AND stock < 0) OR (tipo != 2));";
    $stmt = mysqli_prepare($conexion, $sqlCliente);
    mysqli_stmt_bind_param($stmt, "i", $idEmisor);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $productos = [];

    while ($producto = mysqli_fetch_assoc($result)) {
        $productos[] = $producto;
    }

    mysqli_stmt_close($stmt);

    return json_encode([
        "success" => !empty($productos),
        "productos" => $productos
    ]);
}
function traer_producto_servicio_por_id($idProducto, $idEmisor, $conexion)
{
    $sqlCliente = "SELECT 
                            id_producto,
                            nombre,
                            precio,
                            iva
                        FROM productos_servicios 
                        WHERE id_emisor = ? AND estatus = 1 AND ((tipo = 2 AND stock < 0) OR (tipo != 2));";
    $stmt = mysqli_prepare($conexion, $sqlCliente);
    mysqli_stmt_bind_param($stmt, "i", $idEmisor);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $productos = [];

    while ($producto = mysqli_fetch_assoc($result)) {
        $productos[] = $producto;
    }

    mysqli_stmt_close($stmt);

    return json_encode([
        "success" => !empty($productos),
        "productos" => $productos
    ]);
}

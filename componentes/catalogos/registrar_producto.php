<?php
session_start();
require("../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    if ($_POST['tipo_gestion'] == 0) {
        $sqlMax = "SELECT COALESCE(MAX(id_producto),0) AS no_registro FROM productos_servicios WHERE id_emisor=" . $_SESSION['id_emisor'];
        $resMax = mysqli_query($conexion, $sqlMax);
        $ultimo = mysqli_fetch_array($resMax);

        // print_r($_POST);
        // return;
        $nuevo_id = $ultimo['no_registro'] + 1;
        $idEmisor = $_SESSION['id_emisor'];
        $tipo = $_POST['tipo'];
        $nombre = strtoupper($_POST['nombre']);
        $stock = intval($_POST['stock']);
        $stockMinimo = intval($_POST['stock_minimo']);
        $precio = floatval($_POST['precio']);
        $iva = floatval($_POST['iva']);
        $claveProducto = $_POST['clave_producto'];
        $claveMedida = $_POST['clave_medida'];

        $sqlMedida = "INSERT INTO productos_servicios (
                                                        id_emisor, 
                                                        id_producto, 
                                                        tipo, 
                                                        nombre, 
                                                        stock, 
                                                        stock_minimo, 
                                                        estatus, 
                                                        precio, 
                                                        iva, 
                                                        clave_producto_sat, 
                                                        clave_medida_sat
                                                        )
                                                    VALUES (?,?,?,?,?,?,1,?,?,?,?);";

        $stmt = mysqli_prepare($conexion, $sqlMedida);
        mysqli_stmt_bind_param(
            $stmt,
            "iiisiiddss",
            $idEmisor,
            $nuevo_id,
            $tipo,
            $nombre,
            $stock,
            $stockMinimo,
            $precio,
            $iva, 
            $claveProducto,
            $claveMedida
        );
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            echo "ok";
        } else {
            echo "error";
        }
    } else {
        $sqlUpdate = "UPDATE productos_servicios SET nombre='" . strtoupper($_POST['nombre']) . "', tipo=" . $_POST['tipo'] . ", stock=" . $_POST['stock'] . ", stock_minimo=" . $_POST['stock_minimo'] . ", precio=" . $_POST['precio'] . ", iva=" . $_POST['iva'] . " WHERE id_producto=" . $_POST['tipo_gestion'] . " AND id_emisor=" . $_SESSION['id_emisor'];
        $res = mysqli_query($conexion, $sqlUpdate);

        if ($res) {
            echo "actualizado";
        } else {
            echo "error";
        }
    }
}

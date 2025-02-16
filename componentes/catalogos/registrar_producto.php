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
        $cobroAutomatico = $_POST['cobrarAutomatico'];

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
                                                        clave_medida_sat,
                                                        cobro_automatico
                                                        )
                                                    VALUES (?,?,?,?,?,?,1,?,?,?,?, ?);";

        $stmt = mysqli_prepare($conexion, $sqlMedida);
        mysqli_stmt_bind_param(
            $stmt,
            "iiisiiddssi",
            $idEmisor,
            $nuevo_id,
            $tipo,
            $nombre,
            $stock,
            $stockMinimo,
            $precio,
            $iva,
            $claveProducto,
            $claveMedida,
            $cobroAutomatico
        );
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            echo "ok";
        } else {
            echo "error";
        }
    } else {
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        // Preparar la consulta con placeholders
        $sqlUpdate = "UPDATE productos_servicios 
                      SET nombre = ?, 
                          tipo = ?, 
                          stock = ?, 
                          stock_minimo = ?, 
                          precio = ?, 
                          iva = ?,
                          cobro_automatico = ?
                      WHERE id_producto = ? 
                        AND id_emisor = ?";

        $stmt = mysqli_prepare($conexion, $sqlUpdate);

        if (!$stmt) {
            die("Error al preparar la consulta: " . mysqli_error($conexion));
        }

        $nombre = strtoupper($_POST['nombre']);
        $tipo = $_POST['tipo'];
        $stock = $_POST['stock'];
        $stockMinimo = $_POST['stock_minimo'];
        $precio = $_POST['precio'];
        $iva = $_POST['iva'];
        $idProducto = $_POST['tipo_gestion'];
        $idEmisor = $_SESSION['id_emisor'];
        $cobroAutomatico = $_POST['cobrarAutomatico'] == true ? 1 : 0;


        mysqli_stmt_bind_param(
            $stmt,
            "siiiddiii",
            $nombre,
            $tipo,
            $stock,
            $stockMinimo,
            $precio,
            $iva,
            $cobroAutomatico,
            $idProducto,
            $idEmisor
        );

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            echo "actualizado";
        } else {
            echo "error";
        }

        // Cerrar el statement
        mysqli_stmt_close($stmt);
    }
}

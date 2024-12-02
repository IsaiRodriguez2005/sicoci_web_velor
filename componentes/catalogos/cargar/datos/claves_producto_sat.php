<?php
session_start();
require("../../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    $claveProd = $_POST['claveProducto'];
    $claveMed = $_POST['claveMedida'];

    // Inicializamos el arreglo de respuesta
    $respuesta = [];

    // Consulta para clave de producto
    $consProdu = "SELECT 
                        clave_producto,
                        descripcion as descr_producto
                    FROM _cat_sat_productos
                    WHERE clave_producto = ?;";
    $stmt = mysqli_prepare($conexion, $consProdu);
    mysqli_stmt_bind_param($stmt, "s", $claveProd);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado) {
        // Guardamos el resultado de clave_producto
        $filas = mysqli_fetch_assoc($resultado);
        if ($filas) {
            $respuesta['producto'] = $filas; // Guardamos con la clave 'producto'
        }
    }

    // Consulta para clave de medida
    $consMedida = "SELECT 
                        clave_medida,
                        descripcion as descr_medida
                    FROM _cat_sat_medidas
                    WHERE clave_medida = ?;";
    $stmt = mysqli_prepare($conexion, $consMedida);
    mysqli_stmt_bind_param($stmt, "s", $claveMed);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado) {
        // Guardamos el resultado de clave_medida
        $filas = mysqli_fetch_assoc($resultado);
        if ($filas) {
            $respuesta['medida'] = $filas; // Guardamos con la clave 'medida'
        }
    }

    // Retornamos la respuesta como un JSON
    echo json_encode($respuesta);
}

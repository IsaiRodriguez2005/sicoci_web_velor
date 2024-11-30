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
        if ($_POST['funcion'] == 'cargarClavesSAT') {
            $descript = $_POST['descripcion'];
            echo traer_claves_SAT($conexion, $descript);
        }
    }
}

function traer_claves_SAT($conexion, $descripcion)
{
    header('Content-Type: application/json; charset=utf-8');
    $descripcion = "%{$descripcion}%";
    $sqlProductos = "SELECT 
                            clave_producto as clave,
                            descripcion
                        FROM _cat_sat_productos 
                        WHERE descripcion LIKE ? AND estatus = 1
                        ORDER BY descripcion
                        LIMIT 10;";
    $stmt = mysqli_prepare($conexion, $sqlProductos);
    mysqli_stmt_bind_param($stmt, "s", $descripcion);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $claves = [];

    while ($clave = mysqli_fetch_assoc($result)) {
        $claves[] = $clave;
    }

    mysqli_stmt_close($stmt);

    return json_encode([
        "success" => !empty($claves),
        "claves" => $claves
    ]);
}

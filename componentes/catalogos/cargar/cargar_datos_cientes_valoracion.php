<?php
session_start();
require("../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    $consCliente = "SELECT fec_nac, ocupacion, est_civ
                                FROM emisores_clientes
                                WHERE id_cliente = " . $_POST['id_cliente'] . " AND id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consCliente);

    
    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }

    //echo $respuesta;
    echo json_encode($respuesta);
}

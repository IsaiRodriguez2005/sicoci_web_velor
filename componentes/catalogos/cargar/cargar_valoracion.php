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

    $consTerapeutas = "SELECT e.*, c.nombre_cliente, c.telefono 
                                FROM emisores_historial_expediente AS e 
                                INNER JOIN emisores_clientes AS c ON c.id_cliente = e.id_cliente 
                                WHERE e.id_cliente = " . $_POST['id_cliente'] . " AND e.folio = ".$_POST['folio']." AND e.id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consTerapeutas);

    
    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }

    echo json_encode($respuesta);
}

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

    $consCita = "SELECT a.*, c.nombre_cliente
                                FROM emisores_agenda AS a 
                                INNER JOIN emisores_clientes AS c ON c.id_cliente = a.id_cliente AND c.id_emisor = a.id_emisor
                                WHERE a.id_folio = " . $_POST['id_folio'] . " AND a.id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consCita);

    
    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }

    //echo $respuesta;
    echo json_encode($respuesta);
}

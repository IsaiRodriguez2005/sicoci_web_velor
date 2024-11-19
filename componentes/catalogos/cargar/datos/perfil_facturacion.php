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

    $consPerfil = "SELECT * FROM emisores_clientes_facturacion
                                WHERE id_cliente = " . $_POST['id_cliente'] . " AND id_emisor = " . $_SESSION['id_emisor'] . " AND id_perfil = ".$_POST['id_perfil'].";";
    $resultado = mysqli_query($conexion, $consPerfil);

    
    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }

    //echo $respuesta;
    echo json_encode($respuesta);
}

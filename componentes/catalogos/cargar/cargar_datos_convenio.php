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

    $consConvenio = "SELECT * FROM emisores_convenios
                                WHERE id_convenio = " . $_POST['id_convenio'] . " AND id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consConvenio);

    
    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }

    //echo $respuesta;
    echo json_encode($respuesta);
}

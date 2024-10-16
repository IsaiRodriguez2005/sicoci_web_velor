<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
        $consultar = "DELETE FROM productos_servicios WHERE id_producto = ".$_POST['id_producto']." AND id_emisor = ".$_SESSION['id_emisor'];
        $resultado = mysqli_query($conexion, $consultar);
    }
?>
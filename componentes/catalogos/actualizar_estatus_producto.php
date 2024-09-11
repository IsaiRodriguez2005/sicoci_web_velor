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
        $sqlCliente = "UPDATE productos_servicios SET estatus=".$_POST['codigo_estatus']." WHERE id_producto=".$_POST['id_producto']." AND id_emisor=".$_SESSION['id_emisor'];
        echo $sqlCliente;
        mysqli_query($conexion, $sqlCliente);
    }
?>
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
        $sql = "UPDATE emisores_series SET estatus=".$_POST['estatus']." WHERE id_partida = ".$_POST['id_partida']." AND id_emisor=".$_SESSION['id_emisor'];
        mysqli_query($conexion, $sql);
    }
?>
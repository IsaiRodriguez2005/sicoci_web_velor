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
        unlink("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_minuta.png");

        if(!file_exists("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_minuta.png"))
        {
            $sql = "UPDATE emisores_minutas SET hoja_membretada = 0 WHERE id_emisor = ".$_SESSION['id_emisor'];
            $resultado = mysqli_query($conexion, $sql);
        }
    }
?>
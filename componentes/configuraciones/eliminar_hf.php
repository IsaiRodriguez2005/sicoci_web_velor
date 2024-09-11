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
        if($_POST['tipo'] == 1)
        {
            unlink("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_cotizacion.png");

            if(!file_exists("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_cotizacion.png"))
            {
                $sql = "UPDATE emisores_cotizaciones SET hoja_membretada = 2 WHERE id_cotizacion = ".$_POST['id_cotizacion'];
                $resultado = mysqli_query($conexion, $sql);
            }
        }
        else
        {
            unlink("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/firma_cotizacion.png");

            if(!file_exists("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/firma_cotizacion.png"))
            {
                $sql = "UPDATE emisores_cotizaciones SET firma_autoriza = 2 WHERE id_cotizacion = ".$_POST['id_cotizacion'];
                $resultado = mysqli_query($conexion, $sql);
            }
        }
    }
?>
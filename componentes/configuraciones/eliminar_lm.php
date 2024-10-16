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
            unlink("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/logo.png");

            if(!file_exists("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/logo.png"))
            {
                $sql = "UPDATE emisores_configuraciones SET logo = 0, tipo_logo = 0 WHERE id_emisor = ".$_SESSION['id_emisor'];
                $resultado = mysqli_query($conexion, $sql);

                echo '
                    <center>
                        <img src="img/no-image.png" width="150px" heigth="150px">
                    </center>
                ';
            }
        }
        else
        {
            unlink("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/marca.png");

            if(!file_exists("../../emisores/".$_SESSION['id_emisor']."/archivos/generales/marca.png"))
            {
                $sql = "UPDATE emisores_configuraciones SET marca = 0, tipo_marca = 0 WHERE id_emisor = ".$_SESSION['id_emisor'];
                $resultado = mysqli_query($conexion, $sql);

                echo '
                    <center>
                        <img src="img/no-image.png" width="150px" heigth="150px">
                    </center>
                ';
            }
        }
    }
?>
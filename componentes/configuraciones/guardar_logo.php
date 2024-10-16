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
        $tipo = $_POST['tipo'];
        $tempLogo = $_FILES['logo']['tmp_name'];

        if(empty($tempLogo))
        {
           $sql = "UPDATE emisores_configuraciones SET tipo_logo = ".$tipo." WHERE id_emisor = ".$_SESSION['id_emisor']; 
           mysqli_query($conexion, $sql);

           echo "1";
        }
        else
        {
            if($_FILES['logo']['size'] > 100000)
            {
                echo "3";
            }
            else
            {
                $logo_subido = move_uploaded_file($tempLogo, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/logo.png");   
                if($logo_subido)
                {
                    $bandera_logo = 1;
                }
                else
                {
                    $bandera_logo = 2;
                }

                if($bandera_logo = 1)
                {
                    $sql = "UPDATE emisores_configuraciones SET logo = 1, tipo_logo = ".$tipo." WHERE id_emisor = ".$_SESSION['id_emisor']; 
                    mysqli_query($conexion, $sql);
                    
                    echo "1";
                }
                else
                {
                    $sql = "UPDATE emisores_configuraciones SET tipo_logo = ".$tipo." WHERE id_emisor = ".$_SESSION['id_emisor']; 
                    mysqli_query($conexion, $sql);

                    echo "2";
                }
            }
        }
    }
?>
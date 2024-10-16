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
        $tempMarca = $_FILES['marca']['tmp_name'];

        if(empty($tempMarca))
        {
           $sql = "UPDATE emisores_configuraciones SET tipo_marca = ".$tipo." WHERE id_emisor = ".$_SESSION['id_emisor']; 
           mysqli_query($conexion, $sql);

           echo "1";
        }
        else
        {
            if($_FILES['marca']['size'] > 100000)
            {
                echo "3";
            }
            else
            {
                $marca_subido = move_uploaded_file($tempMarca, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/marca.png");   
                if($marca_subido)
                {
                    $bandera_marca = 1;
                }
                else
                {
                    $bandera_marca = 2;
                }

                if($bandera_marca = 1)
                {
                    $sql = "UPDATE emisores_configuraciones SET marca = 1, tipo_marca = ".$tipo." WHERE id_emisor = ".$_SESSION['id_emisor']; 
                    mysqli_query($conexion, $sql);
                    
                    echo "1";
                }
                else
                {
                    $sql = "UPDATE emisores_configuraciones SET tipo_marca = ".$tipo." WHERE id_emisor = ".$_SESSION['id_emisor']; 
                    mysqli_query($conexion, $sql);

                    echo "2";
                }
            }
        }
    }
?>
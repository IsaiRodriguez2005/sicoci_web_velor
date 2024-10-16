<?php
    session_start();
    require("../../conexion.php");
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
        $caracteres = array("&", '"', "'");
        $reemplazo = array("&amp;", "&quot;", "&apos;");

        if($_POST['nombre_ocupacion'])
        {

            
            $selectMAX = "SELECT COALESCE(MAX(id_ocupacion),0) AS no_registro FROM emisores_ocupaciones WHERE id_emisor = ".$_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;

            $insertOcupaciones = "INSERT INTO emisores_ocupaciones VALUES(".$ultimo.", ".$_SESSION['id_emisor'].", '".strtoupper($_POST['nombre_ocupacion'])."',1)";
            $resultado=mysqli_query($conexion, $insertOcupaciones);
            if($resultado)
            {
                echo "ok";
            }
            else
            {
                echo "error";
            }
        }
        else
        {

        }
    }
?>
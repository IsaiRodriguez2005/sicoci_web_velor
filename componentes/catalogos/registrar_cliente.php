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
        $caracteres = array("&", '"', "'");
        $reemplazo = array("&amp;", "&quot;", "&apos;");
        $nuevo_social = str_replace($caracteres, $reemplazo, strtoupper($_POST['nombre_social']));

        if($_POST['id_cliente'] == 0)
        {
            $fecha_alta = date("Y-m-d");

            $selectMAX = "SELECT COALESCE(MAX(id_cliente),0) AS no_registro FROM emisores_clientes WHERE id_emisor=".$_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;

            $insertCliente = "INSERT INTO emisores_clientes VALUES(".$ultimo.", ".$_SESSION['id_emisor'].",'".trim($nuevo_social)."','".strtolower($_POST['correo'])."','".$_POST['telefono']."', '".$fecha_alta."', 1)";
            $resultado=mysqli_query($conexion, $insertCliente);
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
            $updateCliente = "UPDATE emisores_clientes SET nombre_cliente='".trim($nuevo_social)."', correo='".strtolower($_POST['correo'])."', telefono='".$_POST['telefono']."' WHERE id_cliente=".$_POST['id_cliente']." AND id_emisor=".$_SESSION['id_emisor'];
            $resultado=mysqli_query($conexion, $updateCliente);
            if($resultado)
            {
                echo "ok";
            }
            else
            {
                echo "error";
            }
        }
    }
?>
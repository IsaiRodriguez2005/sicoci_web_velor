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

        if($_POST['id_consultorio'] == 0)
        {
            $fecha_alta = date("Y-m-d");

            $selectMAX = "SELECT COALESCE(MAX(id_consultorio),0) AS no_registro FROM emisores_consultorios WHERE id_emisor = ".$_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;

            $insertConsultorios = "INSERT INTO emisores_consultorios VALUES(".$_SESSION['id_emisor'].", ".$ultimo.", '".strtoupper($_POST['nombre'])."','".$fecha_alta."')";
            $resultado=mysqli_query($conexion, $insertConsultorios);
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
            $updateCliente = "UPDATE emisores_clientes SET id_emisor=".$_SESSION['id_emisor'];
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
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
        if($_POST['tipo_gestion'] == 0)
        {
            $sqlMax = "SELECT COALESCE(MAX(id_producto),0) AS no_registro FROM productos_servicios WHERE id_emisor=".$_SESSION['id_emisor'];
            $resMax = mysqli_query($conexion, $sqlMax);
            $ultimo = mysqli_fetch_array($resMax);

            $nuevo_id = $ultimo['no_registro'] + 1;
            
            $sqlInsert = "INSERT INTO productos_servicios VALUES(".$_SESSION['id_emisor'].",".$nuevo_id.",".$_POST['tipo'].",'".strtoupper($_POST['nombre'])."',".$_POST['stock'].",".$_POST['stock_minimo'].",1,".$_POST['precio'].",".$_POST['iva'].");";
            $res = mysqli_query($conexion, $sqlInsert);

            if($res)
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
            $sqlUpdate = "UPDATE productos_servicios SET nombre='".strtoupper($_POST['nombre'])."', tipo=".$_POST['tipo'].", stock=".$_POST['stock'].", stock_minimo=".$_POST['stock_minimo'].", precio=".$_POST['precio'].", iva=".$_POST['iva']." WHERE id_producto=".$_POST['tipo_gestion']." AND id_emisor=".$_SESSION['id_emisor'];
            $res = mysqli_query($conexion, $sqlUpdate);

            if($res)
            {
                echo "actualizado";
            }
            else
            {
                echo "error";
            }
        }
    }
?>
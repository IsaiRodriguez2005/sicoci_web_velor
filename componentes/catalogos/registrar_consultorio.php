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

            $insertCliente = "INSERT INTO emisores_clientes VALUES(".$ultimo.", ".$_SESSION['id_emisor'].", '".strtoupper(trim($_POST['rfc']))."','".trim($nuevo_social)."','".strtoupper(trim($_POST['calle']))."','".strtoupper(trim($_POST['no_exterior']))."','".strtoupper(trim($_POST['no_interior']))."','".$_POST['codigo_postal']."','".strtoupper(trim($_POST['colonia']))."','".strtoupper($_POST['estado'])."','".strtoupper($_POST['municipio'])."','".strtoupper($_POST['pais'])."','".$_POST['regimen']."','".$_POST['metodo_pago']."','".$_POST['forma_pago']."', '".$_POST['uso_cfdi']."','".strtolower($_POST['correo'])."','".$_POST['telefono']."',".$_POST['tipo_cliente'].", '".$fecha_alta."', 1)";
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
            $updateCliente = "UPDATE emisores_clientes SET rfc='".strtoupper(trim($_POST['rfc']))."', nombre_social='".trim($nuevo_social)."', calle='".strtoupper(trim($_POST['calle']))."', no_exterior='".strtoupper(trim($_POST['no_exterior']))."', no_interior='".strtoupper(trim($_POST['no_interior']))."', codigo_postal='".strtoupper($_POST['codigo_postal'])."', colonia='".strtoupper(trim($_POST['colonia']))."', municipio='".strtoupper($_POST['municipio'])."', estado='".strtoupper($_POST['estado'])."', pais='".strtoupper($_POST['pais'])."', regimen_fiscal='".strtoupper($_POST['regimen'])."', metodo_pago='".strtoupper($_POST['metodo_pago'])."', forma_pago='".strtoupper($_POST['forma_pago'])."', uso_cfdi='".strtoupper($_POST['uso_cfdi'])."', correo='".strtolower($_POST['correo'])."', telefono='".$_POST['telefono']."', tipo_cliente=".$_POST['tipo_cliente']." WHERE id_cliente=".$_POST['id_cliente']." AND id_emisor=".$_SESSION['id_emisor'];
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
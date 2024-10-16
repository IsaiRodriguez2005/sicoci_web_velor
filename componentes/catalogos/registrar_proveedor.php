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
        //var_dump($_POST['nombre_comercial']);
        if($_POST['id_proveedor'] == 0)
        {
            // buscador de nombre
            $existe_proveedor = "SELECT * FROM emisores_proveedores WHERE nombre_comercial = '" .$_POST['nombre_comercial'] . "';";
            $res_exis_prov = mysqli_query($conexion, $existe_proveedor);
            // regresa el numero de nombres iguales que hay
            $provedores_existentes = $res_exis_prov->num_rows;

            // si no hay un regitro de proveedor, hara la insercion
            if($provedores_existentes < 1)
            {
                $fecha_alta = date("Y-m-d");
                $selectMAX = "SELECT COALESCE(MAX(id_proveedor),0) AS no_registro FROM emisores_proveedores WHERE id_emisor=".$_SESSION['id_emisor'];
                $resMAX = mysqli_query($conexion, $selectMAX);
                $max = mysqli_fetch_array($resMAX);
                $ultimo = $max['no_registro'] + 1;
    
                $insertProveedor = "INSERT INTO emisores_proveedores VALUES(".$ultimo.", ".$_SESSION['id_emisor'].",'".strtoupper($_POST['nombre_comercial'])."','".strtoupper($_POST['calle'])."','".strtoupper($_POST['no_exterior'])."','".strtoupper($_POST['no_interior'])."','".$_POST['codigo_postal']."','".strtoupper($_POST['colonia'])."','".strtoupper($_POST['estado'])."','".strtoupper($_POST['municipio'])."','".strtoupper($_POST['pais'])."','".$_POST['regimen']."','".strtolower($_POST['correo'])."', '".$fecha_alta."', 1, '".$_POST['telefono']."')";
                $resultado = mysqli_query($conexion, $insertProveedor);
                if($resultado)
                {
                    echo "ok";
                }
                else
                {
                    echo "error";
                }
            }
            else // si no, dara el mensaje de existente, que mandara el mensaje de error  
            {
                echo "existe";
            }
        }
        else
        {
            $updateCliente = "UPDATE emisores_proveedores SET nombre_comercial='".strtoupper($_POST['nombre_comercial'])."', calle='".strtoupper($_POST['calle'])."', no_exterior='".strtoupper($_POST['no_exterior'])."', no_interior='".strtoupper($_POST['no_interior'])."', codigo_postal='".strtoupper($_POST['codigo_postal'])."', colonia='".strtoupper($_POST['colonia'])."', municipio='".strtoupper($_POST['municipio'])."', estado='".strtoupper($_POST['estado'])."', pais='".strtoupper($_POST['pais'])."', regimen_fiscal='".strtoupper($_POST['regimen'])."', correo='".strtolower($_POST['correo'])."', telefono = '".strtolower($_POST['telefono'])."' WHERE id_proveedor=".$_POST['id_proveedor']." AND id_emisor=".$_SESSION['id_emisor'];
            $resultado = mysqli_query($conexion, $updateCliente);
            if($resultado)
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
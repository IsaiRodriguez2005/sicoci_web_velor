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
        
        if($_POST['id_personal'] == 0)
        {
            // buscador de nombre
            $existe_personal = "SELECT * FROM emisores_personal WHERE nombre_personal = '" .$_POST['nombre_personal'] . "';";
            $res_exis_pers = mysqli_query($conexion, $existe_personal);
            // regresa el numero de nombres iguales que hay
            $personal_existente = $res_exis_pers->num_rows;

            // si no hay un regitro de personal, hara la insercion
            if($personal_existente < 1)
            {
                $fecha_alta = date("Y-m-d");
                $selectMAX = "SELECT COALESCE(MAX(id_personal),0) AS no_registro FROM emisores_personal WHERE id_emisor=".$_SESSION['id_emisor'];
                $resMAX = mysqli_query($conexion, $selectMAX);
                $max = mysqli_fetch_array($resMAX);
                $ultimo = $max['no_registro'] + 1;
    
                $insertPersonal = "INSERT INTO emisores_personal VALUES(".$ultimo.", ".$_SESSION['id_emisor'].",'".strtoupper($_POST['nombre_personal'])."',".intval($_POST['tipo_personal']).",'".strtoupper($_POST['calle'])."','".strtoupper($_POST['no_exterior'])."','".strtoupper($_POST['no_interior'])."','".$_POST['codigo_postal']."','".strtoupper($_POST['colonia'])."','".strtoupper($_POST['estado'])."','".strtoupper($_POST['municipio'])."','".strtoupper($_POST['pais'])."','".strtolower($_POST['correo'])."', '".$_POST['telefono']."','".$fecha_alta."', 1)";
                $resultado = mysqli_query($conexion, $insertPersonal);
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
            $updateCliente = "UPDATE emisores_personal SET nombre_personal='".strtoupper($_POST['nombre_personal'])."', tipo='".$_POST['tipo_personal']."', calle='".strtoupper($_POST['calle'])."', no_exterior='".strtoupper($_POST['no_exterior'])."', no_interior='".strtoupper($_POST['no_interior'])."', codigo_postal='".strtoupper($_POST['codigo_postal'])."', colonia='".strtoupper($_POST['colonia'])."', municipio='".strtoupper($_POST['municipio'])."', estado='".strtoupper($_POST['estado'])."', pais='".strtoupper($_POST['pais'])."', correo='".strtolower($_POST['correo'])."', telefono='".strtolower($_POST['telefono'])."' WHERE id_personal=".$_POST['id_personal']." AND id_emisor=".$_SESSION['id_emisor'];
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
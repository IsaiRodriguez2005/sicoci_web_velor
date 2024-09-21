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
        
        if($_POST['id_personal'])
        {
                $fecha_alta = date("Y-m-d");
                $selectMAX = "SELECT COALESCE(MAX(id_permiso),0) AS no_registro FROM emisores_personal_permisos WHERE id_emisor=".$_SESSION['id_emisor'];
                $resMAX = mysqli_query($conexion, $selectMAX);
                $max = mysqli_fetch_array($resMAX);
                $ultimo = $max['no_registro'] + 1;
    
                $insertPermiso = "INSERT INTO emisores_personal_permisos VALUES(".$ultimo.", ".$_SESSION['id_emisor'].",'".intval($_POST['id_personal'])."','".$_POST['fecha_permiso']."','".strtoupper($_POST['motivo_permiso'])."','".$fecha_alta."', 1)";
                $resultado = mysqli_query($conexion, $insertPermiso);
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
            /*
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
                */
        }


    }

?>
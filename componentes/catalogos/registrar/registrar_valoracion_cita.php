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
        
        if($_POST['id_cliente'] != 0)
        {

            isset($_POST['toximanias']);
            $fecha = date("Y-m-d");

            $selectMAX = "SELECT COALESCE(MAX(folio),0) AS no_registro FROM emisores_historial_expediente WHERE id_emisor = ".$_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;

            
            $valorcionSQL = "INSERT INTO emisores_historial_expediente (`folio`, 
                                                                            `id_emisor`, 
                                                                            `fecha_emision`, 
                                                                            `id_cliente`, 
                                                                            `edad`, 
                                                                            `id_ocupacion`, 
                                                                            `estado_civil`, 
                                                                            `taxicomanias`, 
                                                                            `motivo_consulta`, 
                                                                            `actividad_fisica`,
                                                                            `ta`, 
                                                                            `fc`, 
                                                                            `fr`, 
                                                                            `oxigeno`, 
                                                                            `temperatura`, 
                                                                            `glucosa`, 
                                                                            `farmacos`, 
                                                                            `diagnostico_medico`, 
                                                                            `escala_eva`) 
                                                                            VALUES (
                                                                            $ultimo, 
                                                                            ".intval($_SESSION['id_emisor']).",
                                                                            '".$fecha."',
                                                                            ".intval($_POST['id_cliente']).",
                                                                            ".intval($_POST['edad']).",
                                                                            ".intval($_POST['id_ocupacion']).",
                                                                            ".intval($_POST['estado_civil']).",
                                                                            '".strtoupper($_POST['toximanias'])."',
                                                                            '".strtoupper($_POST['motivo_consulta'])."',
                                                                            '".strtoupper($_POST['act_fisica'])."',
                                                                            '".strtoupper($_POST['ta'])."',
                                                                            '".strtoupper($_POST['fc'])."',
                                                                            '".strtoupper($_POST['fr'])."',
                                                                            '".strtoupper($_POST['satO2'])."',
                                                                            '".strtoupper($_POST['temp'])."',
                                                                            '".strtoupper($_POST['glucosa'])."',
                                                                            '".strtoupper($_POST['farmacos'])."',
                                                                            '".strtoupper($_POST['diagnostico_medico'])."',
                                                                            ".intval($_POST['escalaDolor'])."
                                                                                );";
            $resValoracion = mysqli_query($conexion, $valorcionSQL);
            

            if($resValoracion)
            {
    
                $buscarCita = "UPDATE emisores_agenda SET estatus = 3 WHERE id_cliente = ".$_POST['id_cliente']." AND id_folio = ".$_POST['id_folio']." AND id_emisor = " .$_SESSION['id_emisor'];
                $resultado = mysqli_query($conexion, $buscarCita);
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
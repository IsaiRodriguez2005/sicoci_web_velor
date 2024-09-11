<?php
    session_start();
    require("../conexion.php");

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
            $sqlRepetido = "SELECT COUNT(*) as existe FROM emisores_expediente WHERE id_emisor = ".$_SESSION['id_emisor']." AND nombre_documento = '".strtoupper($_POST['nombre_documento'])."' AND tipo_catalogo=".$_POST['tipo_catalogo']."";
            $resRepetido = mysqli_query($conexion, $sqlRepetido);
            $repetido = mysqli_fetch_array($resRepetido);
            if($repetido['existe'] > 0)
            {
                echo "repetido";
            }
            else
            {
                $sqlMax = "SELECT (COALESCE(MAX(id_documento), 0) + 1) as id_documento FROM emisores_expediente WHERE id_emisor = ".$_SESSION['id_emisor'];
                $resMax = mysqli_query($conexion, $sqlMax);
                $maximo = mysqli_fetch_array($resMax);

                $insert = "INSERT INTO emisores_expediente VALUES(".$maximo['id_documento'].",".$_SESSION['id_emisor'].",".$_POST['tipo_catalogo'].", '".strtoupper($_POST['nombre_documento'])."',".$_POST['genera_vigencia'].", 1)";
                $registro = mysqli_query($conexion, $insert);

                if($registro)
                {
                    echo "correcto";
                }
                else
                {
                    echo "incorrecto";
                }
            }
        }
        else
        {
            $update = "UPDATE emisores_expediente SET tipo_catalogo = ".$_POST['tipo_catalogo'].", nombre_documento = '".strtoupper($_POST['nombre_documento'])."', genera_vigencia = ".$_POST['genera_vigencia']." WHERE id_documento = ".$_POST['tipo_gestion']." AND id_emisor = ".$_SESSION['id_emisor'];
            $resultado = mysqli_query($conexion, $update);
            
            if($resultado)
            {
                echo "correcto";
            }
            else
            {
                echo "incorrecto";
            }
        }
    }
?>
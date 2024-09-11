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
            $sqlRepetido = "SELECT COUNT(*) as existe FROM emisores_series WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_documento = ".$_POST['documento']." AND serie = '".$_POST['serie']."'";
            $resRepetido = mysqli_query($conexion, $sqlRepetido);
            $repetido = mysqli_fetch_array($resRepetido);
            if($repetido['existe'] > 0)
            {
                echo "repetido";
            }
            else
            {
                $sqlMax = "SELECT (COALESCE(MAX(id_partida), 0) + 1) as id_partida FROM emisores_series WHERE id_emisor = ".$_SESSION['id_emisor'];
                $resMax = mysqli_query($conexion, $sqlMax);
                $maximo = mysqli_fetch_array($resMax);

                $insert = "INSERT INTO emisores_series VALUES(".$maximo['id_partida'].",".$_SESSION['id_emisor'].", ".$_POST['documento'].", '".strtoupper($_POST['serie'])."', ".$_POST['folio'].", ".$_POST['cp'].", '".$_POST['leyenda']."', 1)";
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
            $update = "UPDATE emisores_series SET id_documento = ".$_POST['documento'].", serie = '".strtoupper($_POST['serie'])."', folio = ".$_POST['folio'].", codigo_postal = ".$_POST['cp'].", leyenda = '".strtoupper($_POST['leyenda'])."' WHERE id_partida = ".$_POST['tipo_gestion']." AND id_emisor = ".$_SESSION['id_emisor'];
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
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
        $sqlRepetido = "SELECT COUNT(*) as existe FROM emisores_modulos WHERE id_emisor = ".$_POST['id_emisor']." AND id_modulo = ".$_POST['id_modulo'];
        $resRepetido = mysqli_query($conexion, $sqlRepetido);
        $repetido = mysqli_fetch_array($resRepetido);
        if($repetido['existe'] > 0)
        {
            echo "repetido";
        }
        else
        {
            $sqlMax = "SELECT (COALESCE(MAX(id_partida), 0) + 1) as id_partida FROM emisores_modulos WHERE id_emisor = ".$_POST['id_emisor'];
            $resMax = mysqli_query($conexion, $sqlMax);
            $maximo = mysqli_fetch_array($resMax);

            $insert = "INSERT INTO emisores_modulos VALUES(".$maximo['id_partida'].",".$_POST['id_emisor'].", ".$_POST['id_modulo'].", 1)";
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
?>
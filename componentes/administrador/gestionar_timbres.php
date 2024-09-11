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
        $fecha = date("Y-m-d");
        if($_POST['abono'] == 1)
        {
            $operacion = 1;
            $sqlTimbres = "UPDATE emisores SET timbres = timbres + ".$_POST['timbres']." WHERE id_emisor = 1";
        }
        else
        {
            $operacion = 2;
            $sqlTimbres = "UPDATE emisores SET timbres = timbres - ".$_POST['timbres']." WHERE id_emisor = 1";
        }

        $sqlPartida = "SELECT COALESCE(MAX(id_partida),0) AS contar FROM timbres WHERE id_emisor=".$_POST['id_emisor'];
        $resPartida = mysqli_query($conexion, $sqlPartida);
        $partida = mysqli_fetch_array($resPartida);
        $max = $partida['contar'] + 1;

        $insertEmisor = "INSERT INTO timbres VALUES(".$max.",".$_POST['id_emisor'].",".$_POST['timbres'].",'".$fecha."',".$operacion.")";
        mysqli_query($conexion, $insertEmisor);

        mysqli_query($conexion, $sqlTimbres);
    }
?>
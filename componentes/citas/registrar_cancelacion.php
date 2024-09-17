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
        $fecha_cancelado = date('Y-m-d');
        $sql_cancelacion = "INSERT INTO emisores_cancelaciones VALUES(".$_SESSION['id_emisor'].", 0, ".$_POST['id_folio'].", '".$fecha_cancelado."', 0, 0, 0, 0, '".strtoupper($_POST['motivo'])."', 1)";
        $cancelado = mysqli_query($conexion, $sql_cancelacion);
        if($cancelado)
        {
            $sql_estatus = "UPDATE emisores_agenda SET estatus = 4 WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_folio = ".$_POST['id_folio'];
            $estatus = mysqli_query($conexion, $sql_estatus);
            if($estatus)
            {
                echo "ok";
            }
            else
            {
                echo "error2";
            }
        }
        else
        {
            echo "error";
        }
    }
?>
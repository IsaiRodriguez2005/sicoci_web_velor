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
        $sqlEmisor = "UPDATE emisores_personal SET estatus=".$_POST['estatus']." WHERE id_personal = ".$_SESSION['id_emisor']." AND id_personal=".$_POST['id_personal'];
        mysqli_query($conexion, $sqlEmisor);
    }
?>
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
        $sqlEmisor = "UPDATE emisores_personal_permisos SET estatus=".$_POST['estatus']." WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_permiso=".$_POST['id_permiso'];
        echo $sqlEmisor;
        $resultado = mysqli_query($conexion, $sqlEmisor);

        echo $resultado;
    }
?>
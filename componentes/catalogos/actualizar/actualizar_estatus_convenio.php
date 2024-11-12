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
        $sqlConvenio = "UPDATE emisores_convenios SET estatus=".$_POST['codigo_estatus']." WHERE id_convenio=".$_POST['id_convenio']." AND id_emisor=".$_SESSION['id_emisor'];
        $resultado = mysqli_query($conexion, $sqlConvenio);
        if($resultado){
            echo 'ok';
        } else {
            echo 'error';
        }
    }
?>
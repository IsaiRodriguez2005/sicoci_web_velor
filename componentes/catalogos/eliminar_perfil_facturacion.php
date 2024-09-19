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
        $consultar = "DELETE FROM emisores_clientes_facturacion WHERE id_perfil = ".$_POST['id_perfil']." AND id_emisor = ".$_SESSION['id_emisor'];
        $resultado = mysqli_query($conexion, $consultar);
        if($resultado){
            echo 'ok';
        }else{
            echo 'error';
        }
    }
?>
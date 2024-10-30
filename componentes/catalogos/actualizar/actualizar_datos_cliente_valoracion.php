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
        
        $sqlCliente = "UPDATE emisores_clientes SET est_civ = ".intval($_POST['estado_civil'])." ,
                                                    fec_nac = '".$_POST['fecha_nacimiento']."',
                                                    ocupacion = ".intval($_POST['ocupacion'])."
                                                    WHERE id_cliente=".$_POST['id_cliente']." AND id_emisor=".$_SESSION['id_emisor'];
        $resultado = mysqli_query($conexion, $sqlCliente);
        if($resultado){
            echo 'ok';
        }
    }
?>
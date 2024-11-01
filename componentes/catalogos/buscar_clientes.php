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
        $html = '';
        if(true)
        {
            $sql = "SELECT c.id_cliente, c.nombre_cliente, (SELECT COUNT(*) FROM emisores_agenda AS a WHERE a.id_cliente = c.id_cliente) AS total_registros
                        FROM emisores_clientes AS c
                        WHERE c.nombre_cliente = '".$_POST['nombre_social']."' AND c.id_emisor = ".$_SESSION['id_emisor'].";";
        }
        //echo $sql;
        $res = mysqli_query($conexion, $sql);
        $cliente = mysqli_fetch_array($res);

        echo json_encode($cliente);
    }
?>

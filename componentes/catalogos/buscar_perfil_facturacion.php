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
            $sql = "SELECT id_cliente, nombre_cliente FROM emisores_clientes WHERE id_cliente = ".$_POST['id_cliente'].";";
        }
        $res = mysqli_query($conexion, $sql);
        $cliente = mysqli_fetch_array($res);

        echo $cliente['nombre_cliente'];
    }
?>


<?php

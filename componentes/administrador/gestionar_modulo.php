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
        if($_POST['id_modulo'] == 0)
        {
            $insert = "INSERT INTO _cat_erp_modulos VALUES(NULL,'".strtoupper($_POST['modulo'])."', 1)";
            mysqli_query($conexion, $insert);
        }
        else
        {
            $update = "UPDATE _cat_erp_modulos SET nombre_modulo = '".strtoupper($_POST['modulo'])."' WHERE id_modulo = ".$_POST['id_modulo'];
            mysqli_query($conexion, $update);
        }
    }
?>
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
        if($_POST['id_documento'] == 0)
        {
            $insert = "INSERT INTO _cat_erp_documentos VALUES(NULL,'".strtoupper($_POST['documento'])."','".$_POST['modulo']."', 1)";
            mysqli_query($conexion, $insert);
        }
        else
        {
            $update = "UPDATE _cat_erp_documentos SET nombre_documento = '".strtoupper($_POST['documento'])."', id_modulo = '".$_POST['modulo']."' WHERE id_documento = ".$_POST['id_documento'];
            mysqli_query($conexion, $update);
        }
    }
?>
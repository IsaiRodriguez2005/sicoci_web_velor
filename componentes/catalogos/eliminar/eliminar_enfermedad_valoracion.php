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
        $sql = "DELETE FROM emisores_historial_expediente_enfermedades WHERE id_folio=".$_POST['id_folio']." AND id_enfermedad=".$_POST['id_enfermedad']." AND id_emisor=".$_SESSION['id_emisor'];
        //echo $sql;
        $resultado = mysqli_query($conexion, $sql);

        if($resultado){
            echo 'ok';
        } else {
            echo 'e';
        }
    }
?>
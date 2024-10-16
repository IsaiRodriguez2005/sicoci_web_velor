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
        $sqlEmisor = "UPDATE emisores SET 
            rfc='".strtoupper($_POST['rfc'])."', 
            nombre_social='".strtoupper($_POST['nombre_social'])."',
            nombre_comercial='".strtoupper($_POST['nombre_comercial'])."',
            calle='".strtoupper($_POST['calle'])."',
            exterior='".strtoupper($_POST['no_exterior'])."',
            interior='".strtoupper($_POST['no_interior'])."',
            codigo_postal='".$_POST['codigo_postal']."',
            clave_colonia='".strtoupper($_POST['colonia'])."',
            clave_estado='".strtoupper($_POST['estado'])."',
            clave_municipio='".strtoupper($_POST['municipio'])."',
            clave_pais='".strtoupper($_POST['pais'])."',
            clave_regimen='".strtoupper($_POST['regimen'])."',
            sitio_web='".$_POST['sitio_web']."',
            correo='".$_POST['correo']."',
            telefono='".$_POST['telefono']."'
            WHERE
            id_emisor=".$_POST['id_emisor'];
        mysqli_query($conexion, $sqlEmisor);
        echo $sqlEmisor;
    }
?>
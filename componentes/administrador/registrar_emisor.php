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
        $fecha_alta = date("Y-m-d");
        $insertEmisor = "INSERT INTO emisores VALUES(NULL,'".strtoupper($_POST['rfc'])."','".strtoupper($_POST['nombre_social'])."','".strtoupper($_POST['nombre_comercial'])."','".strtoupper($_POST['calle'])."','".strtoupper($_POST['no_exterior'])."','".strtoupper($_POST['no_interior'])."','".$_POST['codigo_postal']."','".strtoupper($_POST['colonia'])."','".strtoupper($_POST['estado'])."','".strtoupper($_POST['municipio'])."','".strtoupper($_POST['pais'])."','".strtoupper($_POST['regimen'])."', 0, '".$_POST['sitio_web']."','".$_POST['correo']."','".$_POST['telefono']."','".$fecha_alta."', 1)";
        mysqli_query($conexion, $insertEmisor);

        $selectMAX = "SELECT COALESCE(MAX(id_emisor),0) AS no_usuario FROM usuarios";
        $resMAX = mysqli_query($conexion, $selectMAX);
        $max = mysqli_fetch_array($resMAX);
        $ultimo = $max['no_usuario'] + 1;

        $insertConfiguracion = "INSERT INTO emisores_configuraciones VALUES(".$ultimo.", 0, 0, 0, 0, 0, 0, 0, '', '')";
        mysqli_query($conexion, $insertConfiguracion);

        $carpeta_raiz = "../../emisores/".$ultimo;
        if (!file_exists($carpeta_raiz)) {
            mkdir($carpeta_raiz, 0777, true);
            if (file_exists($carpeta_raiz)) {
                mkdir($carpeta_raiz."/facturas/prexml", 0777, true);
                mkdir($carpeta_raiz."/facturas/timbradas", 0777, true);
                mkdir($carpeta_raiz."/rep/prexml", 0777, true);
                mkdir($carpeta_raiz."/rep/timbradas", 0777, true);
                mkdir($carpeta_raiz."/notas/prexml", 0777, true);
                mkdir($carpeta_raiz."/notas/timbradas", 0777, true);
                mkdir($carpeta_raiz."/archivos/portafolio", 0777, true);
                mkdir($carpeta_raiz."/archivos/portafolio/clientes", 0777, true);
                mkdir($carpeta_raiz."/archivos/portafolio/proveedores", 0777, true);
                mkdir($carpeta_raiz."/archivos/generales", 0777, true);
            }
        }
    }
?>
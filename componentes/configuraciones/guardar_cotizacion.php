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
        $hoja = $_FILES['hoja']['tmp_name'];
        $bandera_hoja = 1;
        if(!empty($hoja))
        {
            $hoja_subido = move_uploaded_file($hoja, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_cotizacion.png");
            if($hoja_subido)
            {
                $bandera_hoja = 1;
            }
            else
            {
                $bandera_hoja = 2;
            }
        }

        $firma = $_FILES['firma']['tmp_name'];
        $bandera_firma = 1;
        if(!empty($firma))
        {
            $firma_subido = move_uploaded_file($firma, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/firma_cotizacion.png");
            if($firma_subido)
            {
                $bandera_firma = 1;
            }
            else
            {
                $bandera_firma = 2;
            }
        }

        if($_POST['mostrar_cb'] == "true")
        {
            $bandera_cuentas = 1;
        }
        else
        {
            $bandera_cuentas = 2;
        }

        if($_POST['id_cotizacion'] == 0)
        {
            $insert = "INSERT INTO emisores_cotizaciones VALUES(NULL, ".$_SESSION['id_emisor'].", '".$_POST['terminos']."', '".$_POST['observaciones']."', '".strtoupper($_POST['calidad'])."', '".strtoupper($_POST['nombre_autoriza'])."', ".$bandera_hoja.", ".$bandera_firma.", ".$bandera_cuentas.")";
            $res = mysqli_query($conexion, $insert);
            if($res)
            {
                $bandera_sql = 1;
            }
            else
            {
                $bandera_sql = 2;
            }
        }
        else
        {
            $update = "UPDATE emisores_cotizaciones SET terminos = '".$_POST['terminos']."', observaciones = '".$_POST['observaciones']."', codigo_calidad = '".strtoupper($_POST['calidad'])."', nombre_autoriza = '".strtoupper($_POST['nombre_autoriza'])."', hoja_membretada = ".$bandera_hoja.", firma_autoriza = ".$bandera_firma.", mostrar_cuentas = ".$bandera_cuentas." WHERE id_cotizacion = ".$_POST['id_cotizacion'];
            $res = mysqli_query($conexion, $update);
            if($res)
            {
                $bandera_sql = 1;
            }
            else
            {
                $bandera_sql = 2;
            }
        }

        echo $bandera_hoja.$bandera_firma.$bandera_sql;
    }
?>
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
            $hoja_subido = move_uploaded_file($hoja, "../../emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_minuta.png");
            if($hoja_subido)
            {
                $bandera_hoja = 1;
            }
            else
            {
                $bandera_hoja = 2;
            }
        }

        if($_POST['id_cotizacion'] == 0)
        {
            $insert = "INSERT INTO emisores_minutas VALUES(NULL, ".$_SESSION['id_emisor'].", '".strtoupper($_POST['calidad_minuta'])."', ".$bandera_hoja.")";
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
            $update = "UPDATE emisores_minutas SET codigo_calidad = '".strtoupper($_POST['calidad_minuta'])."', hoja_membretada = ".$bandera_hoja." WHERE id_minuta = ".$_POST['id_minuta'];
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

        echo $bandera_hoja.$bandera_sql;
    }
?>
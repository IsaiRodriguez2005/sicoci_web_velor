<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

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
        $sql = "SELECT COUNT(a.upload) AS no_registro FROM emisores_expediente_archivos a WHERE a.id_emisor=".$_SESSION['id_emisor']." AND id_cliente=".$_POST['id_proveedor']." AND tipo_catalogo = 2 AND a.upload != 0";
        $res = mysqli_query($conexion, $sql);
        $conteo = mysqli_fetch_array($res);
        
        if($conteo['no_registro'] > 0)
        {
            $sql2 = "SELECT a.id_archivo, e.nombre_documento FROM emisores_expediente_archivos a INNER JOIN emisores_expediente e ON a.id_documento = e.id_documento AND a.id_emisor = e.id_emisor AND e.tipo_catalogo = 2 WHERE a.id_cliente=".$_POST['id_proveedor']." AND a.id_emisor=".$_SESSION['id_emisor']." AND a.upload = 1";
            $res2 = mysqli_query($conexion, $sql2);
            
            // Creamos un instancia de la clase ZipArchive
            $zip = new ZipArchive();
            $ruta_archivos = '../../emisores/'.$_SESSION['id_emisor'].'/archivos/portafolio/proveedores/'.$_POST['id_proveedor'].'/';
            // Creamos y abrimos un archivo zip temporal
            $zip->open($ruta_archivos."Expediente(".$_POST['nombre_cliente'].").zip", ZipArchive::CREATE);
            // Añadimos un directorio
            $dir_zip = "Expediente(".$_POST['nombre_cliente'].")/";
            
            //$zip->addEmptyDir();
            //Añadimos un archivo dentro del directorio que hemos creado
            while ($archivos = mysqli_fetch_array($res2)) 
            //while ($archivos = mysqli_fetch_assoc($res2)) 
            {
                $nombre_archivo = $ruta_archivos.$dir_zip.$archivos['nombre_documento']."pdf";
                $zip->addFile($nombre_archivo, $archivos['nombre_documento']."pdf");
                /*
                if (file_exists($nombre_archivo) == true) 
                {
                    $zip->addFile($nombre_archivo, $ruta_archivos.$dir_zip.$archivos['nombre_documento']."pdf");
                }
                */
            }

            // Una vez añadido los archivos deseados cerramos el zip.
            $zip->close();
            // Creamos las cabezeras que forzaran la descarga del archivo como archivo zip.
            //header("Content-type: application/octet-stream");
            //header("Content-disposition: attachment; filename=Expediente(".$_POST['nombre_cliente'].").zip");
            // leemos el archivo creado
            //readfile("Expediente(".$_POST['nombre_cliente'].").zip");
            // Por ���ltimo eliminamos el archivo temporal creado
            //unlink("Expediente(".$_POST['nombre_cliente'].").zip");
            echo '../../emisores/'.$_SESSION['id_emisor'].'/archivos/portafolio/proveedores/'.$_POST['id_proveedor'].'/Expediente('.$_POST['nombre_cliente'].').zip';
        }
        else
        {
            echo $conteo['no_registro'];
        }
    }
?>
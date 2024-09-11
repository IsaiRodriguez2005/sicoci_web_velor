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
		$vigencia = $_POST['fecha'];

        $nombreDoc = $_FILES['archivo']['name'];
	    $rutaDoc = $_FILES['archivo']['tmp_name'];
	    $tipoDoc = $_FILES['archivo']['type'];

        if(empty($nombreDoc))
        {
            if($_POST['id_archivo'] == 0)
            {
                $consultar = "SELECT COALESCE(MAX(id_archivo)) AS no_registro FROM emisores_expediente_archivos WHERE id_cliente=".$_POST['id_proveedor']." AND id_emisor=".$_SESSION['id_emisor']." AND tipo_catalogo=2";
                $resultado = mysqli_query($conexion, $consultar);
                $existe = mysqli_fetch_array($resultado);

                $max = $existe['no_registro'] + 1;
                $consultaInsert = "INSERT INTO emisores_expediente_archivos VALUES(".$max.", ".$_POST['id_documento'].", ".$_POST['id_proveedor'].", ".$_SESSION['id_emisor'].", 2, 0, '".$_POST['fecha']."')";
                mysqli_query($conexion, $consultaInsert);

                echo "1";
            }
            else
            {
                $consultaUpdate = "UPDATE emisores_expediente_archivos SET vigencia = '".$_POST['fecha']."' WHERE id_archivo=".$_POST['id_archivo']." AND id_cliente=".$_POST['id_proveedor']." AND id_emisor=".$_SESSION['id_emisor']." AND tipo_catalogo = 2";
                mysqli_query($conexion, $consultaUpdate);

                echo "1";
            }
        }
        else
        {
            $doc_subido = move_uploaded_file($rutaDoc, "../../emisores/".$_SESSION['id_emisor']."/archivos/portafolio/proveedores/".$_POST['id_proveedor']."/".$_POST['nombre_documento'].".pdf");
            if($doc_subido)
            {
                if($_POST['id_archivo'] == 0)
                {
                    $consultar = "SELECT COALESCE(MAX(id_archivo)) AS no_registro FROM emisores_expediente_archivos WHERE id_cliente=".$_POST['id_proveedor']." AND id_emisor=".$_SESSION['id_emisor']." AND tipo_catalogo = 2";
                    $resultado = mysqli_query($conexion, $consultar);
                    $existe = mysqli_fetch_array($resultado);

                    $max = $existe['no_registro'] + 1;
                    $consultaInsert = "INSERT INTO emisores_expediente_archivos VALUES(".$max.", ".$_POST['id_documento'].", ".$_POST['id_proveedor'].", ".$_SESSION['id_emisor'].", 2, 1, '".$_POST['fecha']."')";
                    mysqli_query($conexion, $consultaInsert);

                    echo "1";
                }
                else
                {
                    $consultaUpdate = "UPDATE emisores_expediente_archivos SET upload = 1, vigencia = '".$_POST['fecha']."' WHERE id_archivo=".$_POST['id_archivo']." AND id_cliente=".$_POST['id_proveedor']." AND id_emisor=".$_SESSION['id_emisor']." AND tipo_catalogo = 2";
                    mysqli_query($conexion, $consultaUpdate);

                    echo "1";
                }
            }
            else
            {
                echo "2";
            }
        }
    }
?>
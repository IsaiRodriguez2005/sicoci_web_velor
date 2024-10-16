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
        $consultar = "UPDATE emisores_expediente_archivos SET upload = 0 WHERE id_archivo=".$_POST['id_archivo']." AND id_cliente=".$_POST['id_proveedor']." AND id_emisor=".$_SESSION['id_emisor']." AND tipo_catalogo = 2";
        $resultado = mysqli_query($conexion, $consultar);

        if($resultado)
        {
            $archivo = "../../emisores/".$_SESSION['id_emisor']."/archivos/portafolio/proveedores/".$_POST['id_proveedor']."/".$_POST['nombre_documento'].".pdf";
            if(file_exists($archivo))
            {
                unlink($archivo);
            }
            echo "1";
        }
        else
        {
            echo "2";
        }
    }
?>
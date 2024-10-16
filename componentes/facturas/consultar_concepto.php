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
        $datos = array();

        $sql = "SELECT clave_concepto, clave_unidad, precio_unitario, porcentaje_iva, porcentaje_retencion, exento_iva FROM emisores_facturas_conceptos WHERE id_concepto = ".$_POST['id_concepto']." AND id_emisor = ".$_SESSION['id_emisor'];
        $res = mysqli_query($conexion, $sql);
        $dato = mysqli_fetch_array($res);

        $datos['campo'] = $dato;

        echo json_encode($datos);
    }
?>
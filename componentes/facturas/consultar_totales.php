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

        $sql = "SELECT
            COALESCE(SUM(fd.monto_iva),0) as acuIva,
            COALESCE(SUM(fd.monto_retencion),0) as acuRet,
            COALESCE(SUM(fd.importe),0) as acuImp
            FROM emisores_facturas_detalles fd
            WHERE fd.id_emisor = ".$_SESSION['id_emisor']." AND fd.id_documento = ".$_POST['id_documento']." AND fd.folio_factura = ".$_POST['folio_factura'];
        $res = mysqli_query($conexion, $sql);
        $dato = mysqli_fetch_array($res);

        $datos['campo'] = $dato;

        echo json_encode($datos);
    }
?>
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
            c.rfc,
            c.calle,
            c.no_exterior,
            c.no_interior,
            c.codigo_postal,
            IF(LENGTH(c.colonia) > 4, c.colonia, sc.nombre_colonia) as colonia,
            IF(LENGTH(c.municipio) > 3, c.municipio, sm.nombre_municipio) as municipio,
            c.estado,
            c.pais,
            c.regimen_fiscal,
            c.uso_cfdi,
            c.metodo_pago,
            c.forma_pago,
            c.dias_credito,
            sr.descripcion as nombre_regimen
            FROM emisores_clientes c
            LEFT JOIN _cat_sat_colonias sc ON sc.clave_colonia = c.colonia AND sc.codigo_postal = c.codigo_postal
            LEFT JOIN _cat_sat_municipios sm ON sm.clave_municipio = c.municipio AND sm.clave_estado = c.estado
            LEFT JOIN _cat_sat_regimen_fiscal sr ON sr.clave_regimen = c.regimen_fiscal
            WHERE c.id_emisor = ".$_SESSION['id_emisor']." AND c.id_cliente = ".$_POST['id_cliente']."
        ";
        $res = mysqli_query($conexion, $sql);
        $dato = mysqli_fetch_array($res);

        $datos['campo'] = $dato;

        echo json_encode($datos);
    }
?>
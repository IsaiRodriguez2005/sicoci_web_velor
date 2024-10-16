<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    function truncar($numero, $decimales)
    {
        $truncar = 10**$decimales;
        return intval($numero * $truncar) / $truncar;
    }

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
        if($_POST['iva_exento'] == 2)
        {
            $porcentaje_iva = 0.00;
            $monto_iva = 0.00;
            $porcentaje_retencion = 0.00;
            $monto_retencion = 0.00;
        }
        else
        {
            $porcentaje_iva = truncar($_POST['iva'], 2);
            $monto_iva = (truncar($_POST['iva'], 2) / 100) * (truncar($_POST['cantidad'], 2) * truncar($_POST['precio'], 2));
            $porcentaje_retencion = truncar($_POST['retencion'], 2);
            $monto_retencion = (truncar($_POST['retencion'], 2) / 100) * (truncar($_POST['cantidad'], 2) * truncar($_POST['precio'], 2));
        }

        $importe = truncar($_POST['cantidad'], 2) * truncar($_POST['precio'], 2);
        $importe = truncar($importe, 2);

        if($_POST['bandera_partida'] == 0)
        {
            $sql_partida = "SELECT COALESCE(MAX(no_partida), 0) as partida FROM emisores_facturas_detalles WHERE id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura']." AND id_emisor = ".$_SESSION['id_emisor'];
            $res_partida = mysqli_query($conexion, $sql_partida);
            $partida = mysqli_fetch_array($res_partida);
            $new_partida = $partida['partida'] + 1;

            $sql_agregar = "INSERT INTO `emisores_facturas_detalles` VALUES (".$new_partida.", ".$_POST['id_documento'].", ".$_POST['folio_factura'].", ".$_SESSION['id_emisor'].", ".$_POST['id_producto'].", ".$_POST['cantidad'].", '".$_POST['clave_concepto']."', '".$_POST['clave_medida']."', '".strtoupper($_POST['descripcion'])."', ".$_POST['precio'].", ".$porcentaje_iva.", ".$monto_iva.", ".$porcentaje_retencion.", ".$monto_retencion.", ".$importe.", ".$_POST['iva_exento'].")";
            $res_agregar = mysqli_query($conexion, $sql_agregar);
        }
        else
        {
            $sql_update = "UPDATE emisores_facturas_detalles SET id_producto = ".$_POST['id_producto'].", cantidad = ".$_POST['cantidad'].", clave_sat_servicio = '".$_POST['clave_concepto']."', clave_sat_unidad = '".$_POST['clave_medida']."', descripcion = '".strtoupper($_POST['descripcion'])."', precio_unitario = ".$_POST['precio'].", porcentaje_iva = ".$porcentaje_iva.", monto_iva = ".$monto_iva.", porcentaje_retencion = ".$porcentaje_retencion.", monto_retencion = ".$monto_retencion.", importe = ".$importe.", exento_iva = ".$_POST['iva_exento']." WHERE no_partida = ".$_POST['bandera_partida']." AND id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura']." AND id_emisor = ".$_SESSION['id_emisor'];
            $res_agregar = mysqli_query($conexion, $sql_update);
        }
        
        
        if($res_agregar)
        {
            $html = '
                <table class="table table-striped" id="tabla_productos2">
                    <thead>
                        <tr>
                            <th class="sticky text-center text-sm">Acciones</th>
                            <th class="sticky text-center text-sm">Partida</th>
                            <th class="sticky text-center text-sm">Cantidad</th>
                            <th class="sticky text-center text-sm">Descripci&oacute;n</th>
                            <th class="sticky text-center text-sm">P/U</th>
                            <th class="sticky text-center text-sm">IVA</th>
                            <th class="sticky text-center text-sm">Retenci&oacute;n IVA</th>
                            <th class="sticky text-center text-sm">Importe</th>
                        </tr>
                    </thead>
                    <tbody id="tabla_rows">
            ';
            $sql_mostrar = "SELECT no_partida, id_producto, cantidad, clave_sat_servicio, clave_sat_unidad, descripcion, precio_unitario, porcentaje_iva, monto_iva, porcentaje_retencion, monto_retencion, importe, exento_iva FROM emisores_facturas_detalles WHERE id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura']." AND id_emisor = ".$_SESSION['id_emisor'];
            $res_mostrar = mysqli_query($conexion, $sql_mostrar);
            while($mostrar = mysqli_fetch_array($res_mostrar))
            {
                $html .= '
                        <tr>
                            <th class="sticky text-center text-sm">
                                <button type="button" class="btn btn-danger btn-sm" title="Eliminar concepto" onclick="eliminar_producto('.$mostrar['no_partida'].','.$_POST['id_documento'].','.$_POST['folio_factura'].')">
                                    <i class="fas fa-trash"></i>
                                </button> &nbsp;
                                <button type="button" class="btn btn-warning btn-sm" title="Editar concepto" onclick="editar_producto('.$mostrar['no_partida'].','.$mostrar['id_producto'].','.$mostrar['cantidad'].',&quot;'.$mostrar['clave_sat_servicio'].'&quot;,&quot;'.$mostrar['clave_sat_unidad'].'&quot;,&quot;'.$mostrar['descripcion'].'&quot;,'.$mostrar['precio_unitario'].','.$mostrar['porcentaje_iva'].','.$mostrar['porcentaje_retencion'].','.$mostrar['exento_iva'].')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </th>
                            <th class="sticky text-center text-sm">'.$mostrar['no_partida'].'</th>
                            <th class="sticky text-center text-sm">'.$mostrar['cantidad'].'</th>
                            <th class="sticky text-center text-sm">'.$mostrar['descripcion'].'</th>
                            <th class="sticky text-center text-sm">'.$mostrar['precio_unitario'].'</th>
                            <th class="sticky text-center text-sm">'.$mostrar['monto_iva'].'</th>
                            <th class="sticky text-center text-sm">'.$mostrar['monto_retencion'].'</th>
                            <th class="sticky text-center text-sm">'.$mostrar['importe'].'</th>
                        </tr>
                ';   
            }
            $html .= '
                    </tbody>
                </table>
            ';

            echo $html;
        }
        else
        {
            echo "error";
        }
    }
?>
<script>
  $(function () {
    $('#tabla_productos2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
  });
</script>
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
        $sql_borrar = "DELETE FROM emisores_facturas_detalles WHERE no_partida = ".$_POST['partida']." AND id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura']. " AND id_emisor = ".$_SESSION['id_emisor'];
        $res_borrar = mysqli_query($conexion, $sql_borrar);
        
        if($res_borrar)
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
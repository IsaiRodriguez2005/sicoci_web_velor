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
        $tabla = '
            <table class="table table-striped" id="tabla_facturas_recurrentes">
                <thead>
                    <tr>
                        <th class="sticky text-center">Acciones</th>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nombre de la factura</th>
                        <th class="text-center">Genera ISR</th>
                        <th class="text-center">D&iacute;a facturaci&oacute;n</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Mes</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody>
        ';
        $sql = "SELECT id_facturacion, nombre_facturacion, dia_facturacion, porcentaje_isr, tipo, mes, estatus FROM emisores_clientes_fac_rec WHERE id_emisor=".$_SESSION['id_emisor']." AND id_cliente=".$_POST['id_cliente'];
        $res = mysqli_query($conexion, $sql);
        while($datos = mysqli_fetch_array($res))
        {
            if($datos['estatus'] == 1)
            {
                $estado = "Activo";
                $titulo = "Desactivar facturaci&oacute;n recurrente";
                $color = "btn-secondary";
                $desactive = "<i class='fas fa-times-circle'></i>";
                $codigo_estatus = 2;
            }else{
                $estado = "Inactivo";
                $titulo = "Activar facturaci&oacute;n recurrente";
                $color = "btn-success";
                $desactive = "<i class='fas fa-check-circle'></i>";
                $codigo_estatus = 1;
            }
            if($datos['tipo'] == 1)
            {
                $tipo = "MENSUAL";
            }
            else
            {
                $tipo = "ANUAL";
            }
            switch($datos['mes'])
            {
                case 0 : $mes ="NO APLICA"; break;
                case 1 : $mes ="ENERO"; break;
                case 2 : $mes ="FEBRERO"; break;
                case 3 : $mes ="MARZO"; break;
                case 4 : $mes ="ABRIL"; break;
                case 5 : $mes ="MAYO"; break;
                case 6 : $mes ="JUNIO"; break;
                case 7 : $mes ="JULIO"; break;
                case 8 : $mes ="AGOSTO"; break;
                case 9 : $mes ="SEPTIEMBRE"; break;
                case 10 : $mes ="OCTUBRE"; break;
                case 11 : $mes ="NOVIEMBRE"; break;
                case 12 : $mes ="DICIEMBRE"; break;
            }
            $tabla.="
                    <tr>
                        <td class='text-center'>
                            <div class='btn-group'>
                                <button type='button' class='btn btn-primary btn-sm' title='Agregar conceptos a la factura' onclick='agregar_conceptos_factura(".$datos['id_facturacion'].",&quot;".$datos['nombre_facturacion']."&quot;,".$_POST['id_cliente'].")'>
                                    <i class='fas fa-plus-circle'></i>
                                </button> &nbsp;
                                <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_factura_recurrente(".$datos['id_facturacion'].",".$_POST['id_cliente'].",".$codigo_estatus.");'>
                                    ".$desactive."
                                </button> &nbsp;
                                <button type='button' class='btn btn-danger btn-sm' title='Eliminar factura recurrente' onclick='eliminar_factura_recurrente(".$datos['id_facturacion'].",".$_POST['id_cliente'].")'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>".$datos['id_facturacion']."</td>
                        <td class='text-center'>".$datos['nombre_facturacion']."</td>
                        <td class='text-center'>% ".$datos['porcentaje_isr']."</td>
                        <td class='text-center'>".$datos['dia_facturacion']."</td>
                        <td class='text-center'>".$tipo."</td>
                        <td class='text-center'>".$mes."</td>
                        <td class='text-center'>".$estado."</td>
                    </tr>
            ";
        }

        $sql_clave = "SELECT id_concepto, alias, clave_concepto FROM emisores_facturas_conceptos WHERE id_emisor = ".$_SESSION['id_emisor']." AND estatus = 1 ORDER BY alias ASC";
        $res_clave = mysqli_query($conexion, $sql_clave);
        while($clave = mysqli_fetch_array($res_clave))
        {
            $options .= "<option value='".$clave['id_concepto']."'>[".$clave['clave_concepto']."] ".$clave['alias']."</option>";
        }
        $tabla.='
                </tbody>
            </table>
            <div class="modal fade" id="modal-facturacion-conceptos">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Conceptos de la factura recurrente</h4>
                        </div>
                        <div class="modal-body">
                            <h6><i class="fas fa-file-code"></i> Datos generales de la factura</h6><hr>
                            <div class="row">
                                <div class="col-1">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="m4_id_factura" disabled>
                                        <input type="hidden" class="form-control" id="m4_id_cliente">
                                    </div>
                                </div>
                                <div class="col-9">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="m4_nombre_factura" disabled>
                                    </div>
                                </div>
                            </div>
                            <br><h6><i class="fas fa-bell"></i> Relaciona conceptos a tu factura recurrente</h6><hr>
                            <div class="row">
                                <div class="col-2">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                        </div>
                                        <input type="number" class="form-control" placeholder="Cantidad" id="m4_cantidad" onfocus="resetear(&quot;m4_cantidad&quot;)" min="1">
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-bell"></i></span>
                                        </div>
                                        <select class="form-control" id="m4_clave_sat" onfocus="resetear(&quot;m4_clave_sat&quot;)" onchange="consulta_clave()">
                                            <option value="0">Selecciona Clave SAT...</option>
                                            '.$options.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        </div>
                                        <input type="number" class="form-control" placeholder="Precio" id="m4_precio" onfocus="resetear(&quot;m4_precio&quot;)" min="1">
                                        <input type="hidden" id="m4_clave_sat_producto">
                                        <input type="hidden" id="m4_clave_sat_medida">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <input type="checkbox" id="m4_check_iva" onclick="valida_iva()" checked>
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" placeholder="% IVA" id="m4_iva" onfocus="resetear(&quot;m4_iva&quot;)">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                            
                                                <input type="checkbox" id="m4_check_ret" onclick="valida_retencion()">
                                                
                                            </span>
                                        </div>
                                        <input type="number" class="form-control" placeholder="% Ret IVA" id="m4_ret" onfocus="resetear(&quot;m4_ret&quot;)" disabled>
                                    </div>
                                </div>
                                <div class="6">
                                    <div class="input-group">
                                        <div class="icheck-success d-inline">
                                            <input type="checkbox" id="m4_iva_extento" onclick="validar_iva_exento()">
                                            <label for="m4_iva_extento">
                                                Exento de IVA
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-stream"></i></span>
                                        </div>
                                        <textarea class="form-control" placeholder="Descripci&oacute;n del concepto" id="m4_descripcion" onfocus="resetear(&quot;m4_descripcion&quot;)"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <center>
                                        <button type="button" class="btn btn-primary" onclick="guarda_conceptos_facturas()">Guardar Concepto</button>
                                    </center>
                                </div>
                            </div>
                            <br>
                            <h6><i class="fas fa-tag"></i> Conceptos agregados a la factura</h6><hr>
                            <div class="row">
                                <div class="col-12" id="mostrar_conceptos">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="cerrar_modal(&quot;modal-facturacion-conceptos&quot;)">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        ';

        echo $tabla;
    }
?>
<script>
  $(function () {
    $('#tabla_facturas_recurrentes').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
  });
</script>
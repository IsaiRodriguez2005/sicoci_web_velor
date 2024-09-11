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
        ////////////////////////// SE CARGA LOS DATOS DE LA FACTURA
        $sql_factura = "SELECT * FROM emisores_facturas WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura'];
        $res_factura = mysqli_query($conexion, $sql_factura);
        $factura = mysqli_fetch_array($res_factura);

        if($factura['rfc'] == "XAXX010101000")
        {
            $disabled_periodo = "";
            $disabled_mes = "";
            $disabled_anio = "";
        }
        else
        {
            $disabled_periodo = "disabled";
            $disabled_mes = "disabled";
            $disabled_anio = "disabled";
        }
        if($factura['moneda'] == "MXN")
        {
            $disabled_tc = "disabled";
            $tc = "1";
        }
        else
        {
            $disabled_tc = "";
            $tc = $factura['tipo_cambio'];
        }
        if($factura['porcentaje_retencion_isr'] != 0)
        {
            $check_isr = "checked";
            $disabled_isr = "";
        }
        else
        {
            $check_isr = "";
            $disabled_isr = "disabled";
        }
        //////////////////////////// TERMINO DE CARGAR LOS DATOS DE LA FACTURA

        /////////////////////////// CARGAMOS LOS DETALLES DE LA FACTURA
        $sql_mostrar = "SELECT no_partida, id_producto, cantidad, clave_sat_servicio, clave_sat_unidad, descripcion, precio_unitario, porcentaje_iva, monto_iva, porcentaje_retencion, monto_retencion, importe, exento_iva FROM emisores_facturas_detalles WHERE id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura']." AND id_emisor = ".$_SESSION['id_emisor'];
        $res_mostrar = mysqli_query($conexion, $sql_mostrar);
        while($mostrar = mysqli_fetch_array($res_mostrar))
        {
            $partidas_conceptos .= '
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
        /////////////////////////// TERMINAMOS DE CARGAR LOS DETALLES
        
        ////////////////////////// CARGAMOS CATALOGOS
        $display_domicilio = "display: none;";
        $domicilio = "";
        $rfc = "";
        $uso_cfdi = "";
        $metodo_pago = "";
        $forma_pago = "";
        $sqlCliente = "SELECT c.id_cliente, c.rfc, c.metodo_pago, c.forma_pago, c.uso_cfdi, c.nombre_social, IF(c.rfc = 'XEXX010101000', CONCAT(c.calle, ' ', c.no_exterior, ' ', c.no_interior, ' ', c.colonia, ' CP ', c.codigo_postal, ', ', c.municipio, ', ', c.estado, ', ', c.pais), CONCAT(c.calle, ' ', c.no_exterior, ' ', c.no_interior, ' COL. ', col.nombre_colonia, ' CP ', c.codigo_postal, ', ', m.nombre_municipio, ', ', c.estado, ', ', c.pais)) AS domicilio FROM emisores_clientes c LEFT JOIN _cat_sat_colonias col ON col.clave_colonia = c.colonia AND col.codigo_postal = c.codigo_postal LEFT JOIN _cat_sat_municipios m ON m.clave_municipio = c.municipio AND m.clave_estado = c.estado WHERE c.id_emisor = ".$_SESSION['id_emisor']." AND c.estatus = 1 ORDER BY c.nombre_social ASC";
        $resCliente = mysqli_query($conexion, $sqlCliente);
        while($cliente = mysqli_fetch_array($resCliente))
        {
            if($factura['id_cliente'] == $cliente['id_cliente'])
            {
                $options_clientes .= "<option value='".$cliente['id_cliente']."' selected>".$cliente['nombre_social']."</option>";
                $display_domicilio = "";
                $domicilio = $cliente['domicilio'];
                $rfc = $cliente['rfc'];
                $uso_cfdi = $cliente['uso_cfdi'];
                $metodo_pago = $cliente['metodo_pago'];
                $forma_pago = $cliente['forma_pago'];
            }
            else
            {
                $options_clientes .= "<option value='".$cliente['id_cliente']."'>".$cliente['nombre_social']."</option>";
            }
        }

        $consultaUsos = "SELECT clave_uso, descripcion FROM _cat_sat_uso_cfdi WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoUsos = mysqli_query($conexion, $consultaUsos);
        while($usos = mysqli_fetch_array($resultadoUsos))
        {
            if($uso_cfdi == $usos['clave_uso'])
            {
                $options_usos .= "<option value='".$usos['clave_uso']."' selected>[".$usos['clave_uso']."] ".$usos['descripcion']."</option>";
            }
            else
            {
                $options_usos .= "<option value='".$usos['clave_uso']."'>[".$usos['clave_uso']."] ".$usos['descripcion']."</option>";
            }
        }

        $consultaMetodo = "SELECT clave_metodo, descripcion FROM _cat_sat_metodo_pago WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoMetodo = mysqli_query($conexion, $consultaMetodo);
        while($metodo = mysqli_fetch_array($resultadoMetodo))
        {
            if($metodo_pago == $metodo['clave_metodo'])
            {
                $options_metodo .= "<option value='".$metodo['clave_metodo']."' selected>[".$metodo['clave_metodo']."] ".$metodo['descripcion']."</option>";
            }
            else
            {
                $options_metodo .= "<option value='".$metodo['clave_metodo']."'>[".$metodo['clave_metodo']."] ".$metodo['descripcion']."</option>";
            }
        }

        $consultaForma = "SELECT clave_forma, descripcion FROM _cat_sat_forma_pago WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoForma = mysqli_query($conexion, $consultaForma);
        while($forma = mysqli_fetch_array($resultadoForma))
        {
            if($forma_pago == $forma['clave_forma'])
            {
                $options_forma .= "<option value='".$forma['clave_forma']."' selected>[".$forma['clave_forma']."] ".$forma['descripcion']."</option>";
            }
            else
            {
                $options_forma .= "<option value='".$forma['clave_forma']."'>[".$forma['clave_forma']."] ".$forma['descripcion']."</option>";
            }
        }

        $consultaMoneda = "SELECT clave_moneda, descripcion FROM _cat_sat_monedas WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoMoneda = mysqli_query($conexion, $consultaMoneda);
        while($moneda = mysqli_fetch_array($resultadoMoneda))
        {
            if($factura['moneda'] == $moneda['clave_moneda'])
            {
                $options_moneda .= "<option value='".$moneda['clave_moneda']."' selected>[".$moneda['clave_moneda']."] ".$moneda['descripcion']."</option>";
            }
            else
            {
                $options_moneda .= "<option value='".$moneda['clave_moneda']."'>[".$moneda['clave_moneda']."] ".$moneda['descripcion']."</option>";
            }
        }

        $consultaRelacion = "SELECT clave_relacion, descripcion FROM _cat_sat_tipo_relacion WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoRelacion = mysqli_query($conexion, $consultaRelacion);
        while($relacion = mysqli_fetch_array($resultadoRelacion))
        {
            if($relacion['clave_relacion'] == "04")
            {
                $options_relacion .= "<option value='".$relacion['clave_relacion']."' selected>[".$relacion['clave_relacion']."] ".$relacion['descripcion']."</option>";
            }
            else
            {
                $options_relacion .= "<option value='".$relacion['clave_relacion']."'>[".$relacion['clave_relacion']."] ".$relacion['descripcion']."</option>";
            }
        }

        $consultaExpo = "SELECT clave_exportacion, descripcion FROM _cat_sat_exportacion WHERE estatus = 1 ORDER BY clave_exportacion ASC";
        $resultadoExpo = mysqli_query($conexion, $consultaExpo);
        while($expo = mysqli_fetch_array($resultadoExpo))
        {
            if($factura['exportacion'] == $expo['clave_exportacion'])
            {
                $options_exportacion .= "<option value='".$expo['clave_exportacion']."' selected>[".$expo['clave_exportacion']."] ".$expo['descripcion']."</option>";
            }
            else
            {
                $options_exportacion .= "<option value='".$expo['clave_exportacion']."'>[".$expo['clave_exportacion']."] ".$expo['descripcion']."</option>";
            }
        }

        $consultaPeriodo = "SELECT clave_periodicidad, descripcion FROM _cat_sat_periodicidad WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoPeriodo = mysqli_query($conexion, $consultaPeriodo);
        while($periodo = mysqli_fetch_array($resultadoPeriodo))
        {
            if($factura['periodicidad'] == $expo['clave_periodicidad'])
            {
                $options_periodicidad .= "<option value='".$periodo['clave_periodicidad']."' selected>[".$periodo['clave_periodicidad']."] ".$periodo['descripcion']."</option>";
            }
            else
            {
                $options_periodicidad .= "<option value='".$periodo['clave_periodicidad']."'>[".$periodo['clave_periodicidad']."] ".$periodo['descripcion']."</option>";
            }
        }

        $consultaMeses = "SELECT clave_meses, descripcion FROM _cat_sat_meses WHERE estatus = 1 ORDER BY descripcion ASC";
        $resultadoMeses = mysqli_query($conexion, $consultaMeses);
        while($meses = mysqli_fetch_array($resultadoMeses))
        {
            if($factura['meses'] == $expo['clave_meses'])
            {
                $options_meses .= "<option value='".$meses['clave_meses']."' selected>[".$meses['clave_meses']."] ".$meses['descripcion']."</option>";
            }
            else
            {
                $options_meses .= "<option value='".$meses['clave_meses']."'>[".$meses['clave_meses']."] ".$meses['descripcion']."</option>";
            }
        }

        $consultaConcepto = "SELECT id_concepto, clave_concepto, alias FROM emisores_facturas_conceptos WHERE id_emisor = ".$_SESSION['id_emisor']." AND estatus = 1 ORDER BY alias ASC";
        $resultadoConcepto = mysqli_query($conexion, $consultaConcepto);
        while($concepto = mysqli_fetch_array($resultadoConcepto))
        {
            $options_concepto .= "<option value='".$concepto['id_concepto']."'>[".$concepto['clave_concepto']."] ".$concepto['alias']."</option>";
        }
        ////////////////////////// TERMINA CARGAMOS CATALOGOS

        ///////////////////////// SE CARGAN LOS CFDI RELACIONADOS
        $consultaRelacionUUID = "SELECT cfdi.id_partida, cfdi.uuid_relacionado, r.descripcion FROM emisores_cfdi_relacionados cfdi INNER JOIN _cat_sat_tipo_relacion r ON r.clave_relacion = cfdi.clave_relacion WHERE cfdi.id_emisor = ".$_SESSION['id_emisor']." AND cfdi.id_documento = ".$_POST['id_documento']." AND cfdi.folio = ".$_POST['folio_factura']." AND cfdi.tipo_documento = 'F'";
        $resultadoRelacionUUID = mysqli_query($conexion, $consultaRelacionUUID);
        while($relacionUUID = mysqli_fetch_array($resultadoRelacionUUID))
        {
            $uuid_relacionados .= "
                <tr>
                    <td class='text-center text-sm'>".$relacionUUID['uuid_relacionado']."</td>
                    <td class='text-center text-sm'>".$relacionUUID['descripcion']."</td>
                    <td class='text-center text-sm'>
                        <button type='button' class='btn btn-danger btn-sm' title='Eliminar relaci&oacute;n' onclick='eliminar_relacion(".$relacionUUID['id_partida'].",".$_POST['id_documento'].",".$_POST['folio_factura'].")'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </td>
                </tr>
            ";
        }
        ///////////////////////// TERMINAN LOS CFDI RELACIONADOS

        $html .= '
            <input type="hidden" id="e_id_documento" value="'.$_POST['id_documento'].'">
            <input type="hidden" id="e_folio_factura" value="'.$_POST['folio_factura'].'">
            <input type="hidden" id="e_serie_factura" value="'.$_POST['serie_factura'].'">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title text-sm">Crear factura</h3>
                </div>
                <div class="card-body">
                    <h6 class="text-sm"><i class="fas fa-id-card"></i> Datos el cliente</h6><hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <select class="form-control text-sm" id="cliente" onfocus="resetear(&quot;cliente&quot;)" onchange="consultar_datos()">
                                    <option value="0">Selecciona cliente...</option>
                                    '.$options_clientes.'
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="datos_cliente" style="'.$display_domicilio.'">
                        <div class="col-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="rfc" value="'.$rfc.'" disabled>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marked"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="domicilio" value="'.$domicilio.'" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="datos_cliente2" style="display: none;">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="regimen" disabled>
                            </div>
                        </div>
                    </div>
                    <br><h6 class="text-sm"><i class="fas fa-info-circle"></i> Informaci&oacute;n general</h6><hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Datos generales</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Relacionar CFDI</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="false">Informaci&oacute;n global y exportaci&oacute;n</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-three-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="uso_cfdi" onfocus="resetear(&quot;uso_cfdi&quot;)">
                                                            <option value="0">Selecciona uso cfdi...</option>
                                                            '.$options_usos.'
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="metodo_pago" onfocus="resetear(&quot;metodo_pago&quot;)" onchange="activar_forma()">
                                                            <option value="0">Metodo de pago...</option>
                                                            '.$options_metodo.'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="forma_pago" onfocus="resetear(&quot;forma_pago&quot;)">
                                                            <option value="0">Forma de pago...</option>
                                                            '.$options_forma.'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="moneda" onfocus="resetear(&quot;moneda&quot;)" onchange="activar_moneda()">
                                                            <option value="0">Moneda...</option>
                                                            '.$options_moneda.'
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-cash-register"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control text-sm" id="tipo_cambio" placeholder="Tipo de cambio" onfocus="resetear(&quot;tipo_cambio&quot;)" value="'.$tc.'" '.$disabled_tc.'>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-cash-register"></i></span>
                                                        </div>
                                                        <input type="number" class="form-control text-sm" id="dias_credito" placeholder="D&iacute;as de credito" onfocus="resetear(&quot;dias_credito&quot;)" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-asterisk"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control text-sm" id="referencia" placeholder="Referencia" onfocus="resetear(&quot;referencia&quot;)" onkeypress="return check_caracter(event)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-eye"></i></span>
                                                        </div>
                                                        <textarea class="form-control text-sm" id="observaciones" placeholder="Observaciones de la factura" onfocus="resetear(&quot;observaciones&quot;)" onkeypress="return check_caracter(event)"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-arrows-alt-h"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="tipo_relacion" onfocus="resetear(&quot;tipo_relacion&quot;)">
                                                            <option value="0">Selecciona tipo relaci&oacute;n...</option>
                                                            '.$options_relacion.'
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control text-sm" id="uuid_relacion" placeholder="UUID a relacionar" onfocus="resetear(&quot;uuid_relacion&quot;)">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <button type="button" class="btn btn-success text-sm" onclick="agregar_relacion();"><i class="fas fa-plus"></i> Relacionar UUID</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="sticky text-center text-sm">UUID</th>
                                                            <th class="sticky text-center text-sm">Tipo Relaci&oacute;n</th>
                                                            <th class="sticky text-center text-sm">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="mostrar_uuid_relacionados">
                                                        '.$uuid_relacionados.'
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel" aria-labelledby="custom-tabs-three-messages-tab">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-file-export"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="exportacion" onfocus="resetear(&quot;exportacion&quot;)">
                                                            '.$options_exportacion.'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="periodicidad" onfocus="resetear(&quot;periodicidad&quot;)" '.$disabled_periodo.'>
                                                            <option value="0">Selecciona tipo periodicidad...</option>
                                                            '.$options_periodicidad.'
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                        </div>
                                                        <select class="form-control text-sm" id="meses" onfocus="resetear(&quot;meses&quot;)" '.$disabled_mes.'>
                                                            <option value="0">Selecciona mes...</option>
                                                            '.$options_meses.'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-history"></i></span>
                                                        </div>
                                                        <input type="text" class="form-control text-sm" id="anio" placeholder="A&ntilde;o" onfocus="resetear(&quot;anio&quot;)" value="'.$factura['ano'].'" '.$disabled_anio.'>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><h6 class="text-sm"><i class="fas fa-store"></i> Conceptos de la factura</h6><hr>
                    <div class="callout callout-info">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-store"></i></span>
                                    </div>
                                    <select class="form-control text-sm" id="concepto_sat" onfocus="resetear(&quot;concepto_sat&quot;)" onchange="buscar_concepto()">
                                        <option value="0">Selecciona concepto...</option>
                                        '.$options_concepto.'
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    </div>
                                    <input type="number" class="form-control text-sm" id="cantidad" placeholder="Cantidad" onfocus="resetear(&quot;cantidad&quot;)">
                                    <input type="hidden" id="e_partida" value="0">
                                    <input type="hidden" id="clave_sat_concepto">
                                    <input type="hidden" id="clave_sat_medida">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control text-sm" id="precio" placeholder="Precio" onfocus="resetear(&quot;precio&quot;)">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                    </div>
                                    <input type="number" class="form-control text-sm" id="iva" placeholder="IVA" onfocus="resetear(&quot;iva&quot;)">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                    </div>
                                    <input type="number" class="form-control text-sm" id="retencion" placeholder="Retenci&oacute;n" onfocus="resetear(&quot;retencion&quot;)">
                                </div>
                            </div>
                            <div class="4">
                                <div class="input-group">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="iva_exento" onclick="validar_iva_exento()">
                                        <label for="iva_exento">
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
                                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                                    </div>
                                    <textarea class="form-control text-sm" id="descripcion_concepto" placeholder="Descripci&oacute;n del concepto" onfocus="resetear(&quot;descripcion_concepto&quot;)" onkeypress="return check_caracter(event)"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <center><button type="button" class="btn btn-success text-sm" onclick="agregar_producto();"><i class="fas fa-plus"></i> Agregar concepto</button></center><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" id="tabla_productos">
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
                                        '.$partidas_conceptos.'
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br><h6 class="text-sm"><i class="fas fa-money-check"></i> Totales</h6><hr>
                    <div class="row">
                        <div class="col-2">
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="mostrar_isr" '.$check_isr.' onclick="activar_isr();">
                                    <label for="mostrar_isr">Retenci&oacute;n de ISR</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="porcentaje_isr" title="Porcentaje Retenci&oacute;n ISR" placeholder="Retenci&oacute;n de ISR" value="'.$factura['porcentaje_retencion_isr'].'" onkeyup="calcular_isr();" '.$disabled_isr.'>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="subtotal" title="Subtotal" value="'.$factura['subtotal'].'" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="iva_total" title="IVA" value="'.$factura['iva'].'" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="retencion_iva" title="Retenci&oacute;n IVA" value="'.$factura['retencion_iva'].'" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="retencion_isr" title="Retenci&oacute;n ISR" value="'.$factura['retencion_isr'].'" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control text-sm" id="total" title="Total" value="'.$factura['total'].'" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';

        echo $html;
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
      "autoWidth": false,
      "responsive": true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
  });
</script>
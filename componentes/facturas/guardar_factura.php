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
        $fecha_emision = date("Y-m-d");
        $sql_cliente = "SELECT rfc, nombre_social, calle, no_exterior, no_interior, codigo_postal, colonia, municipio, estado, pais, regimen_fiscal, residencia_fiscal, id_tributario FROM emisores_clientes WHERE id_cliente = ".$_POST['id_cliente']." AND id_emisor = ".$_SESSION['id_emisor'];
        $res_cliente = mysqli_query($conexion, $sql_cliente);
        $cliente = mysqli_fetch_array($res_cliente);

        $sql_update = "
            UPDATE emisores_facturas SET 
                fecha_emision = '".$fecha_emision."', 
                id_cliente = ".$_POST['id_cliente'].", 
                rfc = '".$cliente['rfc']."',
                nombre = '".$cliente['nombre_social']."', 
                calle = '".$cliente['calle']."',
                no_exterior = '".$cliente['no_exterior']."',
                no_interior = '".$cliente['no_interior']."',
                colonia = '".$cliente['colonia']."',
                codigo_postal = '".$cliente['codigo_postal']."',
                municipio = '".$cliente['municipio']."',
                estado = '".$cliente['estado']."',
                pais = '".$cliente['pais']."',
                regimen_fiscal = '".$cliente['regimen_fiscal']."',
                residencia_fiscal = '".$cliente['residencia_fiscal']."',
                numero_residencia = '".$cliente['id_tributario']."',
                dias_credito = ".$_POST['dias_credito'].",
                metodo_pago = '".$_POST['metodo_pago']."',
                forma_pago = '".$_POST['forma_pago']."',
                uso_cfdi = '".$_POST['uso_cfdi']."',
                moneda = '".$_POST['moneda']."',
                tipo_cambio = ".$_POST['tipo_cambio'].",
                subtotal = ".$_POST['subtotal'].",
                iva = ".$_POST['iva_total'].",
                retencion_iva = ".$_POST['retencion_iva'].",
                porcentaje_retencion_isr = ".$_POST['porcentaje_isr'].",
                retencion_isr = ".$_POST['retencion_isr'].",
                total = ".$_POST['total'].",
                exportacion = '".$_POST['exportacion']."',
                periodicidad = '".$_POST['periodicidad']."',
                meses = '".$_POST['meses']."',
                ano = '".$_POST['anio']."',
                estatus = 2,
                id_usuario = ".$_SESSION['id_usuario'].",
                observaciones = '".strtoupper($_POST['observaciones'])."',
                referencia = '".strtoupper($_POST['referencia'])."',
                saldo = ".$_POST['total']."
            WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_documento = ".$_POST['id_documento']." AND folio_factura = ".$_POST['folio_factura']."
        ";
        $res_update = mysqli_query($conexion, $sql_update);

        if($res_update)
        {
            $html = "
                <td class='text-center'>
                <div class='btn-group'>
                    <button type='button' id='btn_xml_".$_POST['id_documento'].$_POST['folio_factura']."' class='btn btn-secondary btn-sm' disabled title='Descargar XML' onclick='descargar_xml(".$_POST['id_documento'].",".$_POST['folio_factura'].")'>
                        <i class='fas fa-code'></i>
                    </button>
                    &nbsp;
                    <button type='button' id='btn_pdf_".$_POST['id_documento'].$_POST['folio_factura']."' class='btn btn-primary btn-sm' title='Ver PDF' onclick='ver_pdf(".$_POST['id_documento'].",".$_POST['folio_factura'].", &quot;".$_POST['serie_factura']."&quot;)'>
                        <i class='fas fa-copy'></i>
                    </button>
                    &nbsp;
                    <button type='button' id='btn_edit_".$_POST['id_documento'].$_POST['folio_factura']."' class='btn btn-warning btn-sm' title='Editar factura' onclick='editar_factura(".$_POST['id_documento'].",".$_POST['folio_factura'].", &quot;".$_POST['serie_factura']."&quot;)'>
                        <i class='fas fa-pencil-alt'></i>
                    </button>
                    &nbsp;
                    <button type='button' id='btn_envio_".$_POST['id_documento'].$_POST['folio_factura']."' class='btn btn-success btn-sm' title='Enviar archivos' onclick='evniar_correo(".$_POST['id_documento'].",".$_POST['folio_factura'].")'>
                        <i class='fas fa-paper-plane'></i>
                    </button>
                    &nbsp;
                    <button type='button' id='btn_can_".$_POST['id_documento'].$_POST['folio_factura']."' class='btn btn-danger btn-sm' title='Cancelar factura' onclick='cancelar_factura(".$_POST['id_documento'].",".$_POST['folio_factura'].")'>
                        <i class='fas fa-ban'></i>
                    </button>
                </div>
            </td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$_POST['serie_factura']."</td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$_POST['folio_factura']."</td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".date("d/m/Y", strtotime($fecha_emision))."</td>
            <td class='text-center text-sm' id='td_ef_".$_POST['id_documento'].$_POST['folio_factura']."' style='white-space: nowrap; overflow-x: auto;'><span class='badge badge-warning' style='width: 100%; color:white;'>PROFORMA</span></td>
            <td class='text-center text-sm' id='td_ec_".$_POST['id_documento'].$_POST['folio_factura']."' style='white-space: nowrap; overflow-x: auto;'><span class='badge badge-danger' style='width: 100%; color:white;'>POR COBRAR</span></td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$cliente['nombre_social']."</td>
            <td class='text-center text-sm'></td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>$ ".$_POST['total']."</td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>$ ".$_POST['referencia']."</td>
            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$_SESSION['nombre_usuario']."</td>
            ";
            echo $html;
        }
        else
        {
            echo $sql_update;
        }
    }
?>
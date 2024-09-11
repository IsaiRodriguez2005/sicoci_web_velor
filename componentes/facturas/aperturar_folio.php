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
        $sql_folio = "SELECT serie, folio, codigo_postal FROM emisores_series WHERE id_partida = ".$_POST['id_partida']." AND id_emisor = ".$_SESSION['id_emisor']." AND id_documento = ".$_POST['id_documento'];
        $res_folio = mysqli_query($conexion, $sql_folio);
        $folio = mysqli_fetch_array($res_folio);

        $fecha_emision = date("Y-m-d");

        $sql_aperturar = "
            INSERT INTO `emisores_facturas` 
            (`id_emisor`, `id_documento`, `folio_factura`, `serie_factura`, `fecha_emision`, `lugar_emision`, `id_cliente`, `rfc`, `nombre`, `calle`, `no_exterior`, `no_interior`, `colonia`, `codigo_postal`, `municipio`, `estado`, `pais`, `regimen_fiscal`, `residencia_fiscal`, `numero_residencia`, `dias_credito`, `metodo_pago`, `forma_pago`, `uso_cfdi`, `moneda`, `tipo_cambio`, `subtotal`, `iva`, `retencion_iva`, `retencion_isr`, `total`, `exportacion`, `periodicidad`, `meses`, `ano`, `uuid`, `version_cfdi`, `fecha_timbrado`, `no_certificado`, `no_certificado_sat`, `sello`, `sello_sat`, `cadena_original`, `estatus`, `estatus_cobranza`, `id_usuario`, `observaciones`, `referencia`, `saldo`) 
            VALUES 
            (".$_SESSION['id_emisor'].", ".$_POST['id_documento'].", ".$folio['folio'].", '".$folio['serie']."', '".$fecha_emision."', '".$folio['codigo_postal']."', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', 'MXN', 1, 0, 0, 0, 0, 0, '01', '', '', '', '', '4.0', '', '', '', '', '', '', 1, 1, ".$_SESSION['id_usuario'].", '', '', 0);
        ";
        $res_aperturar = mysqli_query($conexion, $sql_aperturar);
        
        if($res_aperturar)
        {
            $nuevo_folio = $folio['folio'] + 1;
            $sql_update = "UPDATE emisores_series SET folio = ".$nuevo_folio." WHERE id_partida = ".$_POST['id_partida']." AND id_emisor = ".$_SESSION['id_emisor']." AND id_documento = ".$_POST['id_documento'];
            $res_update = mysqli_query($conexion, $sql_update);

            if($res_update)
            {
                $html['estatus'] = "ok";
                $html['folio'] = $folio['folio'];
                $html['serie'] = $folio['serie'];
                $html['registro_tr'] = "
                    <tr id='tr_".$_POST['id_documento'].$folio['folio']."'>
                        <td class='text-center'>
                            <div class='btn-group'>
                                <button type='button' id='btn_xml_".$_POST['id_documento'].$folio['folio']."' class='btn btn-secondary btn-sm' disabled title='Descargar XML' onclick='descargar_xml(".$_POST['id_documento'].",".$folio['folio'].")' disabled>
                                    <i class='fas fa-code'></i>
                                </button>
                                &nbsp;
                                <button type='button' id='btn_pdf_".$_POST['id_documento'].$folio['folio']."' class='btn btn-primary btn-sm' disabled title='Ver PDF' onclick='ver_pdf(".$_POST['id_documento'].",".$folio['folio'].")' disabled>
                                    <i class='fas fa-copy'></i>
                                </button>
                                &nbsp;
                                <button type='button' id='btn_edit_".$_POST['id_documento'].$folio['folio']."' class='btn btn-warning btn-sm' title='Editar factura' onclick='editar_factura(".$_POST['id_documento'].",".$folio['folio'].")'>
                                    <i class='fas fa-pencil-alt'></i>
                                </button>
                                &nbsp;
                                <button type='button' id='btn_envio_".$_POST['id_documento'].$folio['folio']."' class='btn btn-success btn-sm' disabled title='Enviar archivos' onclick='enviar_correo(".$_POST['id_documento'].",".$folio['folio'].")' disabled>
                                    <i class='fas fa-paper-plane'></i>
                                </button>
                                &nbsp;
                                <button type='button' id='btn_can_".$_POST['id_documento'].$folio['folio']."' class='btn btn-danger btn-sm' title='Cancelar factura' onclick='cancelar_factura(".$_POST['id_documento'].",".$folio['folio'].")'>
                                    <i class='fas fa-ban'></i>
                                </button>
                            </div>
                        </td>
                        <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$folio['serie']."</td>
                        <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$folio['folio']."</td>
                        <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".date("d/m/Y", strtotime($fecha_emision))."</td>
                        <td class='text-center text-sm' id='td_ef_".$_POST['id_documento'].$folio['folio']."' style='white-space: nowrap; overflow-x: auto;'><span class='badge badge-danger' style='width: 100%; color:white;'>APERTURADO</span></td>
                        <td class='text-center text-sm' id='td_ec_".$_POST['id_documento'].$folio['folio']."' style='white-space: nowrap; overflow-x: auto;'><span class='badge badge-danger' style='width: 100%; color:white;'>POR COBRAR</span></td>
                        <td class='text-center text-sm'></td>
                        <td class='text-center text-sm'></td>
                        <td class='text-center text-sm'>0.00</td>
                        <td class='text-center text-sm'></td>
                        <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$_SESSION['nombre_usuario']."</td>
                    </tr>
                ";

                echo json_encode($html);
            }
            else
            {
                $sql_eliminar = "DELETE FROM emisores_facturas WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_documento = ".$_POST['id_documento']." AND folio = ".$folio['folio'];
                mysqli_query($conexion, $sql_eliminar);
                $html['estatus'] = "error";
                echo json_encode($html);
            }
        }
        else
        {
            $html['estatus'] = "error";
            echo json_encode($html);
        }
    }
?>
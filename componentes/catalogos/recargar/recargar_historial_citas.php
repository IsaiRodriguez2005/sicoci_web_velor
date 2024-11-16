<?php
session_start();
require("../../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    print_r($_POST);

    $html = '';
    $sql = "SELECT ag.*, cl.nombre_cliente, 
                        p.nombre_personal,
                        co.nombre AS nombre_consultorio,
                        ex.folio AS expediente       
                                    FROM emisores_agenda AS ag
                                        LEFT JOIN emisores_clientes AS cl ON cl.id_cliente = ag.id_cliente AND cl.id_emisor = ag.id_emisor
                                        LEFT JOIN emisores_personal AS p ON p.id_personal = ag.id_terapeuta AND p.id_emisor = ag.id_emisor
                                        LEFT JOIN emisores_consultorios AS co ON co.id_consultorio = ag.id_consultorio AND co.id_emisor = ag.id_emisor
                                        LEFT JOIN emisores_historial_expediente AS ex ON ex.id_folio_cita = ag.id_folio AND ex.id_emisor = ag.id_emisor
                                    WHERE ag.id_folio=" . $_POST['id_folio'] . " AND ag.id_emisor=" . $_SESSION['id_emisor'] . ';';
    //echo $sql;
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        $citas = mysqli_fetch_array($resultado);

        if (isset($citas['expediente'])) {
            $pdfValoracion = "<button type='button' id='btn_pdf_" . $citas['expediente'] . "' class='btn btn-primary btn-sm' title='Ver PDF' onclick='ver_pdf(" . $citas['expediente'] . ", " . $citas['tipo_cita'] . ")' >
                                <i class='fas fa-copy'></i>
                            </button>";
        } else {
            $pdfValoracion = '';
        }
        switch ($citas['estatus']) {
            case 1:
                $estatus = '<span class="badge badge-danger" style="width: 100%; color:white;">APERTURADO</span>';
                $boton_editar = '';
                $valoracion = '';
                $boton_cancelar = '';
                break;
            case 2:
                $estatus = '<span class="badge badge-warning" style="width: 100%; color:white;">AGENDADO</span>';
                $boton_editar = '';
                $valoracion = '';
                $boton_cancelar = '';
                break;
            case 3:
                $estatus = '<span class="badge badge-success" style="width: 100%; color:white;">REALIZADO</span>';
                $boton_editar = 'disabled';
                $valoracion = 'disabled';
                $boton_cancelar = 'disabled';
                $boton_cobrar = '';
                break;
            case 4:
                $estatus = '<span class="badge badge-secondary" style="width: 100%; color:white;">CANCELADO</span>';
                $boton_editar = 'disabled';
                $valoracion = '';
                $boton_cancelar = 'disabled';
                $boton_cobrar = 'disabled';
                break;
        }

        if (empty($_SESSION['id_personal'])) {
            $acciones = "
                                <button type='button' id='btn_edit_" . $citas['id_folio'] . "' class='btn btn-warning btn-sm' " . $boton_editar . " title='Editar cita' onclick='editar_cita(" . $citas['id_folio'] . ")'>
                                    <i class='fas fa-pencil-alt'></i>
                                </button>
                                &nbsp;
                                <button type='button' id='btn_can_" . $citas['id_folio'] . "' class='btn btn-danger btn-sm' " . $boton_cancelar . " title='Cancelar cita' onclick='cancelar_cita(" . $citas['id_folio'] . ", &quot;" . $citas['nombre_cliente'] . "&quot;, &quot;" . $citas['nombre_personal'] . "&quot;, &quot;" . $citas['nombre_consultorio'] . "&quot;)'>
                                    <i class='fas fa-ban'></i>
                                </button>
                                &nbsp;
                                " . $pdfValoracion . "
                                ";
        } else {
            $acciones = "
                                <button type='button' id='btn_rea_" . $citas['id_folio'] . "' class='btn btn-success btn-sm' " . $boton_cancelar . " " . $valoracion . " title='Realizar Valoracion' " . $btn_realizar_valoracion . ">
                                    <i class='fas fa-check'></i>
                                </button>
                                &nbsp;
                                " . $pdfValoracion . "
                ";
        }

        switch ($citas['tipo_servicio']) {
            case 1:
                $tipo_servicio = 'CONSULTORIO';

                break;

            case 2:
                $tipo_servicio = 'DOMICILIO';
        }

        switch ($citas['tipo_cita']) {
            case 1:
                $tipo_cita = 'SEGUIMIENTO';
                $btn_realizar_valoracion = "onclick='realizar_valoracion_subs(" . $citas['id_folio'] . ", " . $citas['id_cliente'] . ")'";
                break;

            case 2:
                $tipo_cita = 'PRIMERA VEZ';
                $btn_realizar_valoracion = "onclick='realizar_valoracion_primera_v(" . $citas['id_folio'] . ", " . $citas['id_cliente'] . ")'";
        }


        $html .= "
                <tr id='tr_" . $citas['id_folio'] . "'>
                    <td class='text-center' style='position: sticky; left: 0; background: whitesmoke;'>
                        <div class='btn-group'>
                            $acciones

                        </div>
                    </td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $citas['id_folio'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . date("d/m/Y", strtotime($citas['fecha_agenda'])) . "</td>
                    <td class='text-center text-sm' id='td_ef_" . $citas['id_folio'] . "' style='white-space: nowrap; overflow-x: auto;'>" . $estatus . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $citas['nombre_cliente'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $citas['nombre_personal'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $citas['nombre_consultorio'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $tipo_cita . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $tipo_servicio . "</td>
                </tr>
            ";

        echo $html;
    } else {
        echo 'e';
    }
}

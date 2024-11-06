<?php
session_start();
require("../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $html = '';
    $html .= '
            <table class="table table-striped" id="tabla_facturas" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Acciones</th>
                        <th class="sticky text-center text-sm">Folio</th>
                        <th class="text-center text-sm">Fecha de Agenda</th>
                        <th class="text-center text-sm">Estatus</th>
                        <th class="sticky text-center text-sm">Cliente</th>
                        <th class="text-center text-sm">Terapeuta</th>
                        <th class="text-center text-sm">Consultorio</th>
                        <th class="text-center text-sm">Tipo de Cita</th>
                        <th class="text-center text-sm">Tipo de Servicio</th>
                    </tr>
                </thead>
                <tbody id="mostrar_clientes">
        ';

    // cosultas de busqueda
    $consulta = "SELECT 
            a.id_folio, 
            a.id_cliente, 
            a.id_consultorio, 
            a.id_terapeuta, 
            a.tipo_servicio, 
            a.tipo_cita, 
            a.fecha_emision, 
            a.fecha_agenda, 
            a.estatus, 
            a.observaciones,
            p.nombre_personal,
            c.nombre as nombre_consultorio,
            cli.nombre_cliente as nombre_cliente,
            cli.fec_nac as fecha_nacimiento,
            cli.ocupacion as ocupacion,
            cli.est_civ as estado_civil,
            cli.telefono as telefono_cliente
            FROM emisores_agenda a 
            LEFT JOIN emisores_personal p ON a.id_terapeuta = p.id_personal AND a.id_emisor = p.id_emisor AND p.tipo = 2
            LEFT JOIN emisores_consultorios c ON a.id_consultorio = c.id_consultorio AND a.id_emisor = c.id_emisor
            LEFT JOIN emisores_clientes cli ON a.id_cliente = cli.id_cliente AND a.id_emisor = cli.id_emisor
        ";


    if (!empty($_POST)) // si {$_POST} contiene algo, ejecutara las validaciones
    {
        // agregamos el {WHERE} a la consulta
        $consulta .= " WHERE a.id_emisor = " . $_SESSION['id_emisor'] . " AND ";

        // primera validacion, si hay {cliente}
        if (!empty($_POST['id_cliente'])) {

            $consulta .= " a.id_cliente = " . $_POST['id_cliente'];
        }

        // RANGO DE FECHAS -------------------------------------BETWEEN---------------------------------

        // sexta validacion, si hay {fecha de agenda}
        if (!empty($_POST['fecha_inicial'])) {

            // si el cliente o estatus o terapeuta o consultoio existio antes, pondremos el {AND} 
            if (!empty($_POST['id_terapeuta']) || !empty($_POST['estatus']) || !empty($_POST['id_cliente']) || !empty($_POST['id_consultorio'])) $consulta .= " AND ";

            // agregamos el {BETWEEN}
            $consulta .= ' a.fecha_agenda >= ';


            $consulta .= "'" . $_POST['fecha_inicial'] . " 08:00:00 ' AND a.fecha_agenda <= '" . $_POST['fecha_final'] . " 16:00:00 '";
        }

        if (!empty($_SESSION['id_personal'])) {
            $consulta .= 'AND a.id_terapeuta = ' . $_SESSION['id_personal'] . '';
        }


        // cerramos la consulta
        $consulta .= " ORDER BY a.fecha_agenda DESC;";
    }

    $resCitas = mysqli_query($conexion, $consulta);
    while ($citas = mysqli_fetch_array($resCitas)) {
        $fechaHora = $citas['fecha_agenda'];

        // Crear un objeto DateTime desde la cadena de fecha y hora
        $dateTime = new DateTime($fechaHora);

        // Obtener la fecha en formato 'YYYY-MM-DD'
        $fecha = $dateTime->format('Y-m-d');

        // Obtener la hora en formato 'HH:MM:SS'
        $hora = $dateTime->format('H:i:s');


        $query_valoacion = 'SELECT folio FROM emisores_historial_expediente WHERE id_folio_cita = ' . $citas['id_folio'] . '';
        $res_val = mysqli_query($conexion, $query_valoacion);

        if ($res_val) {

            $row = mysqli_fetch_array($res_val);
            if ($row && isset($row['folio']) && $row['folio'] > 0) {
                $pdfValoracion = "<button type='button' id='btn_pdf_" . $row['folio'] . "' class='btn btn-primary btn-sm' title='Ver PDF' onclick='ver_pdf(" . $row['folio'] . ", " . $citas['tipo_cita'] . ")' >
                                    <i class='fas fa-copy'></i>
                                </button>";
            } else {
                $pdfValoracion = '';
            }
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

        switch ($citas['tipo_cita']) {
            case 1:
                $tipo_cita = 'SEGUIMIENTO';
                $btn_realizar_valoracion = "onclick='realizar_valoracion_subs(" . $citas['id_folio'] . ", " . $citas['id_cliente'] . ")'";
                break;

            case 2:
                $tipo_cita = 'PRIMERA VEZ';
                $btn_realizar_valoracion = "onclick='realizar_valoracion_primera_v(" . $citas['id_folio'] . ", " . $citas['id_cliente'] . ")'";
        }

        switch ($citas['tipo_servicio']) {
            case 1:
                $tipo_servicio = 'CONSULTORIO';

                break;

            case 2:
                $tipo_servicio = 'DOMICILIO';
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


        $html .= "
                <tr id='tr_" . $citas['id_folio'] . "'>
                    <td class='text-center'>
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
    }
    $html .= "
                </tbody>
            </table>
        ";

    echo $html;
}
?>
<script>
    $(function() {
        $('#tabla_facturas').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            "order": [
                [3, 'asc'],
                [2, 'asc'],
                [1, 'des'],
            ],
        });
    });
</script>
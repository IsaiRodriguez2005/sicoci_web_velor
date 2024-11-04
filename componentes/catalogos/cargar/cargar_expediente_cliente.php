<?php
session_start();
require("../../conexion.php");

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
            <table class="table table-striped" id="expedientes_citas_table" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Acciones</th>
                        <th class="sticky text-center text-sm">Folio</th>
                        <th class="text-center text-sm">Fecha de Realización</th>
                        <th class="sticky text-center text-sm">Cliente</th>
                    </tr>
                </thead>
                <tbody id="mostrar_expediente">
        ';


    $consExpedientes = "SELECT e.fecha_emision, e.folio, e.id_cliente, c.nombre_cliente 
                                    FROM emisores_historial_expediente AS e INNER JOIN emisores_clientes AS c ON e.id_cliente = c.id_cliente 
                                    WHERE e.id_cliente = " . $_POST['id_cliente'] . " AND e.id_emisor = " . $_SESSION['id_emisor'] . "";
                                    //echo $consExpedientes;
    $resultado = mysqli_query($conexion, $consExpedientes);

    while ($expediente = mysqli_fetch_array($resultado)) {

        $fecha = $expediente['fecha_emision'];

        // Crear un objeto DateTime desde la cadena de fecha y hora
        $fecha = new DateTime($fecha);

        // Obtener la fecha en formato 'YYYY-MM-DD'
        $fecha = $fecha->format('d-m-Y');

        /*
        switch ($expediente['estatus']) {
            case 1:
                $estatus = '<span class="badge badge-danger" style="width: 100%; color:white;">APERTURADO</span>';
                $boton_editar = '';
                $boton_cancelar = '';
                break;
            case 2:
                $estatus = '<span class="badge badge-warning" style="width: 100%; color:white;">AGENDADO</span>';
                $boton_editar = '';
                $boton_cancelar = '';
                break;
            case 3:
                $estatus = '<span class="badge badge-success" style="width: 100%; color:white;">REALIZADO</span>';
                $boton_editar = 'disabled';
                $boton_cancelar = '';
                $boton_cobrar = '';
                break;
            case 4:
                $estatus = '<span class="badge badge-secondary" style="width: 100%; color:white;">CANCELADO</span>';
                $boton_editar = 'disabled';
                $boton_cancelar = 'disabled';
                $boton_cobrar = 'disabled';
                break;
        }

        switch ($expediente['tipo_cita']) {
            case 1:
                $tipo_cita = 'SEGUIMIENTO';
                break;

            case 2:
                $tipo_cita = 'PRIMERA VEZ';
        }

        switch ($expediente['tipo_servicio']) {
            case 1:
                $tipo_servicio = 'CONSULTORIO';
                break;

            case 2:
                $tipo_servicio = 'DOMICILIO';
        }
                */

        if (!empty($_SESSION['id_personal'])) {

            $acciones = "
            <!--
                            <button type='button' id='btn_rea_" . $expediente['folio'] . "' class='btn btn-warning btn-sm' title='Cita Realizada' onclick='mostrar_valoracion(" . $expediente['folio'] . "," . $expediente['id_cliente'] . ")'>
                                <i class='fas fa-file'></i>
                                <i class='far fa-file'></i>
                            </button>
                            -->
                            <button type='button' id='btn_pdf_".$expediente['folio']."' class='btn btn-primary btn-sm' title='Ver PDF' onclick='ver_pdf(".$expediente['folio'].")' >
                                <i class='fas fa-copy'></i>
                            </button>
            ";
        }


        $html .= "
                <tr id='tr_" . $expediente['folio'] . "'>
                    <td class='text-center'>
                        <div class='btn-group'>
                            $acciones
                        </div>
                    </td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $expediente['folio'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $fecha . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $expediente['nombre_cliente'] . "</td>
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
        $('#expedientes_citas_table').DataTable({
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
            "order" : [[1, 'desc']],

        });
    });
</script>
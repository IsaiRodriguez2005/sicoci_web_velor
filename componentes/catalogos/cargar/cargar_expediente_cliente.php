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
                <tbody id="mostrar_expediente">
        ';

    $consTerapeutas = "SELECT nombre_personal, correo FROM emisores_personal WHERE tipo = 2 AND id_personal = " . $_POST['id_terapeuta'] . "";
    $resultado = mysqli_query($conexion, $consTerapeutas);

    while ($citas = mysqli_fetch_array($resCitas)) {
        $fechaHora = $citas['fecha_agenda'];

        // Crear un objeto DateTime desde la cadena de fecha y hora
        $dateTime = new DateTime($fechaHora);

        // Obtener la fecha en formato 'YYYY-MM-DD'
        $fecha = $dateTime->format('Y-m-d');

        // Obtener la hora en formato 'HH:MM:SS'
        $hora = $dateTime->format('H:i:s');

        switch ($citas['estatus']) {
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

        switch ($citas['tipo_cita']) {
            case 1:
                $tipo_cita = 'SEGUIMIENTO';
                break;

            case 2:
                $tipo_cita = 'PRIMERA VEZ';
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
                            <button type='button' id='btn_edit_" . $citas['id_folio'] . "' class='btn btn-warning btn-sm' " . $boton_editar . " title='Editar cita' onclick='editar_cita(" . $citas['id_folio'] . ", &quot;" . $citas['nombre_cliente'] . "&quot;, " . $citas['id_consultorio'] . ", " . $citas['id_terapeuta'] . ", " . $citas['tipo_servicio'] . ", " . $citas['tipo_cita'] . ", &quot;" . $fecha . "&quot;, &quot;" . $hora . "&quot;, &quot;" . $citas['observaciones'] . "&quot;);')'>
                                <i class='fas fa-pencil-alt'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_can_" . $citas['id_folio'] . "' class='btn btn-danger btn-sm' " . $boton_cancelar . " title='Cancelar cita' onclick='cancelar_cita(" . $citas['id_folio'] . ", &quot;" . $citas['nombre_cliente'] . "&quot;, &quot;" . $citas['nombre_personal'] . "&quot;, &quot;" . $citas['nombre_consultorio'] . "&quot;)'>
                                <i class='fas fa-ban'></i>
                            </button>
                            ";
        } else {
            $acciones = "
                            <button type='button' id='btn_rea_" . $citas['id_folio'] . "' class='btn btn-success btn-sm' " . $boton_cancelar . " title='Cita Realizada' onclick='realizar_cita(" . $citas['id_folio'] . ", &quot;" . $citas['nombre_cliente'] . "&quot;, &quot;" . $citas['nombre_personal'] . "&quot;, &quot;" . $citas['nombre_consultorio'] . "&quot;)'>
                                <i class='fas fa-check'></i>
                            </button>
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


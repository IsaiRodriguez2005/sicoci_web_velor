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
            <table class="table table-striped" id="tabla_permisos" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Acciones</th>
                        <th class="sticky text-center text-sm"># Permiso</th>
                        <th class="text-center text-sm">Fecha Inicio</th>
                        <th class="text-center text-sm">Fecha Fin</th>
                        <th class="sticky text-center text-sm">Motivo</th>
                        <th class="sticky text-center text-sm">Estatus</th>
                    </tr>
                </thead>
                <tbody id="mostrar_permisos">
        ';

    // cosultas de busqueda
    $consulta = "SELECT * FROM emisores_personal_permisos WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_personal = " . $_POST['id_personal'] . "";
    $resPermisos = mysqli_query($conexion, $consulta);
    while ($permiso = mysqli_fetch_array($resPermisos)) {

        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");

        switch (intval($permiso['estatus'])) {
            case 1:
                $estatus = '<span class="badge badge-success" style="width: 100%; color:white;">AUTORIZADO</span>';
                $boton_editar = '';
                $boton_cancelar = '';
                $codigo_estatus = 2;
                break;
            case 2:
                $estatus = '<span class="badge badge-secondary" style="width: 100%; color:white;">CANCELADO</span>';
                $boton_editar = 'disabled';
                $boton_cancelar = 'disabled';
                $boton_cobrar = 'disabled';
                $codigo_estatus = 1;
                break;
        }

        $html .= "
                <tr id='tr_" . $permiso['id_permiso'] . "'>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <button type='button' id='btn_edit_" . $permiso['id_permiso'] . "' class='btn btn-warning btn-sm' " . $boton_editar . " title='Editar cita' onclick='editar_permiso(" . $permiso['id_permiso'] . ", " . $permiso['id_personal'] . ", &quot;" . $permiso['fecha_inicial'] . "&quot; , &quot;" . $permiso['fecha_final'] . "&quot; , &quot; " . $permiso['motivo'] . "&quot;)'>
                                <i class='fas fa-pencil-alt'></i>
                            </button>
                            &nbsp;
                            
                            <button type='button' id='btn_can_" . $permiso['id_permiso'] . "' class='btn btn-danger btn-sm' " . $boton_cancelar . " title='Cancelar cita' onclick='cancelar_permiso(" . $permiso['id_permiso'] . ", ".$codigo_estatus.")'>
                                <i class='fas fa-ban'></i>
                            </button>
                        </div>
                    </td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $permiso['id_permiso'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $permiso['fecha_inicial'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $permiso['fecha_final'] . "</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $permiso['motivo'] . "</td>
                    <td class='text-center text-sm' id='td_ef_" . $permiso['id_permiso'] . "' style='white-space: nowrap; overflow-x: auto;'>" . $estatus . "</td>
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
        $('#tabla_permisos').DataTable({
            "destroy": true,
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
            "order": [[1, "desc"]]
        });
    });
</script>
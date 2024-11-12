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
    $html = '
            <table class="table table-striped" id="tabla_cliente" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center" style="position: sticky; left: 0; background: white;">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Nombre del Convenio</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody id="mostrar_convenios">
        ';
    $consultaConvenios = "SELECT * FROM emisores_convenios WHERE id_emisor = " . $_SESSION['id_emisor'] . " ORDER BY nombre ASC";
    $resConvenios = mysqli_query($conexion, $consultaConvenios);
    while ($convenio = mysqli_fetch_array($resConvenios)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_social = str_replace($caracteres, $reemplazo, $convenio['nombre']);

        if ($convenio['estatus'] == 1) {
            $estado = "Activo";
            $titulo = "Desactivar cliente";
            $color = "btn-secondary";
            $desactive = "<i class='fas fa-ban'></i>";
            $codigo_estatus = 2;
            $disabled_edicion = "";
        } else {
            $estado = "Inactivo";
            $titulo = "Activar cliente";
            $color = "btn-success";
            $desactive = "<i class='fas fa-check'></i>";
            $codigo_estatus = 1;
            $disabled_edicion = "disabled";
        }

        if(intval($convenio['tipo']) == 1){
            $tipo = '<i class="fas fa-dollar-sign"></i>';
            $cantidad = '$'.$convenio['cost_consul'];
        } else {
            $tipo = '<i class="fas fa-percentage"></i>';
            $cantidad = $convenio['pct_consul'] . '%';
        }

        $html .= "
                        <tr>
                            <td class='text-center' style='position: sticky; left: 0; background: white;'>
                                <div class='btn-group' id='div-check" . $convenio['id_convenio'] . "'>
                                    <button type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_convenio(" . $convenio['id_convenio'] . ")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    &nbsp;
                                    <button type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_convenio(" . $convenio['id_convenio'] . "," . $codigo_estatus . ");'>
                                        " . $desactive . "
                                    </button>
                                </div>
                            </td>
                            <td class='text-center'>" . $convenio['id_convenio'] . "</td>
                            <td class='text-center'>" . $convenio['nombre'] . "</td>
                            <td class='text-center'>" . $tipo . "</td>
                            <td class='text-center'>" . $cantidad . "</td>
                            <td class='text-center'>" . $estado . "</td>
                        </tr>
                    ";
    }
    $html .= '
                </tbody>
            </table>
        ';

    echo $html;
}
?>
<script>
    $(function() {
        $('#tabla_cliente').DataTable({
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
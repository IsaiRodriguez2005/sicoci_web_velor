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
    $operacion = empty($_POST['operacion']) ? 1 : $_POST['operacion'];
    if ($operacion == 1) {
        $html = '
            <table class="table table-striped" id="tabla_convenios" width="100%">
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

        $html .= create_tr($resConvenios);

        $html .= '
                </tbody>
            </table>
            ';

        echo $html;
    } else {

        $consultaConvenios = "SELECT * FROM emisores_convenios WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_convenio = " . $_POST['id_convenio'] . ";";
        $resConvenios = mysqli_query($conexion, $consultaConvenios);

        return create_tr($resConvenios);
    }
}

function create_tr($res)
{
    $html = '';
    while ($convenio = mysqli_fetch_array($res)) {
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

        if (intval($convenio['tipo']) == 1) {
            $tipo = '<i class="fas fa-dollar-sign"></i>';
            $cantidad = '$' . $convenio['cost_consul'];
        } else {
            $tipo = '<i class="fas fa-percentage"></i>';
            $cantidad = $convenio['pct_consul'] . '%';
        }

        $html .= "
                        <tr id='tr_conve_" . $convenio['id_convenio'] . "'>
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

    return $html;
}
?>

<script>
    $(function() {
        $('#tabla_convenios').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "deferRender": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            }
        });
    });
</script>
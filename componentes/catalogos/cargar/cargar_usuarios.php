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
    $tipo = empty($_POST['tipo']) ? 1 : $_POST['tipo'];

    if ($tipo == 1) {
        $html = '
            <table class="table table-striped" id="tabla_usuarios" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center" style="position: sticky; left: 0; background: white;">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Nombre del Usuario</th>
                        <th class="text-center">Correo</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="mostrar_clientes">
        ';
        $consultaClientes = "SELECT * FROM usuarios WHERE id_emisor = " . $_SESSION['id_emisor'] . " ORDER BY nombre ASC";
        $resClientes = mysqli_query($conexion, $consultaClientes);

        $html .= create_tr($resClientes, $conexion);

        $html .= '
                </tbody>
            </table>
        ';

        echo $html;
    } else {

        $consultaClientes = "SELECT * FROM usuarios WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_usuario = " . $_POST['id_usuario'] . ";";
        $resClientes = mysqli_query($conexion, $consultaClientes);

        echo create_tr($resClientes, $conexion);
    }
}


function create_tr($res, $conexion)
{
    while ($clientes = mysqli_fetch_array($res)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_social = str_replace($caracteres, $reemplazo, $clientes['nombre']);

        if ($clientes['estatus'] == 1) {
            $estado = "Activo";
            $titulo = "Desactivar usuario";
            $color = "btn-secondary";
            $desactive = "<i class='fas fa-ban'></i>";
            $codigo_estatus = 2;
            $disabled_edicion = "";
        } else {
            $estado = "Inactivo";
            $titulo = "Activar usuario";
            $color = "btn-success";
            $desactive = "<i class='fas fa-check'></i>";
            $codigo_estatus = 1;
            $disabled_edicion = "disabled";
        }

        $consultaPermisos = "SELECT * FROM usuarios_permisos WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_usuario = " . $clientes['id_usuario'];
        $resultadoPermisos = mysqli_query($conexion, $consultaPermisos);
        $permisos = mysqli_fetch_array($resultadoPermisos);

        $html = "
                    <tr id='tr_usuario_" . $clientes['id_usuario'] . "'>
                        <td class='text-center' style='position: sticky; left: 0; background: white;'>
                            <div class='btn-group' id='div-check" . $clientes['id_usuario'] . "'>
                                <button type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_usuario(" . $clientes['id_usuario'] . ", &quot;" . $clientes['nombre'] . "&quot;, &quot;" . $clientes['correo'] . "&quot;, &quot;" . $clientes['password'] . "&quot;," . $permisos['configuraciones'] . "," . $permisos['agenda'] . "," . $permisos['clientes'] . "," . $permisos['usuarios'] . "," . $permisos['productos'] . "," . $permisos['proveedores'] . "," . $permisos['personal'] . "," . $permisos['tickets'] . "," . $permisos['facturacion'] . "," . $permisos['pago_proveedores'] . "," . $permisos['reportes'] . "," . $permisos['dash_directivo'] . ")'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                &nbsp;
                                <button type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_usuario(" . $clientes['id_usuario'] . "," . $codigo_estatus . ");'>
                                    " . $desactive . "
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>" . $clientes['id_usuario'] . "</td>
                        <td class='text-center'>" . $clientes['nombre'] . "</td>
                        <td class='text-center'>" . $clientes['correo'] . "</td>
                        <td class='text-center'>" . $estado . "</td>
                    </tr>
                ";
        return $html;
    }
}
?>

<script>
    $(function() {
        if (!$.fn.DataTable.isDataTable('#tabla_usuarios')) {
            $('#tabla_usuarios').DataTable({
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
        }
    });
</script>
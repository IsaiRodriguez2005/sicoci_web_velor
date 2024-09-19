<?php
session_start();
require("../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $html = '
            <table class="table table-striped" id="tabla_perfil_facturacion" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Perfil de Facturaci&oacute;n</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody id="mostrar_perfil_facturacion">
        ';
    $consultaClientes = "SELECT * FROM emisores_clientes_facturacion WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_cliente = " . $_POST['id_cliente'] . " ORDER BY nombre_social ASC";
    $resClientes = mysqli_query($conexion, $consultaClientes);
    while ($clientes = mysqli_fetch_array($resClientes)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_social = str_replace($caracteres, $reemplazo, $clientes['nombre_social']);

        if ($clientes['estatus'] == 1) {
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
        $html .= "
                        <tr>
                            <td class='text-center'>
                                <div class='btn-group' id='div-check" . $clientes['id_perfil'] . "'>
                                    <button type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_perfil(" . $clientes['id_cliente'] . ",
                                                                                                                                                                ". $clientes['id_perfil'] . ",
                                                                                                                                                                &quot;" . $clientes['rfc'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['nombre_social'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['calle'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['no_exterior'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['no_interior'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['codigo_postal'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['colonia'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['regimen_fiscal'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['uso_cfdi'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['metodo_pago'] . "&quot;,
                                                                                                                                                                &quot;" . $clientes['forma_pago'] . "&quot;
                                                                                                                                                                )'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    &nbsp;
                                    <button type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_perfil(". $clientes['id_cliente'] . "," . $clientes['id_perfil'] . "," . $codigo_estatus . ");'>
                                        " . $desactive . "
                                    </button>
                                    &nbsp;
                                    <button type='button' class='btn btn-danger btn-sm' " . $disabled_edicion . " title='Eliminar' onclick='eliminar_perfil(" . $clientes['id_perfil'] . ", ". $clientes['id_cliente'] . ")'>
                                        <i class='fas fa-trash-alt'></i>
                                    </button>
                                </div>
                            </td>
                            <td class='text-center'>" . $clientes['id_perfil'] . "</td>
                            <td class='text-center'>" . $clientes['nombre_social'] . "</td>
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
        $('#tabla_perfil_facturacion').DataTable({
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
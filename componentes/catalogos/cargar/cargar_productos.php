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
        //* si quiere cargar.
        $html = '
            <table class="table table-striped" id="tabla_productos_servicios" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center" style="position: sticky; left: 0; background: white;">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Nombre del Producto</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Stock minimo</th>
                        <th class="text-center">IVA</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="mostrar_proveedores">
        ';
        $consultaProducto = "SELECT * FROM productos_servicios WHERE id_emisor = " . $_SESSION['id_emisor'] . " ORDER BY nombre ASC";
        $resProducto = mysqli_query($conexion, $consultaProducto);

        $html .= create_tr($resProducto);

        $html .= '
                </tbody>
            </table>
        ';

        echo $html;
    } else {
        //* solo si actuaiza
        $consultaProducto = "SELECT * FROM productos_servicios WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_producto =" . $_POST['id_producto'] . ";";
        $resProducto = mysqli_query($conexion, $consultaProducto);

        echo create_tr($resProducto);
    }
}


function create_tr($res)
{
    $html = '';
    while ($producto = mysqli_fetch_array($res)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_comercial = str_replace($caracteres, $reemplazo, $producto['nombre']);

        if ($producto['estatus'] == 1) {
            $estado = "Activo";
            $titulo = "Desactivar";
            $color = "btn-secondary";
            $desactive = "<i class='fas fa-ban'></i>";
            $codigo_estatus = 2;
            $disabled_edicion = "";
        } else {
            $estado = "Inactivo";
            $titulo = "Activar";
            $color = "btn-success";
            $desactive = "<i class='fas fa-check'></i>";
            $codigo_estatus = 1;
            $disabled_edicion = "disabled";
        }

        // producto o servicio
        if ($producto['tipo'] == 1) {
            $tipo = 'SERVICIO';
        } else {
            $tipo = 'PRODUCTO';
        }


        // contenido de la tabla
        $html .= "
                    <tr id='tr_prod_" . $producto['id_producto'] . "'>
                        <td class='text-center' style='position: sticky; left: 0; background: white;'>
                            <div class='btn-group' id='div-check" . $producto['id_producto'] . "'>
                                <button type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_producto(" . $producto['id_producto'] . ",&quot;" . $producto['nombre'] . "&quot;, " . $producto['tipo'] . ", " . $producto['stock'] . ", " . $producto['stock_minimo'] . ", " . $producto['precio'] . ", " . $producto['iva'] . ")'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                &nbsp;
                                <button type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_producto(" . $producto['id_producto'] . "," . $codigo_estatus . ");'>
                                    " . $desactive . "
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>" . $producto['id_producto'] . "</td>
                        <td class='text-center'>" . $producto['nombre'] . "</td>
                        <td class='text-center'>" . $tipo . "</td>
                        <td class='text-center'>" . $producto['stock'] . "</td>
                        <td class='text-center'>" . $producto['stock_minimo'] . "</td>
                        <td class='text-center'>% " . $producto['iva'] . "</td>
                        <td class='text-center'>$ " . $producto['precio'] . "</td>
                        <td class='text-center'>" . $estado . "</td>
                    </tr>
                ";
    }

    return $html;
}
?>

<script>
    $(function() {
        $('#tabla_productos_servicios').DataTable({
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
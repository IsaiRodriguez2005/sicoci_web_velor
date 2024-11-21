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
            <table class="table table-striped" id="tabla_personal" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center" style="position: sticky; left: 0; background: white;">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Nombre del Personal</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Correo</th>
                        <th class="text-center">Telefono</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="mostrar_proveedores">
        ';
        $consultaPersonal = "SELECT * FROM emisores_personal WHERE id_emisor = " . $_SESSION['id_emisor'] . " ORDER BY nombre_personal ASC";
        $resPersonal = mysqli_query($conexion, $consultaPersonal);

        $html .= create_tr($resPersonal);

        $html .= '
                </tbody>
            </table>
        ';

        echo $html;
    } else {

        $consultaPersonal = "SELECT * FROM emisores_personal WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_personal=" . $_POST['id_personal'] . ";";
        $resPersonal = mysqli_query($conexion, $consultaPersonal);

        echo create_tr($resPersonal);
    }
}

function create_tr($res)
{
    $html = '';

    while ($personal = mysqli_fetch_array($res)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_comercial = str_replace($caracteres, $reemplazo, $personal['nombre_personal']);

        if ($personal['estatus'] == 1) {
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

        if ($personal['tipo'] == 1) {
            $tipo = 'Recepción';
        } else {
            $tipo = 'Terapeuta';
        }
        $html .= "
                        <tr id='tr_pers_" . $personal['id_personal'] . "'>
                            <td class='text-center' style='position: sticky; left: 0; background: white;'>
                                <div class='btn-group' id='div-check" . $personal['id_personal'] . "'>
                                    <button type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_personal(" . $personal['id_personal'] . ", &quot;" . $personal['nombre_personal'] . "&quot;, &quot;" . $personal['tipo'] . "&quot;, &quot;" . $personal['calle'] . "&quot;, &quot;" . $personal['no_exterior'] . "&quot;, &quot;" . $personal['no_interior'] . "&quot;, &quot;" . $personal['codigo_postal'] . "&quot;, &quot;" . $personal['colonia'] . "&quot;, &quot;" . $personal['municipio'] . "&quot;, &quot;" . $personal['estado'] . "&quot;, &quot;" . $personal['pais'] . "&quot;, &quot;" . $personal['correo'] . "&quot;, &quot;" . $personal['telefono'] . "&quot;)'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    &nbsp;
                                    <button type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_personal(" . $personal['id_personal'] . "," . $codigo_estatus . ");'>
                                        " . $desactive . "
                                    </button>
                                </div>
                            </td>
                            <td class='text-center'>" . $personal['id_personal'] . "</td>
                            <td class='text-center'>" . $personal['nombre_personal'] . "</td>
                            <td class='text-center'>" . $tipo . "</td>
                            <td class='text-center'>" . $personal['correo'] . "</td>
                            <td class='text-center'>" . $personal['telefono'] . "</td>
                            <td class='text-center'>" . $estado . "</td>
                        </tr>
                    ";
    }

    return $html;
}

?>
<script>
    $(function() {
        $('#tabla_personal').DataTable({
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
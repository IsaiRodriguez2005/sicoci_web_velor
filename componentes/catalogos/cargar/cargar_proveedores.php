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
            <table class="table table-striped" id="tabla_proveedores" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center" style="position: sticky; left: 0; background: white;">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Nombre del Proveedor</th>
                        <th class="text-center">Correo</th>
                        <th class="text-center">Telefono</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody id="mostrar_proveedores">
        ';
        $consultaProveedores = "SELECT * FROM emisores_proveedores WHERE id_emisor = " . $_SESSION['id_emisor'] . " ORDER BY nombre_comercial ASC";
        $resProveedores = mysqli_query($conexion, $consultaProveedores);
        
        $html .= create_tr($resProveedores);

        $html .= '
                </tbody>
            </table>
        ';

        echo $html;
    } else {

        $consultaProveedores = "SELECT * FROM emisores_proveedores WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_proveedor = ".$_POST['id_proveedor'].";";
        $resProveedores = mysqli_query($conexion, $consultaProveedores);
        
        echo create_tr($resProveedores);
    }
}

function create_tr($res){

    $html = '';

    while ($proveedores = mysqli_fetch_array($res)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_comercial = str_replace($caracteres, $reemplazo, $proveedores['nombre_comercial']);

        if ($proveedores['estatus'] == 1) {
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
        /*
                if($proveedores['tipo_cliente'] == 1)
                {
                    $tipo = "NO FISCAL";
                }
                else
                {
                    $tipo = "FISCAL";
                }
                    */
        $html .= "
                    <tr id='tr_prov_".$proveedores['id_proveedor']."'>
                        <td class='text-center' style='position: sticky; left: 0; background: white;'>
                            <div class='btn-group' id='div-check" . $proveedores['id_proveedor'] . "'>
                                <button type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_proveedor(" . $proveedores['id_proveedor'] . ", &quot;" . $proveedores['nombre_comercial'] . "&quot;, &quot;" . $proveedores['calle'] . "&quot;, &quot;" . $proveedores['no_exterior'] . "&quot;, &quot;" . $proveedores['no_interior'] . "&quot;, &quot;" . $proveedores['codigo_postal'] . "&quot;, &quot;" . $proveedores['colonia'] . "&quot;, &quot;" . $proveedores['municipio'] . "&quot;, &quot;" . $proveedores['estado'] . "&quot;, &quot;" . $proveedores['pais'] . "&quot;, &quot;" . $proveedores['regimen_fiscal'] . "&quot;, &quot;" . $proveedores['correo'] . "&quot;, " . $proveedores['telefono'] . ")'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                &nbsp;
                                <button type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_proveedor(" . $proveedores['id_proveedor'] . "," . $codigo_estatus . ");'>
                                    " . $desactive . "
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>" . $proveedores['id_proveedor'] . "</td>
                        <td class='text-center'>" . $proveedores['nombre_comercial'] . "</td>
                        <td class='text-center'>" . $proveedores['correo'] . "</td>
                        <td class='text-center'>" . $proveedores['telefono'] . "</td>
                        <td class='text-center'>" . $estado . "</td>
                    </tr>
                ";
    }
    return $html;
}

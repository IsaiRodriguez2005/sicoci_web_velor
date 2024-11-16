<?php
session_start();
require("../../conexion.php");
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
    $id_cliente = $_POST['id_cliente'];
    $estatus = $_POST['estatus'];

    $consultaClientes = "SELECT * FROM emisores_clientes WHERE id_cliente = $id_cliente AND id_emisor = " . $_SESSION['id_emisor'] . " ;";
    $resClientes = mysqli_query($conexion, $consultaClientes);
    while ($clientes = mysqli_fetch_array($resClientes)) {
        $reemplazo = array("&", '"', "'");
        $caracteres = array("&amp;", "&quot;", "&apos;");
        $nuevo_social = str_replace($caracteres, $reemplazo, $clientes['nombre_cliente']);

        if ($clientes['estatus'] == 1) {
            $estado = '<span class="badge badge-success" style="width: 100%; color:white;">ACTIVO</span>';
            $titulo = "Desactivar cliente";
            $color = "btn-secondary";
            $desactive = "<i class='fas fa-ban'></i>";
            $codigo_estatus = 2;
            $disabled_edicion = "";
        } else {
            $estado = '<span class="badge badge-secondary" style="width: 100%; color:white;">INACTIVO</span>';
            $titulo = "Activar cliente";
            $color = "btn-success";
            $desactive = "<i class='fas fa-check'></i>";
            $codigo_estatus = 1;
            $disabled_edicion = "disabled";
        }
        $html .= "
                <tr id='tr_cli_".$clientes['id_cliente']."'>
                    <td class='text-center'>
                        <div class='btn-group' id='div-check" . $clientes['id_cliente'] . "'>
                            <button id='btn_per_" . $clientes['id_cliente'] . "' type='button' class='btn btn-primary btn-sm' " . $disabled_edicion . " title='Perfiles de facturaci&oacute;n' onclick='cargar_perfil(" . $clientes['id_cliente'] . ", &quot;" . $clientes['nombre_cliente'] . "&quot;)'>
                                <i class='fas fa-dollar-sign'></i>
                            </button>
                            &nbsp;
                            <button id='btn_edit_" . $clientes['id_cliente'] . "' type='button' class='btn btn-warning btn-sm' " . $disabled_edicion . " title='Editar registro' onclick='editar_cliente(" . $clientes['id_cliente'] . ", &quot;" . $clientes['nombre_cliente'] . "&quot;, &quot;" . $clientes['correo'] . "&quot;, &quot;" . $clientes['telefono'] . "&quot;, &quot;" . $clientes['fec_nac'] . "&quot;, " . $clientes['ocupacion'] . " , " . $clientes['est_civ'] . ")'>
                                <i class='fas fa-edit'></i>
                            </button>
                            &nbsp;
                            <button id='btn_est_" . $clientes['id_cliente'] . "' type='button' class='btn " . $color . " btn-sm' title='" . $titulo . "' onclick='actualizar_estatus_cliente(" . $clientes['id_cliente'] . "," . $codigo_estatus . ");'>
                                " . $desactive . "
                            </button>
                        </div>
                    </td>
                    <td class='text-center'>" . $clientes['id_cliente'] . "</td>
                    <td class='text-center'>" . $clientes['nombre_cliente'] . "</td>
                    <td class='text-center' id='td_est_" . $clientes['id_cliente'] . "'>" . $estado . "</td>
                </tr>
            ";
    }

    echo $html;
}

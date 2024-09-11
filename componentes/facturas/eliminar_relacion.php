<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
        $sql_eliminar = "DELETE FROM emisores_cfdi_relacionados WHERE id_partida = ".$_POST['id_partida']." AND id_emisor = ".$_SESSION['id_emisor'];
        $resultado_eliminar = mysqli_query($conexion, $sql_eliminar);
        if($resultado_eliminar)
        {
            $consultaRelacionUUID = "SELECT cfdi.id_partida, cfdi.uuid_relacionado, r.descripcion FROM emisores_cfdi_relacionados cfdi INNER JOIN _cat_sat_tipo_relacion r ON r.clave_relacion = cfdi.clave_relacion WHERE cfdi.id_emisor = ".$_SESSION['id_emisor']." AND cfdi.id_documento = ".$_POST['id_documento']." AND cfdi.folio = ".$_POST['folio_factura']." AND cfdi.tipo_documento = 'F'";
            $resultadoRelacionUUID = mysqli_query($conexion, $consultaRelacionUUID);
            while($relacionUUID = mysqli_fetch_array($resultadoRelacionUUID))
            {
                $uuid_relacionados .= "
                    <tr>
                        <td class='text-center text-sm'>".$relacionUUID['uuid_relacionado']."</td>
                        <td class='text-center text-sm'>".$relacionUUID['descripcion']."</td>
                        <td class='text-center text-sm'>
                            <button type='button' class='btn btn-danger btn-sm' title='Eliminar relaci&oacute;n' onclick='eliminar_relacion(".$relacionUUID['id_partida'].",".$_POST['id_documento'].",".$_POST['folio_factura'].")'>
                                <i class='fas fa-trash'></i>
                            </button>
                        </td>
                    </tr>
                ";
            }
            echo $uuid_relacionados;
        }
        else
        {
            echo "error";
        }
    }
?>
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
        $sql = "UPDATE emisores_proveedores_cuentas SET estatus = ".$_POST['estatus']." WHERE id_proveedor=".$_POST['id_proveedor']." AND id_cuenta=".$_POST['id_cuenta']." AND id_emisor=".$_SESSION['id_emisor'];
        mysqli_query($conexion, $sql);


        $tabla = '
            <table class="table table-striped" id="cuentas_bancarias">
                <thead>
                    <tr>
                        <th class="sticky text-center">Acciones</th>
                        <th class="text-center">ID</th>
                        <th class="text-center">Cuenta</th>
                        <th class="text-center">RFC Banco</th>
                        <th class="text-center">Nombre Banco</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody>
        ';
        $sql = "SELECT id_cuenta, id_proveedor, rfc_banco, nombre_banco, cuenta, estatus FROM emisores_proveedores_cuentas WHERE id_emisor=".$_SESSION['id_emisor']." AND id_proveedor=".$_POST['id_proveedor'];
        $res = mysqli_query($conexion, $sql);
        while($datos = mysqli_fetch_array($res))
        {
            if($datos['estatus'] == 1)
            {
                $estado = "Activo";
                $titulo = "Desactivar cuenta";
                $color = "btn-secondary";
                $desactive = "<i class='fas fa-times-circle'></i>";
                $codigo_estatus = 2;
            }else{
                $estado = "Inactivo";
                $titulo = "Activar cuenta";
                $color = "btn-success";
                $desactive = "<i class='fas fa-check-circle'></i>";
                $codigo_estatus = 1;
            }
            $tabla.="
                    <tr>
                        <td class='text-center'>
                            <div class='btn-group'>
                                <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_cuenta_proveedor(".$datos['id_cuenta'].",".$datos['id_proveedor'].",".$codigo_estatus.");'>
                                    ".$desactive."
                                </button> &nbsp;
                                <button type='button' class='btn btn-danger btn-sm' title='Eliminar cuenta' onclick='eliminar_cuenta_proveedor(".$datos['id_cuenta'].",".$datos['id_proveedor'].")'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>".$datos['id_proveedor']."</td>
                        <td class='text-center'>".$datos['cuenta']."</td>
                        <td class='text-center'>".$datos['rfc_banco']."</td>
                        <td class='text-center'>".$datos['nombre_banco']."</td>
                        <td class='text-center'>".$estado."</td>
                    </tr>
            ";
        }

        $tabla.='
                </tbody>
            </table>
        ';

        echo $tabla;
    }
?>

<script>
  $(function () {
    $('#cuentas_bancarias').DataTable({
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
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
        $sqlMax = "SELECT COALESCE(MAX(id_cuenta),0) AS no_registro FROM emisores_cuentas WHERE id_emisor=".$_SESSION['id_emisor'];
        $resMax = mysqli_query($conexion, $sqlMax);
        $ultimo = mysqli_fetch_array($resMax);

        $nuevo_id = $ultimo['no_registro'] + 1;

        $sqlInsert = "INSERT INTO emisores_cuentas VALUES(".$nuevo_id.",".$_SESSION['id_emisor'].",'".$_POST['rfc_banco']."','".$_POST['nombre_banco']."','".$_POST['cuenta']."',1)";
        mysqli_query($conexion, $sqlInsert);


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
        $sql = "SELECT id_cuenta, rfc_banco, nombre_banco, cuenta, estatus FROM emisores_cuentas WHERE id_emisor=".$_SESSION['id_emisor'];
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
                                <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_cuenta(".$datos['id_cuenta'].",".$codigo_estatus.");'>
                                    ".$desactive."
                                </button> &nbsp;
                                <button type='button' class='btn btn-danger btn-sm' title='Eliminar cuenta' onclick='eliminar_cuenta(".$datos['id_cuenta'].")'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>".$datos['id_cuenta']."</td>
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
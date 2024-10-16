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
        $tabla = '
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Modulos relacionados al emisor</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped" id="tabla_modulos">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Acciones</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Modulo</th>
                                        <th class="text-center">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
        ';
        $sqlModulos = "SELECT m.id_partida, c.nombre_modulo, m.id_emisor, m.estatus  FROM emisores_modulos m INNER JOIN _cat_erp_modulos c ON c.id_modulo = m.id_modulo WHERE m.id_emisor=".$_POST['id_emisor'];
        $resModulos = mysqli_query($conexion, $sqlModulos);
        while($modulos = mysqli_fetch_array($resModulos))
        {
            if($modulos['estatus'] == 1)
            {
                $estado = "Activo";
                $titulo = "Desactivar modulo";
                $color = "btn-secondary";
                $desactive = "<i class='fas fa-times-circle'></i>";
                $codigo_estatus = 2;
            }else{
                $estado = "Inactivo";
                $titulo = "Activar modulo";
                $color = "btn-success";
                $desactive = "<i class='fas fa-check-circle'></i>";
                $codigo_estatus = 1;
            }
            $tabla.="
                                    <tr>
                                        <td class='text-center'>
                                            <div class='btn-group'>
                                                <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_modulo(".$modulos['id_partida'].",".$modulos['id_emisor'].",".$codigo_estatus.");'>
                                                    ".$desactive."
                                                </button>
                                            </div>
                                        </td>
                                        <td class='text-center'>".$modulos['id_partida']."</td>
                                        <td class='text-center'>".$modulos['nombre_modulo']."</td>
                                        <td class='text-center'>".$estado."</td>
                                    </tr>
            ";
        }

        $tabla.='
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        ';

        echo $tabla;
    }
?>

<script>
  $(function () {
    $('#tabla_modulos').DataTable({
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
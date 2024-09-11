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
                    <h3 class="card-title">Historial de timbres</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped" id="tabla_timbres">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Partida</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="sticky text-center">Operaci&oacute;n</th>
                                    </tr>
                                </thead>
                                <tbody>
        ';
        $sqlTimbres = "SELECT * FROM timbres WHERE id_emisor=".$_POST['id_emisor'];
        $resTimbres = mysqli_query($conexion, $sqlTimbres);
        while($timbres = mysqli_fetch_array($resTimbres))
        {
            if($timbres['operacion']==1)
            {
                $operacion = "Abono";
            }
            else
            {
                $operacion = "Descuento";
            }
            $tabla.="
                                    <tr>
                                        <td class='text-center'>".$timbres['id_partida']."</td>
                                        <td class='text-center'>".$timbres['cantidad']."</td>
                                        <td class='text-center'>".date("d/m/Y", strtotime($timbres['fecha']))."</td>
                                        <td class='text-center'>".$operacion."</td>
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
    $('#tabla_timbres').DataTable({
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
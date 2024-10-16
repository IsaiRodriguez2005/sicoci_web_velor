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
            <table class="table table-striped" id="tabla_stock">
                <thead>
                    <tr>
                        <th class="sticky text-center">Nombre Comercial</th>
                        <th class="text-center">Nombre Social</th>
                        <th class="text-center">Timbres</th>
                    </tr>
                </thead>
                <tbody>
        ';
        $sqlStock = "SELECT nombre_social, nombre_comercial, timbres FROM emisores WHERE timbres < 100 AND estatus = 1";
        $resStock = mysqli_query($conexion, $sqlStock);
        while($stock = mysqli_fetch_array($resStock))
        {
            $tabla.="
                    <tr>
                        <td class='text-center'>".$stock['nombre_social']."</td>
                        <td class='text-center'>".$stock['nombre_comercial']."</td>
                        <td class='text-center'>".$stock['timbres']."</td>
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
    $('#tabla_stock').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": false,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
  });
</script>
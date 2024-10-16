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
            <table class="table table-striped" id="tabla_inactivos">
                <thead>
                    <tr>
                        <th class="sticky text-center">Nombre Comercial</th>
                        <th class="text-center">Nombre Social</th>
                        <th class="text-center">Timbres</th>
                    </tr>
                </thead>
                <tbody>
        ';
        $sqlInactivos = "SELECT rfc, nombre_social, nombre_comercial FROM emisores WHERE estatus = 2";
        $resStock = mysqli_query($conexion, $sqlInactivos);
        while($inactivos = mysqli_fetch_array($resStock))
        {
            $tabla.="
                    <tr>
                        <td class='text-center'>".$inactivos['rfc']."</td>
                        <td class='text-center'>".$inactivos['nombre_social']."</td>
                        <td class='text-center'>".$inactivos['nombre_comercial']."</td>
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
    $('#tabla_inactivos').DataTable({
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
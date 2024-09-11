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
            <table class="table table-striped" id="productos">
                <thead>
                    <tr>
                        <th class="sticky text-center">Acciones</th>
                        <th class="text-center">Clave SAT</th>
                        <th class="text-center">Descripci&oacute;n SAT</th>
                    </tr>
                </thead>
                <tbody>
        ';
        if($_POST['tipo'] == 1)
        {
            $sql = "SELECT clave_producto, descripcion FROM _cat_sat_producto_servicio WHERE clave_producto LIKE '%".strtoupper($_POST['dato'])."%' AND estatus = 1 LIMIT 25";
        }
        else
        {
            $sql = "SELECT clave_producto, descripcion FROM _cat_sat_producto_servicio WHERE descripcion LIKE '%".strtoupper($_POST['dato'])."%' AND estatus = 1 LIMIT 25";
        }
        $res = mysqli_query($conexion, $sql);
        while($datos = mysqli_fetch_array($res))
        {
            $tabla.="
                    <tr>
                        <td class='text-center'>
                            <div class='btn-group'>
                                <button type='button' class='btn btn-primary btn-sm' title='Relacionar clave' onclick='relacionar_producto(&quot;".$datos['clave_producto']."&quot;)'>
                                    <i class='fas fa-download'></i>
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>".$datos['clave_producto']."</td>
                        <td class='text-center'>".$datos['descripcion']."</td>
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
    $('#productos').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
  });
</script>
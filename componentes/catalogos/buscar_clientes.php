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
        $html = '';
        if(true)
        {
            $sql = "SELECT id_cliente, nombre_cliente FROM emisores_clientes WHERE nombre_cliente = '".$_POST['nombre_social']."';";
        }
        $res = mysqli_query($conexion, $sql);
        $cliente = mysqli_fetch_array($res);

        echo $cliente['id_cliente'];
    }
?>


<?php
/*
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




$sql = "SELECT id_cliente, nombre_social FROM emisores_clientes WHERE nombre_social LIKE '%".strtoupper($_POST['cliente'])."%' LIMIT 0, 25;";
*/
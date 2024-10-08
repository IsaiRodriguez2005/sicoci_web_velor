<?php
    session_start();
    require("componentes/conexion.php");
    date_default_timezone_set('America/Mexico_City');
    if (!isset($_SESSION['nombre_usuario'])) 
    {
        session_destroy();
        header('location: index.html');
    }
    else
    {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Intranet Velor Innovation</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Fav icon -->
  <link rel="icon" type="image/icon" href="favicon.ico"/>
  <!-- Sweet Alerts-->
  <script src="js/sweetalert2@11.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php
    include("componentes/estructura/encabezado.php");
    include("componentes/estructura/menu.php");
  ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Inicio</li>
              <li class="breadcrumb-item active">Gesti&oacute;n de Documentos</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Formulario de registro -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Registro de nuevo documento</h3>
                    <input type="hidden" id="tipo_gestion" value="0">
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-file-invoice"></i> Tipo de documento</h6><hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-file"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Escpec&iacute;fica nombre del documento" id="documento" onfocus="resetear('documento')" maxlength="100">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-brain"></i></span>
                                </div>
                                <select class="form-control" id="modulo" onfocus="resetear('modulo')">
                                    <option value="0">Escoge modulo a relacionar</option>
                                    <?php
                                        $consultaModulo = "SELECT id_modulo, nombre_modulo FROM _cat_erp_modulos WHERE estatus = 1 ORDER BY nombre_modulo ASC";
                                        $resultadoMdulo = mysqli_query($conexion, $consultaModulo);
                                        while($modulo = mysqli_fetch_array($resultadoMdulo))
                                        {
                                            echo "<option value='".$modulo['id_modulo']."'>".$modulo['nombre_modulo']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <center><button type="button" class="btn btn-info" onclick="gestionar_documentos();" id="btn_emisor">Guardar Documento</button></center>
                </div>
            </div>
            <br>
            <!-- Tabla que muestra los usuarios registrados -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Catalogo de Documentos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped" id="tabla_documentos">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Acciones</th>
                                        <th class="sticky text-center">ID</th>
                                        <th class="text-center">Nombre de documento</th>
                                        <th class="text-center">Modulo</th>
                                        <th class="text-center">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT d.id_documento, d.nombre_documento, d.id_modulo, d.estatus, m.nombre_modulo FROM _cat_erp_documentos d INNER JOIN _cat_erp_modulos m ON m.id_modulo = d.id_modulo ORDER BY nombre_documento ASC";
                                        $resultado = mysqli_query($conexion, $sql);
                                        while($doctos = mysqli_fetch_array($resultado))
                                        {
                                            if($doctos['estatus'] == 1)
                                            {
                                                $estado = "Activo";
                                                $titulo = "Desactivar documento";
                                                $color = "btn-secondary";
                                                $desactive = "<i class='fas fa-times-circle'></i>";
                                                $codigo_estatus = 2;
                                            }else{
                                                $estado = "Inactivo";
                                                $titulo = "Activar documento";
                                                $color = "btn-success";
                                                $desactive = "<i class='fas fa-check-circle'></i>";
                                                $codigo_estatus = 1;
                                            }
                                            echo "
                                                <tr>
                                                    <td class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-sm btn-warning' title='Editar registro' onclick='editar_documento(".$doctos['id_documento'].", &quot;".$doctos['nombre_documento']."&quot;,".$doctos['id_modulo'].")'>
                                                                <i class='fas fa-edit'></i>
                                                            </button>
                                                            &nbsp;
                                                            <button type='button' class='btn btn-sm ".$color."' title='".$titulo."' onclick='actualizar_estatus_docto(".$doctos['id_documento'].",".$codigo_estatus.");'>
                                                                ".$desactive."
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class='text-center'>".$doctos['id_documento']."</td>
                                                    <td class='text-center'>".$doctos['nombre_documento']."</td>
                                                    <td class='text-center'>".$doctos['nombre_modulo']."</td>
                                                    <td class='text-center'>".$estado."</td>
                                                </tr>
                                            ";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <?php
    include("componentes/estructura/pie.php");
  ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- Funciones JS Personalizadas -->
<script src="js/peticiones_documentos.js"></script>
<script src="js/peticiones_generales.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $('#tabla_documentos').DataTable({
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
</body>
</html>
<?php
    }
?>
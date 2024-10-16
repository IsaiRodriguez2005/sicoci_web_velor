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
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
              <li class="breadcrumb-item active">Gesti&oacute;n de Emisores</li>
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
                    <h3 class="card-title" id="leyenda">Registro de nuevo emisor</h3>
                    <input type="hidden" id="tipo_gestion" value="0">
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6><hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="RFC" id="rfc" onfocus="resetear('rfc')" maxlength="13">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Raz&oacute;n social" id="nombre_social" onfocus="resetear('nombre_social')" maxlength="100">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Nombre comercial" id="nombre_comercial" onfocus="resetear('nombre_comercial')" maxlength="100">
                            </div>
                        </div>
                    </div>
                    <br><h6><i class="fas fa-map-marked-alt"></i> Datos del domicilio</h6><hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-road"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Calle" id="calle" onfocus="resetear('calle')" maxlength="35">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="N&uacute;mero exterior" id="no_exterior" onfocus="resetear('no_exterior')" maxlength="30">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="N&uacute;mero interior" id="no_interior" onfocus="resetear('no_interior')" maxlength="30">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-street-view"></i></span>
                                </div>
                                <input type="number" class="form-control" placeholder="C&oacute;digo postal" id="codigo_postal" onfocus="resetear('codigo_postal')" onKeyup="buscar_cp()" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" min="0">
                            </div>
                        </div>
                        <div class="col-4">
                            <input type="hidden" id="colonia_oculta" value="1">
                            <div class="input-group mb-3" id="dato_colonia">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="colonia" onfocus="resetear('colonia')" disabled>
                                    <option value="0">Colonia</option>
                                </select>
                                &nbsp;
                                <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="estado" onfocus="resetear('estado')" disabled>
                                    <option value="0">Estado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="municipio" onfocus="resetear('municipio')" disabled>
                                    <option value="0">Municipio</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="pais" onfocus="resetear('pais')" disabled>
                                    <option value="0">Pais</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br><h6><i class="fas fa-dollar-sign"></i> Actividad econ&oacute;mica</h6><hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <select class="form-control" id="regimen" onfocus="resetear('regimen')">
                                    <option value="0">Regimen fiscal</option>
                                    <?php
                                        $consultaRegimenes = "SELECT clave_regimen, descripcion FROM _cat_sat_regimen_fiscal WHERE estatus = 1 ORDER BY descripcion ASC";
                                        $resultadoRegimenes = mysqli_query($conexion, $consultaRegimenes);
                                        while($regimenes = mysqli_fetch_array($resultadoRegimenes))
                                        {
                                            echo "<option value='".$regimenes['clave_regimen']."'>[".$regimenes['clave_regimen']."] ".$regimenes['descripcion']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br><h6><i class="fas fa-info"></i> Datos complementarios</h6><hr>
                    <div class="row">
                        <div class="col-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Tel&eacute;fono(s)" id="telefono" onfocus="resetear('telefono')" maxlength="50">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Correo(s) electr&oacute;nico(s)" id="correo" onfocus="resetear('correo')" maxlength="100">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Sitio web" id="sitio_web" onfocus="resetear('sitio_web')" maxlength="100">
                            </div>
                        </div>
                    </div><br>
                    <center><button type="button" class="btn btn-info" onclick="gestionar_emisor();" id="btn_emisor">Guardar Emisor</button></center><br>
                </div>
            </div>

            <!-- Tabla que muestra los usuarios registrados -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Catalogo de Emisores</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped" id="tabla_emisores">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Acciones</th>
                                        <th class="sticky text-center">ID</th>
                                        <th class="text-center">Raz&oacute;n Social</th>
                                        <th class="text-center">Nombre Comercial</th>
                                        <th class="text-center">Timbres</th>
                                        <th class="text-center">Alta</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $consultaEmisores = "SELECT * FROM emisores ORDER BY nombre_social ASC";
                                        $resEmisores = mysqli_query($conexion, $consultaEmisores);
                                        while($emisores = mysqli_fetch_array($resEmisores))
                                        {
                                            if($emisores['estatus'] == 1)
                                            {
                                                $estado = "Activo";
                                                $titulo = "Desactivar emisor";
                                                $color = "btn-secondary";
                                                $desactive = "<i class='fas fa-user-slash'></i>";
                                                $codigo_estatus = 2;
                                            }else{
                                                $estado = "Inactivo";
                                                $titulo = "Activar emisor";
                                                $color = "btn-success";
                                                $desactive = "<i class='fas fa-check'></i>";
                                                $codigo_estatus = 1;
                                            }
                                            echo "
                                                <tr>
                                                    <td class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-warning btn-sm' title='Editar registro' onclick='editar_emisor(".$emisores['id_emisor'].", &quot;".$emisores['rfc']."&quot;, &quot;".$emisores['nombre_social']."&quot;, &quot;".$emisores['nombre_comercial']."&quot;, &quot;".$emisores['calle']."&quot;, &quot;".$emisores['exterior']."&quot;, &quot;".$emisores['interior']."&quot;, ".$emisores['codigo_postal'].", &quot;".$emisores['clave_colonia']."&quot;, &quot;".$emisores['clave_regimen']."&quot;, &quot;".$emisores['telefono']."&quot;, &quot;".$emisores['correo']."&quot;, &quot;".$emisores['sitio_web']."&quot;)'>
                                                                <i class='fas fa-edit'></i>
                                                            </button>
                                                            &nbsp;
                                                            <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_emisor(".$emisores['id_emisor'].",".$codigo_estatus.");'>
                                                                ".$desactive."
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class='text-center'>".$emisores['id_emisor']."</td>
                                                    <td class='text-center'>".$emisores['nombre_social']."</td>
                                                    <td class='text-center'>".$emisores['nombre_comercial']."</td>
                                                    <td class='text-center'>".$emisores['timbres']."</td>
                                                    <td class='text-center'>".$emisores['fecha_alta']."</td>
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
<script src="js/peticiones_emisores.js"></script>
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
    $('#tabla_emisores').DataTable({
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
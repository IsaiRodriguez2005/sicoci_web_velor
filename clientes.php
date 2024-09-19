<?php
session_start();
require("componentes/conexion.php");
date_default_timezone_set('America/Mexico_City');
if (!isset($_SESSION['nombre_usuario'])) {
    session_destroy();
    header('location: index.html');
} else {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php include("componentes/estructura/title.php"); ?></title>

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
        <link rel="icon" type="image/icon" href="favicon.ico" />
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
                                    <li class="breadcrumb-item active">Gesti&oacute;n de Clientes</li>
                                </ol>
                                <button type="button" class="btn btn-danger" onclick="ver_catalogo();">Ver Clientes/Pacientes</button>
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
                                <h3 class="card-title" id="leyenda">Registro de nuevo cliente</h3>
                                <input type="hidden" id="tipo_gestion" value="0">
                            </div>
                            <div class="card-body">
                                <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Nombre de la persona" id="nombre_social" onfocus="resetear('nombre_social')" maxlength="150">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Correo electr&oacute;nico" id="correo" onfocus="resetear('correo')" maxlength="150">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="N&uacute;mero telef&oacute;nico" id="telefono" onfocus="resetear('telefono')" maxlength="10" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-success" onclick="gestionar_cliente();">Guardar Cliente</button>
                                    </div>
                                </div>
                                <br>

                                <!-- INICIA MODAL MODAL PARA EDITAR CLIENTE -->
                                <div class="modal fade" id="modal_clientes" role="dialog" style="overflow: scroll;">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title text-success">Clientes/Pacientes Registrados</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12" id="vista_clientes"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Perfiles Facturacion -->
                                <div class="modal fade" id="modal_perfiles" role="dialog" style="overflow: scroll;">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title text-success">Registrar Perfil de Facturaci&oacute;n</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" id="perfil_id_cliente" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" id="perfil_nombre_cliente" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" placeholder="RFC" id="rfc" onfocus="resetear('rfc')" maxlength="13">
                                                        </div>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control" placeholder="Nombre de la persona" id="nombre_perfil" onfocus="resetear('nombre_social')" maxlength="150">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card card-dark card-tabs">
                                                            <div class="card-header p-0 pt-1">
                                                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false"><i class="fas fa-map-marked"></i> &nbsp;Domicilio</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false"><i class="fas fa-dollar-sign"></i> &nbsp;Actividad econ&oacute;mica</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                                                    <div class="tab-pane fade show active" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                                                        <div class="row">
                                                                            <div class="col-4">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-road"></i></span>
                                                                                    </div>
                                                                                    <input type="text" class="form-control" placeholder="Calle" id="calle" onfocus="resetear('calle')" maxlength="50">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                                                    </div>
                                                                                    <input type="text" class="form-control" placeholder="N&uacute;mero exterior" id="no_exterior" onfocus="resetear('no_exterior')" maxlength="50">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                                                    </div>
                                                                                    <input type="text" class="form-control" placeholder="N&uacute;mero interior" id="no_interior" onfocus="resetear('no_interior')" maxlength="50">
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
                                                                                    <button type="button" class="btn btn-info" id="colonia_texto" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="input-group mb-3" id="dato_estado">
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
                                                                                <div class="input-group mb-3" id="dato_municipio">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                                                    </div>
                                                                                    <select class="form-control" id="municipio" onfocus="resetear('municipio')" disabled>
                                                                                        <option value="0">Municipio</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="input-group mb-3" id="dato_pais">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                                                    </div>
                                                                                    <select class="form-control" id="pais" onfocus="resetear('pais')" disabled>
                                                                                        <option value="0">Pais</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                                                    </div>
                                                                                    <select class="form-control" id="regimen" onfocus="resetear('regimen')">
                                                                                        <option value="0">Regimen fiscal</option>
                                                                                        <?php
                                                                                        $consultaRegimenes = "SELECT clave_regimen, descripcion FROM _cat_sat_regimen_fiscal WHERE fisica = 1 AND estatus = 1 ORDER BY descripcion ASC";
                                                                                        $resultadoRegimenes = mysqli_query($conexion, $consultaRegimenes);
                                                                                        while ($regimenes = mysqli_fetch_array($resultadoRegimenes)) {
                                                                                            echo "<option value='" . $regimenes['clave_regimen'] . "'>[" . $regimenes['clave_regimen'] . "] " . $regimenes['descripcion'] . "</option>";
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></span>
                                                                                    </div>
                                                                                    <select class="form-control" id="uso_cfdi" onfocus="resetear('uso_cfdi')">
                                                                                        <option value="0">Uso CFDI</option>
                                                                                        <?php
                                                                                        $consultaUsos = "SELECT clave_uso, descripcion FROM _cat_sat_uso_cfdi WHERE fisica = 1 AND estatus = 1";
                                                                                        $resultadoUsos = mysqli_query($conexion, $consultaUsos);
                                                                                        while ($usos = mysqli_fetch_array($resultadoUsos)) {
                                                                                            echo "<option value='" . $usos['clave_uso'] . "'>[" . $usos['clave_uso'] . "] " . $usos['descripcion'] . "</option>";
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                                                                    </div>
                                                                                    <select class="form-control" id="metodo_pago" onfocus="resetear('metodo_pago')" onchange="activar_forma()">
                                                                                        <option value="0">Metodo de pago</option>
                                                                                        <?php
                                                                                        $consultaMetodo = "SELECT clave_metodo, descripcion FROM _cat_sat_metodo_pago WHERE estatus = 1 ORDER BY descripcion ASC";
                                                                                        $resultadoMetodo = mysqli_query($conexion, $consultaMetodo);
                                                                                        while ($metodo = mysqli_fetch_array($resultadoMetodo)) {
                                                                                            echo "<option value='" . $metodo['clave_metodo'] . "'>[" . $metodo['clave_metodo'] . "] " . $metodo['descripcion'] . "</option>";
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                                                                    </div>
                                                                                    <select class="form-control" id="forma_pago" onfocus="resetear('forma_pago')">
                                                                                        <option value="0">Forma de pago</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <center><button type="button" class="btn btn-success" onclick="agregar_perfil();">Guardar Perfil</button></center>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <h6><i class="fas fa-id-card"></i> Perfil de Facturaci&oacute;n</h6>
                                                        <hr>
                                                        <div class="col-12" id="vista_perfil_facturacion">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
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
        <script src="js/peticiones_clientes.js"></script>
        <script src="js/peticiones_generales.js"></script>
        <!-- bs-custom-file-input -->
        <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
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
            $(function() {
                $('#tabla_cliente').DataTable({
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
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
                                    <li class="breadcrumb-item active">Gesti&oacute;n de Usuarios</li>
                                </ol>
                                <button type="button" class="btn btn-danger" onclick="ver_catalogo();">Ver Usuarios</button>
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
                                <h3 class="card-title" id="leyenda">Registro de nuevo usuario</h3>
                                <input type="hidden" id="tipo_gestion" value="0">
                                <input type="hidden" id="id_usuario" value="0">
                            </div>
                            <div class="card-body">
                                <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Nombre del usuario" id="nombre" onfocus="resetear('nombre')" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" class="form-control" placeholder="Correo electr&oacute;nico" id="correo" onfocus="resetear('correo')" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-unlock"></i></span>
                                            </div>
                                            <input type="password" class="form-control" placeholder="Contrase&ntilde;a" id="password" onfocus="resetear('password')" maxlength="10">
                                            &nbsp;<button type="button" class="btn btn-info" onclick="generar_password();"><i class="fas fa-fingerprint"></i></button>
                                        </div>
                                    </div>
                                </div><br>
                                <h6><i class="fas fa-id-card"></i> Ligar Personal</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <select class="form-control" id="id_terapeuta" placeholder="Nombre del terapeuta" onfocus="resetear('id_terapeuta')" onchange="getDatosTerapeuta()">
                                                <option value="">Terapeuta Ligado</option>
                                                <?php
                                                $consTerapeutas = "SELECT id_personal, nombre_personal FROM emisores_personal WHERE tipo = 2";
                                                $resultado = mysqli_query($conexion, $consTerapeutas);


                                                while ($filas = mysqli_fetch_array($resultado)) {
                                                    echo "<option value='" . $filas['id_personal'] . "'>" . $filas['nombre_personal'] . "</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h6><i class="fas fa-lock"></i> Privilegios de acceso</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="configuraciones">
                                                <label for="configuraciones">Configuraciones</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="agenda">
                                                <label for="agenda">Agenda</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="clientes">
                                                <label for="clientes">Clientes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="usuarios">
                                                <label for="usuarios">Usuarios</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="productos">
                                                <label for="productos">Productos/Servicios</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="proveedores">
                                                <label for="proveedores">Proveedores</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="personal">
                                                <label for="personal">Personal</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="tickets">
                                                <label for="tickets">Tickets</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="facturacion">
                                                <label for="facturacion">Facturas</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="pago_proveedores">
                                                <label for="pago_proveedores">Pago proveedores</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="reportes">
                                                <label for="reportes">Reportes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group mb-3">
                                            <div class="icheck-success d-inline">
                                                <input type="checkbox" id="dash_directivo">
                                                <label for="dash_directivo">KPI Directivos</label>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <center><button type="button" class="btn btn-info" onclick="gestionar_usuario();" id="btn_emisor">Guardar Usuario</button></center>
                                <!-- INICIA MODAL MODAL PARA EDITAR CLIENTE -->
                                <div class="modal fade" id="modal_cargar" role="dialog" style="overflow: scroll;">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title text-success">Usuarios Registrados</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 table-responsive" id="vista"></div>
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
                        <br>
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
        <script src="js/peticiones_usuarios.js"></script>
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
            $(function() {
                $('#tabla_usuarios').DataTable({
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
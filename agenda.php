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
                                    <li class="breadcrumb-item active">Facturas</li>
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
                        <div id="div_factura">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title text-sm">Búsqueda de citas</h3>
                                    <input type="hidden" id="tipo_gestion" value="0">
                                    <input type="hidden" id="id_personal" value="<?php if (empty($_SESSION['id_personal'])) {
                                                                                        echo 0;
                                                                                    } else {
                                                                                        echo $_SESSION['id_personal'];
                                                                                    }  ?>">
                                </div>
                                <div class="card-body">
                                    <?php
                                    $hoy = date("Y-m-d");
                                    $pasado = date("Y-m-d", strtotime($hoy . " - 30 days"));
                                    $futuro = date("Y-m-d", strtotime($hoy . " + 30 days"));
                                    ?>
                                    <div class="row">
                                        <!-- Cliente -->
                                        <div class="col-md-9 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="hidden" id="id_cliente2" value="0">
                                                <input type="text" class="form-control" id="cliente" list="clientes" placeholder="Nombre del Cliente" autocomplete="off" onchange="buscar_cliente( this )" onfocus="actualizar_lista_clientes()" required>
                                                <datalist id="clientes">

                                                </datalist>
                                            </div>
                                        </div>

                                        <!-- Estatus
                                        <div class="col-md-4 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-question-circle"></i></span>
                                                </div>
                                                <select class="form-control" id="estatus" name="estatus" required>
                                                    <option value="" selected disabled>Selecciona Estado</option>
                                                    <option value="1">Aperturado</option>
                                                    <option value="2">Agendado</option>
                                                    <option value="3">Realizado</option>
                                                    <option value="4">Cancelado</option>
                                                </select>
                                            </div>
                                        </div>
                                        -->
                                    </div>

                                    <div class="row">
                                        <!-- Terapeuta 
                                        <div class="col-md-4 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <select class="form-control" id="terapeutas" name="terapeutas" required>
                                                    <option value="0" disabled selected>Selecciona Terapeuta</option>
                                                    <?php
                                                    $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 ORDER BY nombre_personal ASC";
                                                    $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
                                                    while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                                                        echo "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        -->
                                        <!-- Consultorio
                                        <div class="col-md-4 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <select class="form-control" id="consultorio" name="consultorio" required>
                                                    <option value="" selected disabled>Selecciona Consultorio</option>
                                                    <?php
                                                    $consultaConsultorio = "SELECT * FROM emisores_consultorios ORDER BY nombre ASC";
                                                    $resConsultoio = mysqli_query($conexion, $consultaConsultorio);
                                                    while ($consultorio = mysqli_fetch_array($resConsultoio)) {
                                                        echo "<option value='" . $consultorio['id_consultorio'] . "'>" . $consultorio['nombre'] . "</option>";
                                                    }
                                                    ?>
                                                </select>   
                                            </div>
                                        </div>
                                        -->
                                    </div>

                                    <div class="row">
                                        <!-- Fecha Inicial -->
                                        <div class="col-md-3 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" title="Ingresa fecha inicial de la búsqueda" id="fecha_inicial" value="<?php echo $pasado; ?>" onfocus="resetear('fecha_inicial')">
                                            </div>
                                        </div>
                                        <!-- Fecha Final -->
                                        <div class="col-md-3 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" title="Ingresa fecha final de la búsqueda" id="fecha_final" value="<?php echo $futuro; ?>" onfocus="resetear('fecha_final')">
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <!-- Botón Buscar -->
                                            <button type="button" class="btn btn-info" onclick="mostrar_historial_citas();">Buscar</button>
                                        </div>
                                    </div>

                                    <div class="row float-right" id="btn-nueva-cita">
                                        <!-- Botón Nueva Factura -->

                                    </div>
                                </div>
                            </div>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title text-sm">Historial de citas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div id="historial_citas" class="table-responsive"></div>
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

            <!--MODAL EDITAR-->
            <div class="modal fade" id="editar_factura" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="overflow-y: scroll;">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Factura # <span class="text-danger" id="e_ffactura"></span></h4>
                        </div>
                        <div class="modal-body">
                            <div id="ver_factura"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="validar_factura(1)">Guardar Proforma</button>
                            <button type="button" class="btn btn-success" onclick="validar_factura(2)">Timbrar Factura</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="ver_pdf_factura" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="overflow-y: scroll;">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Factura # <span class="text-danger" id="pdf_ffactura"></span></h4>
                        </div>
                        <div class="modal-body">
                            <embed id="ruta_pdf" frameborder="0" width="100%" height="600px"></embed>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>





        <!--Modales-->
        <?php
        require("componentes/modales/modales_agenda.php");
        ?>
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
        <script src="js/peticiones_agenda.js"></script>
        <script src="js/peticiones_generales.js"></script>
        <script src="js/peticiones_agenda_terapeuta.js"></script>
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
        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                mostrar_historial_citas();
            });
        </script>
        <script>
            $(function() {
                $('#tabla_facturas').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    // "autoWidth": true,
                    // "responsive": true,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    "order": [
                        [3, 'asc'],
                        [2, 'asc'],
                        [1, 'des'],
                    ],
                });
            });
        </script>
    </body>

    </html>
<?php
}
?>
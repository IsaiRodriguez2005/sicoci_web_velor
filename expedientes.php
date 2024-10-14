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
                                        <!-- Botón Buscar -->
                                        <div class="col-md-2 mb-3">
                                            <button type="button" class="btn btn-info" onclick="mostrar_expedientes_clientes();">Buscar</button>
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
                                        <!-- 
                                            <div class="col-md-3 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" title="Ingresa fecha inicial de la búsqueda" id="fecha_inicial" value="<?php echo $pasado; ?>" onfocus="resetear('fecha_inicial')">
                                                </div>
                                            </div>
                                        -->
                                        <!-- Fecha Final -->
                                        <!--
                                        <div class="col-md-3 mb-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" class="form-control" title="Ingresa fecha final de la búsqueda" id="fecha_final" value="<?php echo $futuro; ?>" onfocus="resetear('fecha_final')">
                                            </div>
                                        </div>
                                        -->
                                    </div>

                                    <div class="row float-right" id="btn-nueva-cita">
                                        <!-- Botón Nueva Factura -->

                                    </div>
                                </div>
                            </div>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title text-sm">Expediente de citas</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div id="expedientes_citas"></div>
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
            <!--Modales-->
            <!-- Modal Valoracion Cita -->
            <div class="modal fade" id="modal_valoracion" role="dialog" style="overflow: scroll;">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title text-danger">Valoracion de Cita</h4>
                            <input type="hidden" id="id_cliente_valoracion">
                            <input type="hidden" id="folio">
                        </div>
                        <div class="card-body">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="form-row">
                                        <!--
                                
                        -->
                                        <div class="col-md-8">
                                            <label for="nombre">Nombre:</label>
                                            <input type="text" class="form-control" id="nombre_valoracion" placeholder="Ingrese su nombre" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="apellidos">Fecha:</label>
                                            <input type="date" class="form-control" id="fecha" value="<?php echo $hoy ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-row pt-4">
                                        <!--
                                        <div class="col-md-3">
                                            <label>Sexo:</label>
                                            <div class="row d-flex justify-content-between">
                                                <div class="col">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="radio" name="sexo" id="masculino_valoracion" value="masculino">
                                                        <label class="form-check-label" for="masculino">Masculino</label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="radio" name="sexo" id="femenino_valoracion" value="femenino">
                                                        <label class="form-check-label" for="femenino">Femenino</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                                -->
                                        <div class="col-md-2">
                                            <label for="edad">Edad:</label>
                                            <input type="number" class="form-control" id="edad_valoracion" placeholder="Ingrese su edad" onfocus="resetear('edad_valoracion')">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="ocupacion">Ocupación:</label>
                                            <div class="d-flex">
                                                <select class="form-control" id="ocupacion_valoracion" onfocus="resetear('ocupacion_valoracion')">


                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="nombre">Telefono:</label>
                                            <input type="text" class="form-control" id="telefono_valoracion" placeholder="Telefono" maxlength="10" onfocus="resetear('telefono_valoracion')">
                                        </div>
                                    </div>

                                    <div class="form-row pt-4">
                                        <div class="col-md-6">
                                            <label for="apellidos">Estado Civil:</label>
                                            <select class="form-control" id="estado_civil_valoracion">
                                                <option value="" selected disabled>Estado Civil</option>
                                                <option value="1">Soltero/a</option>
                                                <option value="2">Casado/a</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="nombre">Toximanias:</label>
                                            <input type="text" class="form-control" id="toximanias_valoracion" placeholder="Toximanias" onfocus="resetear('toximanias_valoracion')">
                                        </div>
                                    </div>

                                    <div class="form-group pt-4">
                                        <label for="motivoConsulta">Motivo de Consulta:</label>
                                        <textarea class="form-control" id="motivo_consulta_valoracion" rows="3" placeholder="¿Cúal es el motivo?" onfocus="resetear('motivo_consulta_valoracion')"></textarea>
                                    </div>
                                    <div class="form-grop pt-3">
                                        <label for="nombre">Actividad Fisica:</label>
                                        <input type="text" class="form-control" id="act_fisica_valoracion" placeholder="Actividad fisica" onfocus="resetear('act_fisica_valoracion')">
                                    </div>
                                    <!--
                        <div class="form-row pt-4">
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="diabetes">
                                    <label class="form-check-label" for="diabetes">Diabetes</label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="hta">
                                    <label class="form-check-label" for="hta">HTA</label>
                                </div>
                            </div>

                            
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="cancer">
                                    <label class="form-check-label" for="cancer">Cáncer</label>
                                </div>
                            </div>

                            
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="enfermedades_reumaticas">
                                    <label class="form-check-label" for="enfermedades_reumaticas">Enf. Reumáticas</label>
                                </div>
                            </div>

                         
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="cardiopatias">
                                    <label class="form-check-label" for="cardiopatias">Cardiopatías</label>
                                </div>
                            </div>

                            <
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="cirugias">
                                    <label class="form-check-label" for="cirugias">Cirugías</label>
                                </div>
                            </div>

                           
                            <div class="col-md-2 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="alergias">
                                    <label class="form-check-label" for="alergias">Alergias</label>
                                </div>
                            </div>

                            
                            <div class="col-md-2 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="transfusiones">
                                    <label class="form-check-label" for="transfusiones">Transfusiones</label>
                                </div>
                            </div>

                            
                            <div class="col-md-2 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="fracturas">
                                    <label class="form-check-label" for="fracturas">Fracturas</label>
                                </div>
                            </div>

                            
                            <div class="col-md-2 pt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="otros">
                                    <label class="form-check-label" for="otros">Otros</label>
                                </div>
                            </div>
                        </div>
-->
                                    <style>
                                        .form-check-input {
                                            width: 15px;
                                            height: 15px;
                                        }
                                    </style>


                                    <div class="form-group pt-4">
                                        <label for="ta">Signos Vitales:</label>
                                        <div class="form-row">
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label for="ta" class="mr-2">TA:</label>
                                                <input type="text" class="form-control" id="tension_art" placeholder="Ingrese la tensión arterial">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label for="fc" class="mr-2">FC:</label>
                                                <input type="text" class="form-control" id="fc" placeholder="Ingrese la frecuencia cardíaca">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label for="fr" class="mr-2">FR:</label>
                                                <input type="text" class="form-control" id="fr" placeholder="Ingrese la frecuencia respiratoria">
                                            </div>
                                        </div>
                                        <div class="form-row pt-2">
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label for="satO2" class="mr-2">Sat. de O2:</label>
                                                <input type="text" class="form-control" id="satO2" placeholder="Ingrese la saturación de oxígeno">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label for="temp" class="mr-2">Temp:</label>
                                                <input type="text" class="form-control" id="temp" placeholder="Ingrese la temperatura">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label for="glucosa" class="mr-2">Glucosa:</label>
                                                <input type="text" class="form-control" id="glucosa" placeholder="Ingrese el nivel de glucosa">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="farmacos">Fármacos:</label>
                                        <textarea class="form-control" id="farmacos" rows="2" placeholder="Ejemplo: Diclofenaco inyectado" onfocus="resetear('farmacos')"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="diagnosticoMedico">Diagnóstico Médico:</label>
                                        <textarea class="form-control" id="diagnosticoMedico" rows="3" placeholder="Diagnostico"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="escalaDolor">Escala de Dolor EVA:</label>
                                        <div class="d-flex align-items-center">
                                            <input type="range" class="custom-range ml-2" min="0" max="10" id="escalaDolor" value="0" >
                                            <span id="escalaValor" class="ml-2">0</span> <!-- Elemento para mostrar el valor -->
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="cerrar_modal('modal_valoracion', '')">Cerrar</button><br><br>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Valoracion Cita -->
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
            <script src="js/peticiones_expedientes.js"></script>
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

    </body>

    </html>
<?php
}
?>
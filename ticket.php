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
        <link rel="icon" type="image/icon" href="favicon.ico" />
        <!-- Sweet Alerts-->
        <script src="js/sweetalert2@11.js"></script>

        <link rel="stylesheet" href="./css/estilos_search.css">
        <link rel="stylesheet" href="./css/estilos_table.css">
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <?php
            include("componentes/estructura/encabezado.php");
            include("componentes/estructura/menu.php");
            ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div class="container-fluid pr-3 pl-3">
                    <!-- ============================================================== -->
                    <!-- Bread crumb and right sidebar toggle -->
                    <!-- ============================================================== -->
                    <div class="row page-titles justify-content-between">
                        <!-- Breadcrumb y título a la izquierda -->
                        <div class="col-8 align-self-center mt-3">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><b>Ticket</b></li>
                                <!-- <li class="breadcrumb-item"><a href="index.php/pventa">Punto de venta</a></li> -->
                                <li class="breadcrumb-item" id="text_serie"></li>
                                <li class="breadcrumb-item" id="text_folio_ticket"></li>
                            </ol>
                        </div>

                        <!-- Botones a la derecha -->
                        <div class="col-4 align-self-center text-right pr-3">
                            <!-- Borrar pedido -->
                            <!-- <a href="#borrarcancelar"
                                class="btn btn-success ml-1 mt-2"
                                data-toggle="modal"
                                title="Borrar ticket">
                                <i class="fas fa-trash-alt"></i>
                            </a> -->
                        </div>
                    </div>

                    <!-- Modal cambiar cliente-->
                    <div id="cambiarCliente" class="modal fade top20" role="dialog" aria-labelledby="cambiarCliente" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Cambiar cliente</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                                </div>
                                <form method="post" action="index.php/pventa/cambiaCliente/15864">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="recipient-name-tarjeta" class="form-label">Cliente:</label>
                                            <select class="select3 form-control select2-hidden-accessible" style="width: 100%;" name="cliente" id="sclientes" tabindex="-1" aria-hidden="true">
                                                <option value="15864">Ab Abasolo </option>
                                                <option value="1208"> CARNICERíA LA VAQUILLA</option>
                                            </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-sclientes-container"><span class="select2-selection__rendered" id="select2-sclientes-container" title="Ab Abasolo ">Ab Abasolo </span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name-tarjeta" class="form-label">Cambia precio:</label>
                                            <select class="select3 form-control select2-hidden-accessible" style="width: 100%;" name="cambiaprecio" tabindex="-1" aria-hidden="true">
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-cambiaprecio-42-container"><span class="select2-selection__rendered" id="select2-cambiaprecio-42-container" title="Si">Si</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">Cambiar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <!-- ============================================================== -->
                    <!-- End Bread crumb and right sidebar toggle -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Start Page Content -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <!-- <div class="col-12" id="bcodigo">
                            <div class="card">
                                <div class="card-body p-b-0">
                                    <h4 class="card-title p-b-10">Agregar producto por código
                                        <a style="color:#fff" class="btn pull-right btn-success btnbuscart" data-toggle="tooltip" title="" data-original-title="Buscar por nombre" target="_blank"><i class="mdi mdi-account-search"></i> [f4]
                                        </a>
                                    </h4>
                                    <hr>
                                    <form method="post" action="index.php/pventa/addProducto/15864" class="row" id="addProducto">
                                        <div class="form-group col-sm-4 col-md-3 mb-3 text-center">
                                            <label class="form-label">Cantidad (f2)</label>
                                            <input type="text" class="form-control text-center" name="cantidad" style="font-size:27px" value="1" id="cantidad" autocomplete="off">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-7 col-lg-4 mb-3 text-center">
                                            <label class="form-label">Código</label>
                                            <input type="text" class="form-control text-center" placeholder="Ingresar código" name="codigo" id="codigo" autofocus="on" style="font-size:27px" required="">
                                        </div>
                                        <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1">
                                    </form>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-12" id="bnombre">
                            <div class="card">
                                <div class="card-body p-3">
                                    <h4 class="card-title mb-3 d-flex justify-content-between align-items-center">
                                        Agregar producto
                                    </h4>
                                    <br>
                                    <hr>
                                    <!-- Fila para los inputs -->
                                    <div class="row">
                                        <!-- Selector de producto -->
                                        <div class="form-group col-md-9 mb-2 text-center">
                                            <label class="form-label font-weight-bold d-block">Producto</label>
                                            <div class="search-container">
                                                <input type="hidden" id="id_producto">
                                                <input type="text" id="search" placeholder="Buscar..." oninput="filtrar_lista()" class="form-control" />
                                                <ul id="suggestions" class="suggestions hidden">
                                                    <!-- Sugerencias dinámicas -->
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Input de cantidad -->
                                        <div class="form-group col-md-3 mb-3 text-center">
                                            <label class="form-label font-weight-bold d-block">Cantidad</label>
                                            <input type="text" name="cantidad" class="form-control text-center" value="1"
                                                style="font-size: 1.5rem;" id="cantidad_producto">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <style>
                                .suggestions li.active {
                                    background-color: #007bff;
                                    color: #fff;
                                }
                            </style>


                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="position-relative pt-0 pb-3">
                                    <!-- Bandera -->
                                    <div class="position-absolute bg-success text-white px-4 py-2 shadow-sm"
                                        style="top: 10px; left: 5px; clip-path: polygon(0 0, 100% 0, 80% 100%, 0% 100%); border-radius: 0 5px 5px 0;" id="texto_estado">

                                    </div>
                                    <br>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title">Productos</h4>
                                    <br>
                                    <hr>
                                    <div class="row">
                                        <div class="ticket-container">
                                            <table id="table_productos_ticket" class="ticket-table">
                                                <thead>
                                                    <tr>
                                                        <th>Borrar</th>
                                                        <th>Cantidad</th>
                                                        <th>Producto</th>
                                                        <th>Precio</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Las filas se agregarán dinámicamente -->
                                                </tbody>
                                                <tfoot>
                                                    <tr id="1" class="gradeX">
                                                        <th class="p-t-0 p-b-0 text-center"></th>
                                                        <th class="text-center"></th>
                                                        <th class="text-center"></th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center" id="text_total_tabla"><b>$0.00</b></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-0">
                                        <h4 class="card-title mb-0">Datos de la venta</h4>
                                        <span data-toggle="tooltip" title="Cambiar nombre">
                                            <a class="btn btn-secondary btn-circle" data-toggle="modal" data-target="#cambiarCliente">
                                                <i class="fas fa-user-edit"></i>
                                            </a>
                                        </span>
                                    </div>
                                    <hr>
                                    <style type="text/css">
                                        #venta td,
                                        #venta th {
                                            padding: 0px;
                                            font-size: 18px;
                                        }
                                    </style>
                                    <table class="table browser no-border mb-0" id="venta">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" class="text-center" id="text_cliente">

                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>FOLIO</b></td>
                                                <td class="text-right"><a href="index.php/pedidos/pedido/15864" id="text_folio_href"></a></td>
                                            </tr>
                                            <tr>
                                                <td><b>ARTICULOS</b></td>
                                                <td class="text-right">1</td>
                                            </tr>
                                            <tr>
                                                <td><b>SUBTOTAL</b></td>
                                                <td class="text-right">$56.64</td>
                                            </tr>
                                            <tr>
                                                <td><b>IVA</b></td>
                                                <td class="text-right">$9.06</td>
                                            </tr>
                                            <tr>
                                                <td><b>IEPS</b></td>
                                                <td class="text-right">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td><b>TOTAL</b></td>
                                                <td class="text-right" id="text_total"></td>
                                            </tr>
                                            <tr>
                                                <td><b>DESCUENTO</b></td>
                                                <td class="text-right">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:35px"><b>COBRAR:</b></td>
                                                <td class="text-right" style="font-size:40px" id="text_cobrar"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row button-group">
                                        <div class="col-lg-4 col-md-4">
                                            <a href="#borrarcancelar" data-toggle="modal" class="btn btn-danger btn-block">Cancel (F9)</a>
                                        </div>
                                        <!-- <div class="col-lg-4 col-md-4">
                                            <a data-toggle="modal" style="color: white;" data-target="#putcredito" class="btn btn-info btn-block">Crédito</a>
                                        </div> -->
                                        <div class="col-lg-4 col-md-4">
                                            <button type="button" data-toggle="modal" data-target="#cobrar" class="btn btn-success btn-block">Cobrar (F7)</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal delete producto-->
                    <div id="deleteProducto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Borrar producto</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" action="index.php/pventa/deleteProducto/15864">
                                    <div class="modal-body">
                                        <input id="producto" type="hidden" name="producto">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Clave de supervisor:</label>
                                            <input id="password" type="password" class="form-control" name="password" required="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">Borrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <!-- Modal delete producto-->
                    <div id="putcredito" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Poner venta a crédito</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" action="index.php/pventa/poneracredito/15864">
                                    <div class="modal-body">
                                        <input id="producto" type="hidden" name="producto">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Clave de supervisor:</label>
                                            <input id="password" type="password" class="form-control" name="password" required="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">A crédito</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <!-- Modal delete producto-->
                    <div id="borrarcancelar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Borrar pedido</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" action="index.php/pedidos/borrar/15864">
                                    <div class="modal-body">
                                        <input id="producto" type="hidden" name="producto">

                                        <div class="form-group">
                                            <label for="password" class="form-label">Clave de supervisor:</label>
                                            <input id="password" type="password" class="form-control" name="password" required="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">Borrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <div id="generaFactura" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content modal-lg">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Confirmar datos de facturación</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" name="timbrado" action="index.php/cfdi/timbrar/15864">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label for="recipient-name1" class="form-label">Cliente:</label>
                                                <input type="text" class="form-control" id="recipient-name1" name="cliente" value="Ab Abasolo " required="">
                                            </div>
                                            <div class="form-group col-6">
                                                <label for="recipient-name2" class="form-label">RFC:</label>
                                                <input type="text" class="form-control" id="recipient-name2" name="rfc" value="XAXX010101000" required="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name3" class="form-label">Domicilio:</label>
                                            <input type="text" class="form-control" id="recipient-name3" name="domicilio" value="Conocido  El moralete Colima Colima" required="">
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name4" class="form-label">Método Pago:</label>
                                            <select name="metodo_pago" class="form-control metodopago" id="metodo_pago">
                                                <option value="PUE">Pago en una sola exhibición</option>
                                                <option value="PPD">Pago en parcialidades o diferido</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name3" class="form-label">Forma de pago:</label>
                                            <select name="forma_pago" class="form-control" id="forma_pago">
                                                <option value="01">Efectivo</option>
                                                <option value="02">Cheque nominativo</option>
                                                <option value="03">Transferencia electrónica de fondos</option>
                                                <option value="04">Tarjeta de crédito</option>
                                                <option value="28">Tarjeta de débito</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name4" class="form-label">Uso CFDI:</label>
                                            <select name="uso_cfdi" class="form-control">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name5" class="form-label">Correo:</label>
                                            <input type="mail" class="form-control" id="recipient-name5" name="correo" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name6" class="form-label">OTROS:</label>
                                            <input type="text" class="form-control" id="recipient-name6" name="otros" value="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="timbrar">Timbrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->

                    <!-- Modal delete producto-->
                    <div id="borrarFacturado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Eliminar pedido </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" action="index.php/pedidos/borrarFacturado/15864">
                                    <div class="modal-body">
                                        <input id="producto2" type="hidden" name="producto">
                                        <div class="form-group">
                                            <label for="password2" class="form-label">Clave de supervisor:</label>
                                            <input id="password2" type="password" class="form-control password" name="password" required="">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">Borrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->

                    <!-- Modal nuevo proyecto-->
                    <div id="cobrar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Cobrar</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" action="index.php/pventa/cobrar/15864" onsubmit="return vdoblesubmit();">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="recipient-name-tarjeta" class="form-label">Entregar a:</label>
                                            <input type="text" id="alias" class="form-control" name="alias">
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name-tarjeta" class="form-label">Método de pagos:</label>
                                            <select name="metodo_pago" class="form-control">
                                                <option value="01">Efectivo</option>
                                                <option value="02">Cheque nominativo</option>
                                                <option value="03">Transferencia electrónica de fondos</option>
                                                <option value="04">Tarjeta de crédito</option>
                                                <option value="28">Tarjeta de débito</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name" class="form-label">Pagó:</label>
                                            <input id="efectivo" type="text" class="form-control" name="efectivo" value="65.70" autofocus="on" autocomplete="off" required="">
                                        </div>
                                        <div class="form-group">
                                            <label for="recipient-name" class="form-label">¿Imprimir ticket?</label>
                                            <div>
                                                <div class="radio radio-primary" style="float:left;">
                                                    <input type="radio" name="imprimir" id="doc" value="0" checked="">
                                                    <label for="doc"> Si </label>
                                                </div>
                                                <div class="radio radio-primary" style="float:left;">
                                                    <input type="radio" name="imprimir" id="doc1" value="1">
                                                    <label for="doc1"> No </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">Cobrar</button>
                                    </div>
                                </form>
                                <script type="text/javascript">
                                    var statSend = false;

                                    function vdoblesubmit() {
                                        if (!statSend) {
                                            statSend = true;
                                            return true;
                                        } else {
                                            swal("Cobrando, espere un momento...");
                                            return false;
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <!-- Modal cambiar cliente-->
                    <div id="cambiaSupervisor" class="modal fade top20" role="dialog" aria-labelledby="cambiarCliente" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Colocar precio producto</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <form method="post" action="index.php/pventa/precioSupervisor/15864">
                                    <div class="modal-body">
                                        <input id="pruductoprecio" type="hidden" name="producto">

                                        <div class="form-group">
                                            <label for="recipient-name-tarjeta" class="form-label">Precio Neto:</label>
                                            <input type="number" class="form-control" name="precio" required="">

                                        </div>

                                        <div class="form-group">
                                            <label for="password2" class="form-label">Clave de supervisor:</label>
                                            <input id="password2" type="password" class="form-control password" name="password" required="">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-info waves-effect waves-light" name="submit">Cambiar</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <!-- ============================================================== -->
                    <!-- End PAge Content -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                </div>
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
        <script src="js/peticiones_ticket.js"></script>
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
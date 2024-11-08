<!-- Modal Nueva Cita -->
<div class="modal fade" id="modal_nueva_cita" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nueva Cita</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <input type="hidden" id="folio_gestion">
                <input type="hidden" id="hora_gestion">
            </div>
            <div class="modal-body">
                <form id="form_nueva_cita">
                    <div class="row mt-3">
                        <!-- Cliente -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="cliente_form" list="clientes" placeholder="Nombre del Cliente" autocomplete="off" onchange="buscar_cliente(this)" onfocus="actualizar_lista_clientes()" required>
                                <!-- <div class="d-flex"> -->
                                <!-- <button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_nueva_cita', 'modal_nuevo_cliente')"><i class="fas fa-user-plus"></i></button>-->
                                <!-- </div> -->
                                <datalist id="clientes">

                                </datalist>
                            </div>
                        </div>
                        <!-- Fecha de Cita -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>

                                <input type="date" class="form-control" id="fecha_cita_form" required onchange="cargar_datos();">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Terapeuta -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                                </div>
                                <select class="form-control" id="terapeuta_form" required onchange="cargar_horarios_disponibles();">
                                    <option value="" disabled selected>Selecciona Terapeuta</option>
                                </select>
                                <button type="button" class="btn btn-info ml-2" onclick="disponibilidad_terapeutas()">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Hora de Cita -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                </div>
                                <select class="form-control" id="hora_cita_form" required onchange="cargar_consultorios_disponibles();">
                                    <option value="" disabled selected>Selecciona Hora de Cita</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Tipo de Servicio -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-briefcase-medical"></i></span>
                                </div>
                                <select class="form-control" id="tipo_servicio_form" onchange="tipo_servicio();" required>
                                    <option value="" disabled selected>Selecciona Tipo de Servicio</option>
                                    <option value="1">Consultorio</option>
                                    <option value="2">Domicilio</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tipo de Cita -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
                                </div>
                                <select class="form-control" id="tipo_cita_form" required disabled>
                                    <option value="" disabled selected>Tipo de Cita</option>
                                    <option value="1">Subsecuente</option>
                                    <option value="2">Primera vez</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Consultorio -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clinic-medical"></i></span>
                                </div>
                                <select class="form-control" id="consultorio_form" required>
                                    <option value="" disabled selected>Selecciona Consultorio</option>
                                </select>
                                <button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_nueva_cita', 'modal_nuevo_consultorio')">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group mt-3">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones_form" rows="4" placeholder="Escribe aqu&iacute; tus observaciones" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="resetear('folio_gestion')">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="gestionar_cita()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Nueva Cita -->


<!-- Modal Nuevo cliente -->
<div class="modal fade" id="modal_nuevo_cliente" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nuevo Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <div class="icheck-success d-inline">
                                <input type="radio" name="tipo_cliente" id="no_fisico" value="1" checked onclick="activar_no_fisico()">
                                <label for="no_fisico">Cliente no fiscal</label>
                            </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <div class="icheck-success d-inline">
                                <input type="radio" name="tipo_cliente" id="fisico" value="2" onclick="activar_fisico()">
                                <label for="fisico">Cliente fiscal</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="RFC" id="rfc" onfocus="resetear('rfc')" maxlength="13" value="XAXX010101000" disabled>
                        </div>
                    </div>
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
                    <div class="col-12">
                        <div class="card card-dark card-tabs">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true"><i class="fas fa-info"></i> &nbsp;Datos de contacto</a>
                                    </li>
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
                                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="Correo electr&oacute;nico" id="correo" onfocus="resetear('correo')" maxlength="150">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="N&uacute;mero telef&oacute;nico" id="telefono" onfocus="resetear('telefono')" maxlength="10" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-road"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="Calle" id="calle" onfocus="resetear('calle')" maxlength="50" disabled>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="N&uacute;mero exterior" id="no_exterior" onfocus="resetear('no_exterior')" maxlength="50" disabled>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="N&uacute;mero interior" id="no_interior" onfocus="resetear('no_interior')" maxlength="50" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-street-view"></i></span>
                                                    </div>
                                                    <input type="number" class="form-control" placeholder="C&oacute;digo postal" id="codigo_postal" onfocus="resetear('codigo_postal')" onKeyup="buscar_cp()" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" min="0" disabled>
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
                                                    <button type="button" class="btn btn-info" id="colonia_texto" onclick="colonia_text();" title="Capturar colonia" disabled><i class="fas fa-edit"></i></button>
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
                                                    <select class="form-control" id="regimen" onfocus="resetear('regimen')" disabled>
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
                                                    <select class="form-control" id="uso_cfdi" onfocus="resetear('uso_cfdi')" disabled>
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
                                                    <select class="form-control" id="metodo_pago" onfocus="resetear('metodo_pago')" onchange="activar_forma()" disabled>
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
                                                    <select class="form-control" id="forma_pago" onfocus="resetear('forma_pago')" disabled>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_nuevo_cliene', 'modal_nueva_cita')">Cerrar</button>
                    <center><button type="button" class="btn btn-success btn-lg" onclick="gestionar_cliente('','modal_nueva_cita', 'modal_nuevo_cliente');">Guardar Cliente</button></center>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Funciones JS Personalizadas -->
<script src="js/peticiones_clientes.js"></script>
<!-- Modal Nuevo cliente -->

<!-- Modal nuevo terapeuta -->
<div class="modal fade" id="modal_nuevo_terapeuta" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nuevo Terapeuta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                            <input type="text" class="form-control" placeholder="Nombre del personal" id="nombre_personal" onfocus="resetear('nombre_personal')" maxlength="150" require>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                            </div>
                            <select class="form-control" id="tipo_personal" name="tipo_personal" required>
                                <option value="2" selected>Terapeuta</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card card-dark card-tabs">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-one-home-tab_ter" data-toggle="pill" href="#custom-tabs-one-home_ter" role="tab" aria-controls="custom-tabs-one-home_ter" aria-selected="true"><i class="fas fa-info"></i> &nbsp;Datos de contacto</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-one-profile-tab_ter" data-toggle="pill" href="#custom-tabs-one-profile_ter" role="tab" aria-controls="custom-tabs-one-profile_ter" aria-selected="false"><i class="fas fa-map-marked"></i> &nbsp;Domicilio</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade show active" id="custom-tabs-one-home_ter" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab_ter">
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
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-profile_ter" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab_ter">
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
                                                    <input type="text" class="form-control" placeholder="C&oacute;digo postal" id="codigo_postal" onfocus="resetear('codigo_postal')" onKeyup="buscar_cp()" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" min="0">
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
                                                    <button type="button" class="btn btn-info" id="colonia_texto" onclick="colonia_text();" title="Capturar colonia" disabled><i class="fas fa-edit"></i></button>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_nuevo_cliene', 'modal_nueva_cita')">Cerrar</button>
                <center><button type="button" class="btn btn-success" onclick="gestionar_personal('', 'modal_nueva_cita', 'modal_nuevo_terapeuta');">Guardar Terapeuta</button></center><br><br><br>
            </div>
        </div>
    </div>
</div>
<!-- Modal nuevo terapeuta -->

<!-- Modal disponibilidad terapeuta -->
<div class="modal fade" id="modal_ver_terapeuta" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Disponibilidad de Terapeuta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div id="disponibilidad_terapeutas" class="table-responsive"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_nuevo_cliene', 'modal_nueva_cita')">Regresar</button>
            </div>
        </div>
    </div>
</div>
<style>
    .badge-custom {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 5px;
        text-align: center;
    }

    .calendar-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
        grid-gap: 10px;
        background-color: #f0f0f0;
        padding: 10px;
    }
</style>
<!-- Modal disponibilidad terapeuta -->

<!-- Funciones JS Personalizadas -->
<script src="js/peticiones_personal.js"></script>


<!-- Modal nuevo Consultorio -->
<div class="modal fade" id="modal_nuevo_consultorio" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nuevo Consultorio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                            <input type="text" class="form-control" placeholder="Nombre del consultorio" id="nombre_consultorio" onfocus="resetear('nombre_consultorio')" maxlength="150" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_nuevo_consultorio', 'modal_nueva_cita')">Cerrar</button>
                <center><button type="button" class="btn btn-success" onclick="gestionar_consultorio('', 'modal_nueva_cita', 'modal_nuevo_consultorio');">Guardar Consultorio</button></center><br><br><br>
            </div>
        </div>
    </div>
</div>
<!-- Funciones JS Personalizadas -->
<script src="js/peticiones_consultorios.js"></script>
<!-- Modal nuevo Consultorio -->

<!-- Modal Cancelacion -->
<div class="modal fade" id="modal_cancelacion" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-danger">Cancelar Cita</h4>
            </div>
            <div class="card-body">
                <div class="card card-info">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-danger">
                                Folio:
                                <span id="folio_cancelar"> </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h7 id="cliente_cancelar">
                                    </h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <h7 id="terapeuta_cancelar">
                                    </h6>
                            </div>
                            <div class="col-5">
                                <h7 id="consultorio_cancelar">
                                    </h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-file-signature"></i>
                                </span>
                            </div>
                            <textarea id="motivo_cancelacion" class="form-control" placeholder="Escriba el motivo de la cancelaci&oacute;n" onfocus="resetear('motivo_cancelacion')" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="cerrar_modal('modal_cancelacion', '')">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="enviar_cancelacion();">Confirmar Cancelaci&oacute;n</button><br><br><br>
            </div>
        </div>
    </div>
</div>
<!-- Modal Cancelacion -->

<!-- Modal Valoracion Cita -->
<div class="modal fade" id="modal_valoracion" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-danger">Valoración de Cita</h4>
                <input type="hidden" id="id_cliente_valoracion">
                <input type="hidden" id="id_folio_cita">
            </div>

            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-dp-tab" data-toggle="pill" href="#custom-tabs-one-dp" role="tab" aria-controls="custom-tabs-one-dp" aria-selected="true">
                            <i class="fas fa-user"></i> &nbsp;Datos Personales
                        </a>
                    </li>
                    <li class="nav-item" id="observaciones">

                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-enf-tab" data-toggle="pill" href="#custom-tabs-one-enf" role="tab" aria-controls="custom-tabs-one-enf" aria-selected="false">
                            <i class="fas fa-list"></i> &nbsp;Enfermedades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-sv-tab" data-toggle="pill" href="#custom-tabs-one-sv" role="tab" aria-controls="custom-tabs-one-sv" aria-selected="false">
                            <i class="fas fa-heartbeat"></i> &nbsp;Signos Vitales
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <!-- Datos Personales Tab -->
                <div class="tab-pane fade show active" id="custom-tabs-one-dp" role="tabpanel" aria-labelledby="custom-tabs-one-dp-tab">
                    <div class="modal-body">
                        <form id="form_valoracion">
                            <div class="row mt-3">
                                <!-- Nombre -->
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="nombre_valoracion" placeholder="Ingrese su nombre" disabled>
                                    </div>
                                </div>

                                <!-- Estado Civil -->
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-heart"></i></span>
                                        </div>
                                        <select class="form-control" id="estado_civil_valoracion" disabled>
                                            <option value="" selected disabled>Estado Civil</option>
                                            <option value="1">Soltero/a</option>
                                            <option value="2">Casado/a</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" onclick="habilitarParaModificar('estado_civil_valoracion')" title="Modificar Estado Civil">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edad -->
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="edad_valoracion" placeholder="Ingrese su edad" onfocus="resetear('edad_valoracion')" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <!-- Ocupación -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                        </div>
                                        <select class="form-control" id="ocupacion_valoracion" onfocus="resetear('ocupacion_valoracion')" disabled>
                                            <!-- Opciones dinámicas de ocupaciones -->
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" onclick="habilitarParaModificar('ocupacion_valoracion')" title="Modificar Ocupación">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="telefono_valoracion" placeholder="Teléfono" maxlength="10" onfocus="resetear('telefono_valoracion')" disabled>
                                    </div>
                                </div>

                                <!-- Fecha Nacimiento -->
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="fecha_nacimiento" onfocus="resetear('fecha_nacimiento')" disabled>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" onclick="habilitarParaModificar('fecha_nacimiento')" title="Modificar Edad">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body" id="boton_confirmar">

                    </div>
                </div>

                <input type="hidden" id="tipo_consulta">
                <!-- Observaciones Tab PRIMERA VEZ-->
                <div class="tab-pane fade" id="custom-tabs-one-obs-pv" role="tabpanel" aria-labelledby="custom-tabs-one-obs-tab">
                    <div class="modal-body">
                        <form id="form_observaciones">
                            <!-- Motivo de Consulta -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-comment-medical"></i></span>
                                        </div>
                                        <textarea class="form-control" id="motivo_consulta_valoracion" rows="3" placeholder="Motivo de consulta..." onfocus="resetear('motivo_consulta_valoracion')"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Actividad Física y Toxicomanías -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-running"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="act_fisica_valoracion" placeholder="Actividad física" onfocus="resetear('act_fisica_valoracion')">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-skull"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="toximanias_valoracion" placeholder="Toxicomanías" onfocus="resetear('toximanias_valoracion')">
                                    </div>
                                </div>
                            </div>

                            <!-- Fármacos -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pills"></i></span>
                                        </div>
                                        <textarea class="form-control" id="farmacos" rows="2" placeholder="Ejemplo: Diclofenaco inyectado" onfocus="resetear('farmacos')"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Diagnóstico Médico -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                        </div>
                                        <textarea class="form-control" id="diagnosticoMedico" rows="3" placeholder="Diagnóstico médico"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Escala de Dolor EVA -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-thermometer-half"></i></span>
                                        </div>
                                        <div class="form-control">
                                            <div class="text-danger d-flex align-items-center" id="escaDolMessage"></div>
                                            <div class="d-flex align-items-center">
                                                <input type="range" class="custom-range ml-2" min="0" max="10" id="escalaDolorP" value="0" oninput="updateValue(1,this.value)">
                                                <span id="escalaValorP" class="ml-2">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- Observaciones Tab SUBSECUENTE-->
                <div class="tab-pane fade" id="custom-tabs-one-obs-sb" role="tabpanel" aria-labelledby="custom-tabs-one-obs-tab">
                    <div class="modal-body">
                        <form id="form_terapia">
                            <!-- Número de Terapia, Continuo/Intermitente, y Avance -->
                            <div class="row">
                                <div class="col-md-2 mt-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                        </div>
                                        <input class="form-control" id="num_terapia" placeholder="Número de terapia..." onfocus="resetear('motivo_consulta_valoracion')" disabled>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-exchange-alt"></i></span>
                                        </div>
                                        <select id="cont_int" class="form-control" onfocus="resetear('cont_int')" disabled>
                                            <option value="1">Continuo</option>
                                            <option value="2">Intermitente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-chart-line"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="avance" placeholder="Ejemplo: 20%" onfocus="resetear('toximanias_valoracion')">
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                        </div>
                                        <textarea class="form-control" id="observacionesSubSec" rows="3" placeholder="Observaciones"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Paquete y No. Terapia -->
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-box"></i></span>
                                        </div>
                                        <input class="form-control" id="farmacos" placeholder="Ejemplo: Paquete de 5 sesiones" onfocus="resetear('farmacos')" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-clipboard-list"></i></span>
                                        </div>
                                        <input class="form-control" id="farmacos" placeholder="Ejemplo: 2/3" onfocus="resetear('farmacos')" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Escala de Dolor EVA -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-thermometer-half"></i></span>
                                        </div>
                                        <div class="form-control">
                                            <div class="text-danger d-flex align-items-center" id="escaDolMessage1"></div>
                                            <div class="d-flex align-items-center">
                                                <input type="range" class="custom-range ml-2" min="0" max="10" id="escalaDolorS" value="0" oninput="updateValue(2,this.value)">
                                                <span id="escalaValorS" class="ml-2">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <script>
                        function updateValue(num, val) {
                            if (num == 1) {
                                document.getElementById('escalaValorP').innerText = val;
                            } else if (num == 2) {
                                document.getElementById('escalaValorS').innerText = val;
                            }
                        }
                    </script>

                </div>

                <!-- Enfermedades Tab -->
                <div class="tab-pane fade" id="custom-tabs-one-enf" role="tabpanel" aria-labelledby="custom-tabs-one-enf-tab">
                    <div class="col-md-12 mt-4 mb-4 mx-auto">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <!-- Enfermedades y botón para añadir una nueva -->
                                <div class="d-flex col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                        </div>
                                        <select class="form-control" id="enfermedades" onfocus="resetear('enfermedades')">
                                            <!-- Opciones de enfermedades -->
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_valoracion', 'modal_nueva_enfermedad')" title="Agregar nueva enfermedad">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>

                                <!-- Tiempo con la enfermedad -->
                                <div class="col-md-3 mt-3 mt-md-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="tiempo_enfermedad" placeholder="Tiempo con la enfermedad" onfocus="resetear('tiempo_enfermedad')">
                                    </div>
                                </div>

                                <!-- ¿Toma algún medicamento? -->
                                <div class="col-md-3 mt-3 mt-md-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pills"></i></span>
                                        </div>
                                        <select class="form-control" id="toma_medicamento" onfocus="resetear('toma_medicamento')">
                                            <option value="" selected disabled>¿Toma medicamento?</option>
                                            <option value="NO">No</option>
                                            <option value="SI">Sí</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Botón Agregar -->
                                <div class="col-md-2 mt-3 mt-md-0 text-right">
                                    <button type="button" class="btn btn-success" onclick="agregarEnfermedadValoracion()">Agregar</button>
                                </div>
                            </div>


                        </div>
                        <!-- Contenedor de los formularios de enfermedades -->
                        <div class="col-md-12 mx-auto pb-4">
                            <div id="form_enfermedad" class="col-md-auto mx-auto">
                                <!-- Aquí se apilarán los formularios dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signos Vitales Tab -->
                <div class="tab-pane fade" id="custom-tabs-one-sv" role="tabpanel" aria-labelledby="custom-tabs-one-sv-tab">
                    <div class="card-body">
                        <!-- Signos Vitales -->
                        <div class="form-group">
                            <!-- Línea de tensión arterial, FC, FR -->
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-heartbeat"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="tension_art" placeholder="Tensión Arterial">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-heart"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="fc" placeholder="FC">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lungs"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="fr" placeholder="FR">
                                    </div>
                                </div>
                            </div>

                            <!-- Línea de oxigenación, temperatura, glucosa -->
                            <div class="form-row mt-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lungs-virus"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="oxigeno" placeholder="Saturación de O2">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-thermometer-half"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="temperatura" placeholder="Temperatura">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tint"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="glucosa" placeholder="Glucosa">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="cerrar_modal('modal_valoracion', '')">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="enviar_valoracion();">Guardar Valoración</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Valoracion Cita -->

<!-- Modal nueva ocupacion -->
<div class="modal fade" id="modal_nueva_ocupacion" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nueva Ocupacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-id-card"></i> Datos de identificación</h6>
                <hr>

                <div class="row">
                    <div class="col-8">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Nombre de la Ocupación" id="nombre_ocupacion" onfocus="resetear('nombre_ocupacion')" maxlength="150" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_nueva_ocupacion', 'modal_valoracion')">Cerrar</button>
                <center><button type="button" class="btn btn-success" onclick="gestionar_ocupacion('modal_valoracion', 'modal_nueva_ocupacion');">Guardar Ocupación</button></center><br><br><br>
            </div>
        </div>
    </div>
</div>
<!-- Modal nueva ocupacion -->

<!-- Modal nueva enfermedad-->
<div class="modal fade" id="modal_nueva_enfermedad" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nueva Enfermedad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-id-card"></i> Datos de identificación</h6>
                <hr>

                <div class="row">
                    <div class="col-8">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Nombre de la enfermedad" id="nombre_enfermedad" onfocus="resetear('nombre_enfermedad')" maxlength="150" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_nueva_enfermedad', 'modal_valoracion')">Cerrar</button>
                <center><button type="button" class="btn btn-success" onclick="gestionar_enfermedad('modal_valoracion', 'modal_nueva_enfermedad');">Guardar Enfermedad</button></center><br><br><br>
            </div>
        </div>
    </div>
</div>
<!-- Modal nueva enfermedad-->
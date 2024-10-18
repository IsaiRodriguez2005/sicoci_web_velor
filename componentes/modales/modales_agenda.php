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
                    <div class="row">
                        <!-- Cliente -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente">Cliente</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control" id="cliente_form" list="clientes" placeholder="Nombre del Cliente" autocomplete="off" onchange="buscar_cliente(this)" onfocus="actualizar_lista_clientes()" required>
                                    <!-- <button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_nueva_cita', 'modal_nuevo_cliente')"><i class="fas fa-user-plus"></i></button>-->
                                </div>
                                <datalist id="clientes">

                                </datalist>
                            </div>
                        </div>
                        <!-- Fecha de Cita -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_hora_cita">Fecha de Cita</label>
                                <input type="date" class="form-control" id="fecha_cita_form" required onchange="cargar_datos();">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Terapeuta -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="terapeuta">Terapeuta</label>
                                <div class="d-flex">
                                    <select class="form-control" id="terapeuta_form" required onchange="cargar_horarios_disponibles();">
                                        <option value="" disabled selected>Selecciona Terapeuta</option>
                                    </select>
                                    <!--<button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_nueva_cita', 'modal_nuevo_terapeuta')"><i class="fas fa-user-plus"></i></button>-->
                                    <button type="button" class="btn btn-info ml-2" onclick="disponibilidad_terapeutas()"><i class="far fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- Hora de Cita -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_hora_cita">Hora de Cita</label>
                                <select class="form-control" id="hora_cita_form" required onchange="cargar_consultorios_disponibles();">
                                    <option value="" disabled selected>Selecciona Hora de Cita</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Tipo de Servicio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_servicio">Tipo de Servicio</label>
                                <select class="form-control" id="tipo_servicio_form" onchange="tipo_servicio();" required>
                                    <option value="" disabled selected>Selecciona Tipo de Servicio</option>
                                    <option value="1">Consultorio</option>
                                    <option value="2">Domicilio</option>
                                </select>
                            </div>
                        </div>
                        <!-- Tipo de Cita -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_cita">Tipo de Cita</label>
                                <select class="form-control" id="tipo_cita_form" required>
                                    <option value="" disabled selected>Selecciona Tipo de Cita</option>
                                    <option value="1">Subsecuente</option>
                                    <option value="2">Primera vez</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Consultorio -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="consultorio">Consultorio</label>
                                <div class="d-flex">
                                    <select class="form-control" id="consultorio_form" required>

                                    </select>
                                    <button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_nueva_cita', 'modal_nuevo_consultorio')"><i class="fas fa-plus-circle"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Observaciones -->
                    <div class="form-group">
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
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-obs-tab" data-toggle="pill" href="#custom-tabs-one-obs" role="tab" aria-controls="custom-tabs-one-obs" aria-selected="false">
                            <i class="fas fa-info"></i> &nbsp;Observaciones
                        </a>
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
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-5">
                                <label for="nombre_valoracion">Nombre:</label>
                                <input type="text" class="form-control" id="nombre_valoracion" placeholder="Ingrese su nombre" disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="estado_civil_valoracion">Estado Civil:</label>
                                <select class="form-control" id="estado_civil_valoracion">
                                    <option value="" selected disabled>Estado Civil</option>
                                    <option value="1">Soltero/a</option>
                                    <option value="2">Casado/a</option>
                                    <!-- Agrega más opciones si es necesario -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="edad_valoracion">Edad:</label>
                                <input type="number" class="form-control" id="edad_valoracion" placeholder="Ingrese su edad" onfocus="resetear('edad_valoracion')">
                            </div>
                            <div class="col-md-5">
                                <label for="ocupacion_valoracion">Ocupación:</label>
                                <div class="d-flex">
                                    <select class="form-control" id="ocupacion_valoracion" onfocus="resetear('ocupacion_valoracion')">
                                        <!-- Opciones dinámicas de ocupaciones -->
                                    </select>
                                    <button type="button" class="btn btn-info ml-2" onclick="abrir_modal('modal_valoracion', 'modal_nueva_ocupacion')">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="telefono_valoracion">Teléfono:</label>
                                <input type="text" class="form-control" id="telefono_valoracion" placeholder="Teléfono" maxlength="10" onfocus="resetear('telefono_valoracion')">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones Tab -->
                <div class="tab-pane fade" id="custom-tabs-one-obs" role="tabpanel" aria-labelledby="custom-tabs-one-obs-tab">
                    <div class="card-body">
                        <!-- Aquí va el contenido de las observaciones -->
                        <div class="form-group">
                            <label for="toximanias_valoracion">Toxicomanías:</label>
                            <input type="text" class="form-control" id="toximanias_valoracion" placeholder="Toximanias">
                        </div>
                        <div class="form-group">
                            <label for="farmacos">Fármacos:</label>
                            <textarea class="form-control" id="farmacos" rows="2" placeholder="Ejemplo: Diclofenaco inyectado"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="diagnosticoMedico">Diagnóstico Médico:</label>
                            <textarea class="form-control" id="diagnosticoMedico" rows="3" placeholder="Diagnóstico"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="escalaDolor">Escala de Dolor EVA:</label>
                            <div class="d-flex align-items-center">
                                <input type="range" class="custom-range ml-2" min="0" max="10" id="escalaDolor" value="0" oninput="updateValue(this.value)">
                                <span id="escalaValor" class="ml-2">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enfermedades Tab -->
                <div class="tab-pane fade" id="custom-tabs-one-enf" role="tabpanel" aria-labelledby="custom-tabs-one-enf-tab">
                    <div class="col-md-8 mt-4 mb-4 mx-auto">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <!-- Contenedor centrado sin text-center para alinear correctamente -->
                                <label for="ocupacion_valoracion" class="font-weight-bold">Enfermedades:</label>

                                <div class="d-flex justify-content-between mb-3">
                                    <!-- Centrar el select y botón -->
                                    <select class="form-control col-md-9" id="enfermedades" onfocus="resetear('enfermedades')">

                                    </select>
                                    <button type="button" class="btn btn-info ml-3" onclick="abrir_modal('modal_valoracion', 'modal_nueva_enfermedad')">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="tiempo_enfermedad" class="font-weight-bold">Tiempo con la enfermedad:</label>
                                        <input type="text" class="form-control" id="tiempo_enfermedad" placeholder="Ingrese el tiempo con la enfermedad" onfocus="resetear('tiempo_enfermedad')">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="toma_medicamento" class="font-weight-bold">¿Toma algún medicamento?</label>
                                        <select class="form-control" id="toma_medicamento" onfocus="resetear('tiempo_enfermedad')">

                                            <option value="" selected disabled>Selecciona una Opcion</option>
                                            <option value="NO">No</option>
                                            <option value="SI">Sí</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Medicamentos, solo se muestra si selecciona " Sí" -->
                                    <div id="medicamento_section" style="display: none;" class="mt-4">
                                        <label for="nombre_medicamento" class="font-weight-bold">Medicamento(s):</label>
                                        <input type="text" class="form-control" id="nombre_medicamento" placeholder="Ingrese los medicamentos">
                                    </div>

                                    <!-- Botón Agregar -->
                                    <div class="text-right mt-4">
                                        <button type="button" class="btn btn-success" onclick="agregarEnfermedadValoracion()">Agregar</button>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <!-- Contenedor de los formularios de enfermedades -->
                    <div class="col-md-10 mx-auto pb-4">
                        <div id="form_enfermedad" class="col-md-auto mx-auto">
                            <!-- Aquí se apilarán los formularios dinámicamente -->
                        </div>
                    </div>
                </div>


                <!-- Signos Vitales Tab -->
                <div class="tab-pane fade" id="custom-tabs-one-sv" role="tabpanel" aria-labelledby="custom-tabs-one-sv-tab">
                    <div class="card-body">
                        <!-- Aquí va el contenido de signos vitales -->
                        <div class="form-group">
                            <label for="ta">Signos Vitales:</label>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="tension_art" placeholder="TA">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="fc" placeholder="FC">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="fr" placeholder="FR">
                                </div>
                            </div>
                            <!-- Agrega más campos aquí -->
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
<script>
    function updateValue(val) {
        document.getElementById('escalaValor').innerText = val;
    }
</script>

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
                <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6>
                <hr>

                <div class="row">
                    <div class="col-8">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Nombre de la Ocupacion" id="nombre_ocupacion" onfocus="resetear('nombre_ocupacion')" maxlength="150" required>
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
                <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6>
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
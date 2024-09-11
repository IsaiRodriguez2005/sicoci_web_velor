<!-- Modal Nueva Cita -->
<div class="modal fade" id="modal_nuevo_cliente" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Registrar Nueva Cita</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                    <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6><hr>
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
                                                        <input type="text" class="form-control" placeholder="N&uacute;mero telef&oacute;nico" id="telefono" onfocus="resetear('telefono')" maxlength="10"  onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
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
                                                                while($regimenes = mysqli_fetch_array($resultadoRegimenes))
                                                                {
                                                                    echo "<option value='".$regimenes['clave_regimen']."'>[".$regimenes['clave_regimen']."] ".$regimenes['descripcion']."</option>";
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
                                                                while($usos = mysqli_fetch_array($resultadoUsos))
                                                                {
                                                                    echo "<option value='".$usos['clave_uso']."'>[".$usos['clave_uso']."] ".$usos['descripcion']."</option>";
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
                                                                while($metodo = mysqli_fetch_array($resultadoMetodo))
                                                                {
                                                                    echo "<option value='".$metodo['clave_metodo']."'>[".$metodo['clave_metodo']."] ".$metodo['descripcion']."</option>";
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
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="gestionar_cita()">Guardar</button>
            </div>
        </div>
    </div>
</div>
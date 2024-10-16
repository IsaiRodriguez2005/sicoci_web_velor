<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
        $html = "";
        $sql = "SELECT e.rfc, 
                        e.nombre_social, 
                        e.nombre_comercial, 
                        e.calle, e.exterior, 
                        e.interior, 
                        e.codigo_postal, 
                        e.clave_colonia, 
                        e.clave_estado, 
                        e.clave_municipio, 
                        e.clave_pais, 
                        e.clave_regimen, 
                        e.sitio_web, 
                        e.correo, 
                        e.telefono, 
                        e.hora_entrada, 
                        e.hora_salida, 
                        e.rango_citas, 
                        e.hora_entrada_sabado, 
                        e.hora_salida_sabado,
                        e.hora_comida_inicio, 
                        e.hora_comida_fin,
                        c.nombre_colonia, 
                        es.nombre_estado, 
                        m.nombre_municipio, 
                        p.descripcion FROM emisores e LEFT JOIN _cat_sat_colonias c ON c.clave_colonia = e.clave_colonia AND c.codigo_postal = e.codigo_postal LEFT JOIN _cat_sat_estados es ON es.clave_estado = e.clave_estado AND es.clave_pais = e.clave_pais LEFT JOIN _cat_sat_municipios m ON m.clave_municipio = e.clave_municipio AND m.clave_estado = e.clave_estado LEFT JOIN _cat_sat_pais p ON p.clave_pais = e.clave_pais  WHERE id_emisor = ".$_SESSION['id_emisor'];
        $reslSQL = mysqli_query($conexion, $sql);
        $emisor = mysqli_fetch_array($reslSQL);

        if(strlen($emisor['clave_colonia']) > 4)
        {
            $colonia = '
                <input type="text" class="form-control" placeholder="Escribe colonia" id="colonia_text" maxlength="100" onfocus="resetear(&quot;colonia_text&quot;)" value="'.$emisor['clave_colonia'].'">
                &nbsp;
                &nbsp; <button type="button" class="btn btn-info" onclick="colonia_select();" title="Capturar colonia"><i class="fas fa-edit"></i></button>
            ';
        }
        else
        {
            $colonia = '
                <select class="form-control" id="colonia" onfocus="resetear(&quot;colonia&quot;)">
                    <option value="'.$emisor['clave_colonia'].'">'.$emisor['nombre_colonia'].'</option>
                </select>
                &nbsp;
                <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>
            ';
        }
        $html .= '
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Mis par&aacute;metros fiscales</h3>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-id-card"></i> Datos de identificaci&oacute;n</h6><hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="RFC" id="rfc" onfocus="resetear(&quot;rfc&quot;)" maxlength="13" value="'.$emisor['rfc'].'">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Raz&oacute;n social" id="nombre_social" onfocus="resetear(&quot;nombre_social&quot;)" maxlength="100" value="'.$emisor['nombre_social'].'">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-industry"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Nombre comercial" id="nombre_comercial" onfocus="resetear(&quot;nombre_comercial&quot;)" maxlength="100" value="'.$emisor['nombre_comercial'].'">
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
                                <input type="text" class="form-control" placeholder="Calle" id="calle" onfocus="resetear(&quot;calle&quot;)" maxlength="35" value="'.$emisor['calle'].'">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="N&uacute;mero exterior" id="no_exterior" onfocus="resetear(&quot;no_exterior&quot;)" maxlength="30" value="'.$emisor['exterior'].'">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="N&uacute;mero interior" id="no_interior" onfocus="resetear(&quot;no_interior&quot;)" maxlength="30" value="'.$emisor['interior'].'">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-street-view"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="C&oacute;digo postal" id="codigo_postal" onfocus="resetear(&quot;codigo_postal&quot;)" onKeyup="buscar_cp()" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="'.$emisor['codigo_postal'].'" maxlength="5">
                            </div>
                        </div>
                        <div class="col-4">
                            <input type="hidden" id="colonia_oculta" value="1">
                            <div class="input-group mb-3" id="dato_colonia">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                '.$colonia.'
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="estado" onfocus="resetear(&quot;estado&quot;)" disabled>
                                <option value="'.$emisor['clave_estado'].'">'.$emisor['nombre_estado'].'</option>
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
                                <select class="form-control" id="municipio" onfocus="resetear(&quot;municipio&quot;)" disabled>
                                <option value="'.$emisor['clave_municipio'].'">'.$emisor['nombre_municipio'].'</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="pais" onfocus="resetear(&quot;pais&quot;)" disabled>
                                <option value="'.$emisor['clave_pais'].'">'.$emisor['descripcion'].'</option>
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
                                <select class="form-control" id="regimen" onfocus="resetear(&quot;regimen&quot;)">
                                    <option value="0">Regimen fiscal</option>
            ';
                                    $consultaRegimenes = "SELECT clave_regimen, descripcion FROM _cat_sat_regimen_fiscal WHERE estatus = 1 ORDER BY descripcion ASC";
                                    $resultadoRegimenes = mysqli_query($conexion, $consultaRegimenes);
                                    while($regimenes = mysqli_fetch_array($resultadoRegimenes))
                                    {
                                        if($regimenes['clave_regimen'] == $emisor['clave_regimen'])
                                        {
                                            $html .= "<option value='".$regimenes['clave_regimen']."' selected>[".$regimenes['clave_regimen']."] ".$regimenes['descripcion']."</option>";
                                        }
                                        else
                                        {
                                            $html .= "<option value='".$regimenes['clave_regimen']."'>[".$regimenes['clave_regimen']."] ".$regimenes['descripcion']."</option>";
                                        }
                                    }
        $html .= '
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
                                <input type="text" class="form-control" placeholder="Tel&eacute;fono(s)" id="telefono" onfocus="resetear(&quot;telefono&quot;)" maxlength="50" value="'.$emisor['telefono'].'">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Correo(s) electr&oacute;nico(s)" id="correo" onfocus="resetear(&quot;correo&quot;)" maxlength="100" value="'.$emisor['correo'].'">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Sitio web" id="sitio_web" onfocus="resetear(&quot;sitio_web&quot;)" maxlength="100" value="'.$emisor['sitio_web'].'">
                            </div>
                        </div>
                    </div>
                    <br><h6><i class="fas fa-clock"></i></i> Horarios lunes a viernes</h6><hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Entrada</i></span>
                                </div>
                                <input type="time" class="form-control" id="hora_entrada" onfocus="resetear(&quot;hora_entrada&quot;)" maxlength="50" value="'.$emisor['hora_entrada'].'">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Salida</i></span>
                                </div>
                                <input type="time" class="form-control" id="hora_salida" onfocus="resetear(&quot;hora_salida&quot;)" maxlength="50" value="'.$emisor['hora_salida'].'">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rangos</span>
                                </div>
                                <input type="number" class="form-control" placeholder="Tiempo en minutos" id="rango_citas" onfocus="resetear(&quot;rango_citas&quot;)" min="1" max="250" value="'.$emisor['rango_citas'].'">
                            </div>
                        </div>
                    </div>
                    <br><h6><i class="fas fa-clock"></i></i> Horarios sabados</h6><hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Entrada</i></span>
                                </div>
                                <input type="time" class="form-control" id="hora_entrada_sabado" onfocus="resetear(&quot;hora_entrada_sabado&quot;)" maxlength="50" value="'.$emisor['hora_entrada_sabado'].'">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Salida</i></span>
                                </div>
                                <input type="time" class="form-control" id="hora_salida_sabado" onfocus="resetear(&quot;hora_salida_sabado&quot;)" maxlength="50" value="'.$emisor['hora_salida_sabado'].'">
                            </div>
                        </div>
                    </div><br>
                    <br><h6><i class="fas fa-clock"></i></i> Horarios de comida</h6><hr>
                    <div class="row">
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Inicio</i></span>
                                </div>
                                <input type="time" class="form-control" id="hora_comida_inicio" onfocus="resetear(&quot;hora_entrada&quot;)" maxlength="50" value="'.$emisor['hora_comida_inicio'].'">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Fin</i></span>
                                </div>
                                <input type="time" class="form-control" id="hora_comida_fin" onfocus="resetear(&quot;hora_salida&quot;)" maxlength="50" value="'.$emisor['hora_comida_fin'].'">
                            </div>
                        </div>
                    </div><br>
                    <center><button type="button" class="btn btn-info" onclick="gestionar_emisor();" id="btn_emisor">Guardar Datos</button></center><br>
                </div>
            </div>
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Mis cuentas bancarias</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                </div>
                                <input type="text" class="form-control" id="mrfc_banco" placeholder="RFC del banco" disabled>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                                </div>
                                <select class="form-control" id="mnombre_banco" onchange="habilitar_banco();">
                                    <option value="0">Selecciona banco</option>
                                    ';
                                    $consultaBanco = "SELECT rfc_banco, nombre_banco FROM _cat_sat_bancos WHERE estatus = 1 ORDER BY nombre_banco ASC";
                                    $resultadoBanco = mysqli_query($conexion, $consultaBanco);
                                    while($banco = mysqli_fetch_array($resultadoBanco))
                                    {
                                        $html .= "<option value='".$banco['rfc_banco']."'>".$banco['nombre_banco']."</option>";
                                    }
                            $html .= '
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-money-check"></i></span>
                                </div>
                                <input type="number" class="form-control" id="mcuenta" placeholder="Cuenta/Clabe bancaria" max="999999999999999999" min="0" onfocus="resetear(&quot;mcuenta&quot;)" disabled>
                            </div>
                        </div>
                        <div class="col-4">
                            &nbsp; <button type="button" class="btn btn-primary" id="mguardar_cuenta" onclick="registrar_cuenta()" disabled>Guardar cuenta</button>
                        </div>
                    </div>
                    <br><h6><i class="fas fa-money-check"></i> Mis cuentas registradas</h6><hr>
                    <div class="row">
                        <div class="col-12" id="tabla_cuentas">
                            <table class="table table-striped" id="cuentas_bancarias">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Acciones</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Cuenta</th>
                                        <th class="text-center">RFC Banco</th>
                                        <th class="text-center">Nombre Banco</th>
                                        <th class="text-center">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                ';
                                $sql = "SELECT id_cuenta, rfc_banco, nombre_banco, cuenta, estatus FROM emisores_cuentas WHERE id_emisor=".$_SESSION['id_emisor'];
                                $res = mysqli_query($conexion, $sql);
                                while($datos = mysqli_fetch_array($res))
                                {
                                    if($datos['estatus'] == 1)
                                    {
                                        $estado = "Activo";
                                        $titulo = "Desactivar cuenta";
                                        $color = "btn-secondary";
                                        $desactive = "<i class='fas fa-times-circle'></i>";
                                        $codigo_estatus = 2;
                                    }else{
                                        $estado = "Inactivo";
                                        $titulo = "Activar cuenta";
                                        $color = "btn-success";
                                        $desactive = "<i class='fas fa-check-circle'></i>";
                                        $codigo_estatus = 1;
                                    }
                                    $html .= "
                                            <tr>
                                                <td class='text-center'>
                                                    <div class='btn-group'>
                                                        <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_cuenta(".$datos['id_cuenta'].",".$codigo_estatus.");'>
                                                            ".$desactive."
                                                        </button> &nbsp;
                                                        <button type='button' class='btn btn-danger btn-sm' title='Eliminar cuenta' onclick='eliminar_cuenta(".$datos['id_cuenta'].")'>
                                                            <i class='fas fa-trash'></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class='text-center'>".$datos['id_cuenta']."</td>
                                                <td class='text-center'>".$datos['cuenta']."</td>
                                                <td class='text-center'>".$datos['rfc_banco']."</td>
                                                <td class='text-center'>".$datos['nombre_banco']."</td>
                                                <td class='text-center'>".$estado."</td>
                                            </tr>
                                    ";
                                }
                            echo '
                                </tbody>
                            </table>
                        </div>
                    </div>
                        </div>
            </div>
        ';

        echo $html;
    }
?>
<script>
  $(function () {
    $('#cuentas_bancarias').DataTable({
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
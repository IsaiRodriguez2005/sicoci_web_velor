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
        $tabla = "";
        $consultaCatalogo = "SELECT s.id_partida, s.id_documento, s.serie, s.folio, s.codigo_postal, s.leyenda, s.estatus, d.nombre_documento FROM emisores_series s INNER JOIN _cat_erp_documentos d ON d.id_documento = s.id_documento WHERE s.id_emisor = ".$_SESSION['id_emisor']." ORDER BY id_partida ASC";
        $resCatalogo = mysqli_query($conexion, $consultaCatalogo);
        while($catalogo = mysqli_fetch_array($resCatalogo))
        {
            if($catalogo['estatus'] == 1)
            {
                $estado = "Activo";
                $titulo = "Desactivar documento";
                $color = "btn-secondary";
                $desactive = "<i class='fas fa-times-circle'></i>";
                $codigo_estatus = 2;
            }else{
                $estado = "Inactivo";
                $titulo = "Activar documento";
                $color = "btn-success";
                $desactive = "<i class='fas fa-check'></i>";
                $codigo_estatus = 1;
            }
            $tabla .= "
                <tr>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <button type='button' class='btn btn-warning btn-sm' title='Editar registro' onclick='editar_documento(".$catalogo['id_partida'].", ".$catalogo['id_documento'].", &quot;".$catalogo['serie']."&quot;, ".$catalogo['folio'].", ".$catalogo['codigo_postal'].", &quot;".$catalogo['leyenda']."&quot;)'>
                                <i class='fas fa-edit'></i>
                            </button>
                            &nbsp;
                            <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_documento(".$catalogo['id_partida'].",".$codigo_estatus.");'>
                                ".$desactive."
                            </button>
                        </div>
                    </td>
                    <td class='text-center'>".$catalogo['id_partida']."</td>
                    <td class='text-center'>".$catalogo['nombre_documento']."</td>
                    <td class='text-center'>".$catalogo['serie']."</td>
                    <td class='text-center'>".$catalogo['folio']."</td>
                    <td class='text-center'>".$catalogo['codigo_postal']."</td>
                    <td class='text-center'>".$estado."</td>
                </tr>
            ";
        }

        $html .= '
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Mis series fiscales</h3>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-file-invoice"></i> Tipo de documento</h6><hr>
                    <input type="hidden" id="tipo_gestion" value="0">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-file"></i></span>
                                </div>
                                <select class="form-control" id="documento" onfocus="resetear(&quot;documento&quot;)">
                                    <option value="0">Selecciona el tipo de documento</option>
                                    <option value="1">Ticket</option>
                                    <option value="2">Factura</option>
                                    <option value="3">Pago a proveedores</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <h6><i class="fas fa-file-invoice"></i> Datos de la serie</h6><hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Serie" id="serie" onfocus="resetear(&quot;serie&quot;)" maxlength="35">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="number" class="form-control" placeholder="Folio" id="folio" onfocus="resetear(&quot;folio&quot;)" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-street-view"></i></span>
                                </div>
                                <input type="number" class="form-control" placeholder="C&oacute;digo postal" id="codigo_postal" onfocus="resetear(&quot;codigo_postal&quot;)" onKeyup="buscar_cp()" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" min="0">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="estado" onfocus="resetear(&quot;estado&quot;)" disabled>
                                    <option value="0">Estado...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="municipio" onfocus="resetear(&quot;municipio&quot;)" disabled>
                                    <option value="0">Municipio...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <textarea class="form-control" placeholder="Informaci&oacute;n extra del documento" id="leyenda_documento" onfocus="resetear(&quot;leyenda_documento&quot;)"></textarea>
                            </div>
                        </div>
                    </div>
                    <br>
                    <center><button type="button" class="btn btn-info" onclick="gestionar_serie();" id="btn_emisor">Guardar Documento</button></center><br>
                </div>
            </div>
        ';

        $html .= '
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Catalogo de Documentos y Series Fiscales</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped" id="tabla_emisores">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Acciones</th>
                                        <th class="sticky text-center">ID</th>
                                        <th class="text-center">Documento</th>
                                        <th class="text-center">Serie</th>
                                        <th class="text-center">Folio</th>
                                        <th class="text-center">C&oacute;digo Postal</th>
                                        <th class="text-center">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$tabla.'
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
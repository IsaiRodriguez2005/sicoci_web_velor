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
        $consultaCatalogo = "SELECT id_documento, tipo_catalogo, nombre_documento, genera_vigencia, estatus FROM emisores_expediente WHERE id_emisor = ".$_SESSION['id_emisor']." ORDER BY tipo_catalogo ASC";
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

            if($catalogo['tipo_catalogo'] == 1)
            {
                $tipo = "CLIENTES";
            }
            if($catalogo['tipo_catalogo'] == 2)
            {
                $tipo = "PROVEEDORES";
            }

            if($catalogo['genera_vigencia'] == 1)
            {
                $vigencia = "SI";
            }
            if($catalogo['genera_vigencia'] == 2)
            {
                $vigencia = "NO";
            }

            $tabla .= "
                <tr>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <button type='button' class='btn btn-warning btn-sm' title='Editar registro' onclick='editar_documento_expediente(".$catalogo['id_documento'].", &quot;".$catalogo['nombre_documento']."&quot;,".$catalogo['tipo_catalogo'].",".$catalogo['genera_vigencia'].")'>
                                <i class='fas fa-edit'></i>
                            </button>
                            &nbsp;
                            <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_documento_expediente(".$catalogo['id_documento'].",".$codigo_estatus.");'>
                                ".$desactive."
                            </button>
                        </div>
                    </td>
                    <td class='text-center'>".$catalogo['id_documento']."</td>
                    <td class='text-center'>".$tipo."</td>
                    <td class='text-center'>".$catalogo['nombre_documento']."</td>
                    <td class='text-center'>".$vigencia."</td>
                    <td class='text-center'>".$estado."</td>
                </tr>
            ";
        }

        $html .= '
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Mi expediente digital</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" id="tipo_gestion" value="0">
                    <h6><i class="fas fa-file-invoice"></i> Datos del documento</h6><hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <select class="form-control" id="tipo_catalogo" onfocus="resetear(&quot;tipo_catalogo&quot;)">
                                    <option value="0">Selecciona tipo catalogo...</option>
                                    <option value="1">CLIENTES</option>
                                    <option value="2">PROVEEDORES</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-file"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Nombre del documento para a&ntilde;adir al expediente" id="nombre" onfocus="resetear(&quot;nombre&quot;)" maxlength="75">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                <select class="form-control" id="vigencia" onfocus="resetear(&quot;vigencia&quot;)">
                                    <option value="0">Genera vigencia...</option>
                                    <option value="1">SI</option>
                                    <option value="2">NO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <center><button type="button" class="btn btn-info" onclick="gestionar_expediente();">Guardar Documento</button></center><br>
                </div>
            </div>
        ';

        $html .= '
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Catalogo de Documentos para Expediente Digital</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped" id="tabla_emisores">
                                <thead>
                                    <tr>
                                        <th class="sticky text-center">Acciones</th>
                                        <th class="sticky text-center">ID</th>
                                        <th class="text-center">CATALOGO</th>
                                        <th class="text-center">Documento</th>
                                        <th class="text-center">Vigencia</th>
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
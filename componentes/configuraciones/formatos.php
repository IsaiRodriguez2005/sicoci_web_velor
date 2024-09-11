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
        $sqlCotizaciones = "SELECT id_cotizacion, terminos, observaciones, codigo_calidad, nombre_autoriza, hoja_membretada, firma_autoriza, mostrar_cuentas FROM emisores_cotizaciones WHERE id_emisor = ".$_SESSION['id_emisor'];
        $resCotizaciones = mysqli_query($conexion, $sqlCotizaciones);
        $cotizaciones = mysqli_fetch_array($resCotizaciones);

        if($cotizaciones['id_cotizacion'] == "")
        {
            $id_cotizacion = 0;
        }
        else
        {
            $id_cotizacion = $cotizaciones['id_cotizacion'];
        }

        if($cotizaciones['hoja_membretada'] == 1)
        {
            $imagen_hoja = "emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_cotizacion.png";
            $boton_eliminar_hoja = '
                <button type="button" class="btn btn-danger btn-sm" title="Eliminar hoja membretada" onclick="eliminar_hf('.$cotizaciones['id_cotizacion'].', 1)">
                    <i class="fas fa-trash"></i>
                </button>
            ';
        }
        else
        {
            $imagen_hoja = "";
            $boton_eliminar_hoja = "";
        }

        if($cotizaciones['firma_autoriza'] == 1)
        {
            $imagen_firma = "emisores/".$_SESSION['id_emisor']."/archivos/generales/firma_cotizacion.png";
            $boton_eliminar_firma = '
                <button type="button" class="btn btn-danger btn-sm" title="Eliminar firma de autorización" onclick="eliminar_hf('.$cotizaciones['id_cotizacion'].', 2)">
                    <i class="fas fa-trash"></i>
                </button>
            ';
        }
        else
        {
            $imagen_firma = "";
            $boton_eliminar_firma = "";
        }

        if($cotizaciones['firma_autoriza'] == 1)
        {
            $firma = "firma_cotizacion.png";
        }
        else
        {
            $firma = "";
        }

        if($cotizaciones['mostrar_cuentas'] == 1)
        {
            $cuentas = "checked";
        }
        else
        {
            $cuentas = "";
        }

        //////////////////////////////////////////////////// PARA MINUTAS
        $sqlMinutas = "SELECT id_minuta, codigo_calidad, hoja_membretada FROM emisores_minutas WHERE id_emisor = ".$_SESSION['id_emisor'];
        $resMinutas = mysqli_query($conexion, $sqlMinutas);
        $minutas = mysqli_fetch_array($resMinutas);

        if($minutas['id_minuta'] == "")
        {
            $id_minuta = 0;
        }
        else
        {
            $id_minuta = $minutas['id_minuta'];
        }

        if($minutas['hoja_membretada'] == 1)
        {
            $imagen_hoja_minuta = "emisores/".$_SESSION['id_emisor']."/archivos/generales/hoja_minuta.png";
            $boton_eliminar_hoja_minuta = '
                <button type="button" class="btn btn-danger btn-sm" title="Eliminar hoja membretada para minuta" onclick="eliminar_m('.$minutas['id_minuta'].')">
                    <i class="fas fa-trash"></i>
                </button>
            ';
        }
        else
        {
            $imagen_hoja_minuta = "";
            $boton_eliminar_hoja_minuta = "";
        }

        $html .= '
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Personalizaci&oacute;n de formatos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-5 col-sm-3">
                            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-cotizaciones" role="tab" aria-controls="vert-tabs-home" aria-selected="true">Cotizaciones</a>
                                <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-minutas" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">Minutas</a>
                            </div>
                        </div>
                        <div class="col-7 col-sm-9">
                            <div class="tab-content" id="vert-tabs-tabContent">
                                <div class="tab-pane text-left fade show active" id="vert-tabs-cotizaciones" role="tabpanel" aria-labelledby="vert-tabs-cotizaciones-tab">
                                    <input type="hidden" id="id_cotizacion" value="'.$id_cotizacion.'">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                                </div>
                                                <textarea class="form-control" placeholder="T&eacute;rminos y condiciones" id="terminos" onfocus="resetear(&quot;terminos&quot;)" rows="4">'.$cotizaciones['terminos'].'</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-eye"></i></span>
                                                </div>
                                                <textarea class="form-control" placeholder="Observaciones" id="observaciones" onfocus="resetear(&quot;observaciones&quot;)" rows="4">'.$cotizaciones['observaciones'].'</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-code"></i></span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="C&oacute;digo calidad" id="calidad" onfocus="resetear(&quot;calidad&quot;)" value="'.$cotizaciones['codigo_calidad'].'">
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Nombre de quien autoriza" id="nombre_autoriza" onfocus="resetear(&quot;nombre_autoriza&quot;)" value="'.$cotizaciones['nombre_autoriza'].'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" id="mostrar_cb" '.$cuentas.'>
                                                    <label for="mostrar_cb">Mostrar cuentas bancarias en cotizaci&oacute;n</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="hoja_membretada" accept="image/png" onfocus="resetear(&quot;hoja_membretada&quot;)">
                                                        <label class="custom-file-label" for="hoja_membretada" id="archivo">Buscar hoja membretada...</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="firma_autoriza" accept="image/png" onfocus="resetear(&quot;firma_autoriza&quot;)">
                                                        <label class="custom-file-label" for="firma_autoriza" id="archivo">Buscar firma autoriza...</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6" id="mostrar_hoja">
                                            <center>
                                                '.$boton_eliminar_hoja.'<br>
                                                <img src="'.$imagen_hoja.'" width="75px" heigth="125px">
                                            </center>
                                        </div>
                                        <div class="col-6" id="mostrar_firma">
                                            <center>
                                                '.$boton_eliminar_firma.'<br>
                                                <img src="'.$imagen_firma.'" width="75px" heigth="75px">
                                            </center>
                                        </div>
                                    </div>
                                    <br><center><button type="button" class="btn btn-info" onclick="guardar_formato_cotizacion();">Guardar Cambios</button></center><br>
                                </div>
                                <div class="tab-pane fade" id="vert-tabs-minutas" role="tabpanel" aria-labelledby="vert-tabs-minutas-tab">
                                    <div class="row">
                                        <div class="col-6">
                                        <input type="hidden" id="id_minuta" value="'.$id_minuta.'">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="hoja_membrete" accept="image/png" onfocus="resetear(&quot;hoja_membrete&quot;)" value="'.$minuta.'">
                                                        <label class="custom-file-label" for="hoja_membrete" id="archivo">Buscar hoja membretada...</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-code"></i></span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="C&oacute;digo calidad" id="calidad_minuta" onfocus="resetear(&quot;calidad_minuta&quot;)" value="'.$minutas['codigo_calidad'].'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6" id="mostrar_hoja_minuta">
                                            <center>
                                                '.$boton_eliminar_hoja_minuta.'<br>
                                                <img src="'.$imagen_hoja_minuta.'" width="100px" heigth="175px">
                                            </center>
                                        </div>
                                    </div>
                                    <br><center><button type="button" class="btn btn-info" onclick="guardar_formato_minuta();">Guardar Cambios</button></center><br>
                                </div>
                            </div>
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
        bsCustomFileInput.init();
    });
</script>
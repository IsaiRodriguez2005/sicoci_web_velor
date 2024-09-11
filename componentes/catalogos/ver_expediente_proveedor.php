<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

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
        $sql = "SELECT e.id_documento, e.nombre_documento, e.genera_vigencia, a.id_archivo, a.id_cliente, a.upload, a.vigencia FROM emisores_expediente e LEFT JOIN emisores_expediente_archivos a ON e.id_documento = a.id_documento AND e.id_emisor = a.id_emisor AND a.id_cliente = ".$_POST['id_proveedor']." WHERE e.id_emisor=".$_SESSION['id_emisor']." AND e.tipo_catalogo = 2 ORDER BY e.nombre_documento ASC";
        $res = mysqli_query($conexion, $sql);
        $mosaicos.="<div class='row'>";
        while($datos = mysqli_fetch_array($res))
        {
            if($datos['upload'] == NULL || $datos['upload'] == 0)
            {
                $color = "danger";
                $icono = "eye-slash";
                $titulo = "Archivo sin cargar";
                $disabled_eliminar = "disabled";
                $disabled_ver = "disabled";
            }
            else
            {
                $color = "success";
                $icono = "eye";
                $titulo = "Ver archivo";
                $disabled_eliminar = "";
                $disabled_ver = "";
            }
            if($datos['genera_vigencia'] == 1)
            {
                $boton_vigencia = "
                    Vigencia: 
                    <input type='date' class='form-control' id='fecha_".$datos['id_documento']."' onfocus='resetear(&quot;fecha_".$datos['id_documento']."&quot;)' value='".$datos['vigencia']."'>
                ";
                $indicador = 1;
            }
            else
            {
                $boton_vigencia = "";
                $indicador = 0;
            }
            if($datos['id_archivo'] == NULL)
            {
                $archivo_id = 0;
            }
            else
            {
                $archivo_id = $datos['id_archivo'];
            }
            if($datos['id_cliente'] == NULL)
            {
                $cliente_id = 0;
            }
            else
            {
                $cliente_id = $datos['id_cliente'];
            }

            $mosaicos.="
                <div class='col-md-4 col-sm-6 col-12'>
                    <div class='info-box shadow'>
                        <span id='span_".$datos['id_documento']."' class='info-box-icon bg-".$color."' title='".$titulo."'>
                            <a href='#' onclick='ver_archivo(".$cliente_id.",&quot;".$datos['nombre_documento']."&quot;,".$_SESSION['id_emisor'].",".$datos['upload'].")'>
                                <i id='i_".$datos['id_documento']."' class='far fa-".$icono."'></i>
                            </a>
                        </span>
                        <div class='info-box-content'>
                            <span class='info-box-text'>".$datos['nombre_documento']."</span>
                            <span class='info-box-number'>
                                ".$boton_vigencia."
                                <div class='custom-file'>
                                    <input type='file' class='custom-file-input' id='archivo_".$datos['id_documento']."' accept='.pdf' onfocus='resetear(&quot;archivo_".$datos['id_documento']."&quot;)'>
                                    <label class='custom-file-label' for='archivo_".$datos['id_documento']."'>Selecciona documento...</label>
                                </div>
                            </span><br>
                            <div class='btn-group'>
                                <button type='button' class='btn btn-primary' title='Subir informaci&oacute;n' onclick='subir_info(".$datos['id_documento'].",".$indicador.",".$archivo_id.",&quot;".$datos['nombre_documento']."&quot;)'><i class='fas fa-upload'></i></button>
                                <button type='button' id='btn_eliminar_".$datos['id_documento']."' class='btn btn-danger' title='Eliminar archivo' onclick='eliminar_archivo(".$datos['id_documento'].",".$archivo_id.",".$datos['id_cliente'].",&quot;".$datos['nombre_documento']."&quot;)' ".$disabled_eliminar."><i class='fas fa-trash'></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        $mosaicos.="</div>";

        echo $mosaicos;
    }
?>
<script>
    $(function () {
        bsCustomFileInput.init();
    });
</script>
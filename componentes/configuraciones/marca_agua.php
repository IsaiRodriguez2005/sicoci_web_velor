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
        $boton_eliminar = "";
        $html = "";
        $sql = "SELECT marca, tipo_marca FROM emisores_configuraciones WHERE id_emisor = ".$_SESSION['id_emisor'];
        $reslSQL = mysqli_query($conexion, $sql);
        $emisor = mysqli_fetch_array($reslSQL);

        if($emisor['marca'] == 0)
        {
            $imagen = "img/no-image.png";
        }
        else
        {
            $imagen = "emisores/".$_SESSION['id_emisor']."/archivos/generales/marca.png";
            $boton_eliminar = '
                <button type="button" class="btn btn-danger btn-sm" title="Eliminar marca de agua" onclick="eliminar_lm(2)">
                    <i class="fas fa-trash"></i>
                </button>
            ';
        }

        switch($emisor['tipo_marca'])
        {
            case 0:
                $opciones = '
                    <option value="0" selected>Selecciona tipo de marca de agua</option>
                    <option value="1">Cuadrado</option>
                    <option value="2">Rectangular</option>
                    <option value="3">Rectangular vertical</option>
                ';
                $ancho = "150px";
                $alto = "150px";
                break;
            case 1:
                $opciones = '
                    <option value="0">Selecciona tipo de marca de agua</option>
                    <option value="1" selected>Cuadrado</option>
                    <option value="2">Rectangular</option>
                    <option value="3">Rectangular vertical</option>
                ';
                $ancho = "150px";
                $alto = "150px";
                break;
            case 2:
                $opciones = '
                    <option value="0">Selecciona tipo de marca de agua</option>
                    <option value="1">Cuadrado</option>
                    <option value="2" selected>Rectangular</option>
                    <option value="3">Rectangular vertical</option>
                ';
                $ancho = "200px";
                $alto = "150px";
                break;
            case 3:
                $opciones = '
                    <option value="0">Selecciona tipo de marca de agua</option>
                    <option value="1">Cuadrado</option>
                    <option value="2">Rectangular</option>
                    <option value="3" selected>Rectangular vertical</option>
                ';
                $ancho = "150px";
                $alto = "200px";
                break;
        }

        $html .= '
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Marca de agua</h3>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-image"></i> Sube tu marca de agua</h6><hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="marca" accept="image/png" onfocus="resetear(&quot;marca&quot;)">
                                        <label class="custom-file-label" for="logo" id="archivo">Buscar mara de agua...</label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                                </div>
                                <select class="form-control" id="tipo" onfocus="resetear(&quot;tipo&quot;)">
                                    '.$opciones.'
                                </select>
                            </div>
                            <center><button type="button" class="btn btn-info" onclick="cargar_marca();" id="btn_emisor">Guardar marca de agua</button></center><br>
                        </div>
                        <div class="col-6" id="mostrar_lm">
                            <center>
                                '.$boton_eliminar.'<br>
                                <img src="'.$imagen.'" width="'.$ancho.'" heigth="'.$alto.'">
                            </center>
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
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
        $pass = "";
        $html = "";
        $sql = "SELECT sello_cer, sello_key, password, sello_vigencia FROM emisores_configuraciones WHERE id_emisor = ".$_SESSION['id_emisor'];
        $reslSQL = mysqli_query($conexion, $sql);
        $emisor = mysqli_fetch_array($reslSQL);

        if($emisor['sello_cer'] == 0)
        {
            $cer = "";
        }
        else
        {
            $cer = "<i class='far fa-check-circle'></i> El CSD del sello digital se encuentra almacenado correctamente";
        }
        if($emisor['sello_key'] == 0)
        {
            $key = "";
        }
        else
        {
            $key = "<i class='far fa-check-circle'></i> El key del sello digital se encuentra almacenado correctamente";
        }

        $html .= '
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title" id="leyenda">Sello digital</h3>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-file"></i> Sube tu sello digital</h6><hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="certificado" accept=".cer" onfocus="resetear(&quot;certificado&quot;)">
                                                <label class="custom-file-label" for="certificado" id="archivo">Certificado del csd...</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="key" accept=".key" onfocus="resetear(&quot;key&quot;)">
                                                <label class="custom-file-label" for="key" id="archivo">Llave del csd...</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                        </div>
                                        <input type="password" class="form-control" placeholder="Escribe password" id="password" onfocus="resetear(&quot;password&quot;)" value="'.$emisor['password'].'">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" placeholder="Vigencia CSD" id="fecha" onfocus="resetear(&quot;fecha&quot;)" value="'.$emisor['sello_vigencia'].'">
                                </div>
                            </div>
                            <center><button type="button" class="btn btn-info" onclick="cargar_sello();" id="btn_emisor">Guardar sello digital</button></center><br>
                        </div>
                        <div class="col-6">
                            <center>
                                '.$cer.'<br>
                                '.$key.'<br>
                                '.$pass.'<br>
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
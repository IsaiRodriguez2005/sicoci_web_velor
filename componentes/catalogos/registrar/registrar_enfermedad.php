<?php
    session_start();
    require("../../conexion.php");
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
        $caracteres = array("&", '"', "'");
        $reemplazo = array("&amp;", "&quot;", "&apos;");

        if($_POST['nombre_enfermedad'])
        {
            $checkEnfermedad = "SELECT COUNT(*) as count FROM emisores_enfermedades WHERE id_emisor = ".$_SESSION['id_emisor'] ." AND nombre = '".strtoupper($_POST['nombre_enfermedad'])."'";
            $resultado =  mysqli_query($conexion, $checkEnfermedad);
            $resultado = mysqli_fetch_array($resultado);

            if($resultado['count'] > 0){
                echo 'ex';
            } else {
                $selectMAX = "SELECT COALESCE(MAX(id_enfermedad),0) AS no_registro FROM emisores_enfermedades WHERE id_emisor = ".$_SESSION['id_emisor'];
                $resMAX = mysqli_query($conexion, $selectMAX);
                $max = mysqli_fetch_array($resMAX);
                $ultimo = $max['no_registro'] + 1;
    
                $insertenfermedades = "INSERT INTO emisores_enfermedades VALUES(".$ultimo.", ".$_SESSION['id_emisor'].", '".strtoupper($_POST['nombre_enfermedad'])."',1)";
                $resultado=mysqli_query($conexion, $insertenfermedades);
                if($resultado)
                {
                    echo "ok";
                }
                else
                {
                    echo "e";
                }
            }
            
        }
        else
        {

        }
    }
?>
<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');
    error_reporting(0);

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
        $nuevo_social = str_replace($caracteres, $reemplazo, strtoupper($_POST['nombre_social']));

        if($_POST['id_perfil'] == 0)
        {

            $fecha_alta = date("Y-m-d");
            
            $selectMAX = "SELECT COALESCE(MAX(id_perfil),0) AS no_registro FROM emisores_clientes_facturacion WHERE id_emisor=".$_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;
            

            $insertCliente = "INSERT INTO emisores_clientes_facturacion VALUES(".$ultimo.", 
                                                                            ".$_POST['id_cliente'].", 
                                                                            ".$_SESSION['id_emisor'].",
                                                                            '".strtoupper($_POST['rfc'])."',
                                                                            '".trim($nuevo_social)."',
                                                                            '".strtoupper($_POST['calle'])."',
                                                                            '".strtolower($_POST['no_exterior'])."',
                                                                            '".strtolower($_POST['no_interior'])."',
                                                                            '".strtolower($_POST['codigo_postal'])."',
                                                                            '".strtolower($_POST['colonia'])."',
                                                                            '".strtolower($_POST['municipio'])."',
                                                                            '".strtolower($_POST['estado'])."',
                                                                            '".strtolower($_POST['pais'])."',
                                                                            '".strtolower($_POST['regimen_fiscal'])."',
                                                                            '".strtolower($_POST['metodo_pago'])."',
                                                                            '".strtolower($_POST['forma_pago'])."',
                                                                            '".strtolower($_POST['uso_cfdi'])."',
                                                                            '".$fecha_alta."',
                                                                            1)";
            $resultado=mysqli_query($conexion, $insertCliente);
            if($resultado)
            {
                echo $ultimo;
            }
            else
            {
                echo "2";
            }
        }
        else
        {
            $updateCliente = "UPDATE emisores_clientes_facturacion SET 
                                                                        rfc='".trim($_POST['rfc'])."',  
                                                                        nombre_social='".strtoupper($_POST['nombre_social'])."', 
                                                                        calle='".strtolower($_POST['calle'])."', 
                                                                        no_exterior='".strtolower($_POST['no_exterior'])."', 
                                                                        no_interior='".strtolower($_POST['no_interior'])."', 
                                                                        codigo_postal='".strtolower($_POST['codigo_postal'])."', 
                                                                        colonia='".strtolower($_POST['colonia'])."', 
                                                                        municipio='".strtolower($_POST['municipio'])."', 
                                                                        estado='".strtolower($_POST['estado'])."', 
                                                                        pais='".strtolower($_POST['pais'])."', 
                                                                        regimen_fiscal='".strtolower($_POST['regimen_fiscal'])."', 
                                                                        metodo_pago='".strtolower($_POST['metodo_pago'])."', 
                                                                        forma_pago='".strtolower($_POST['forma_pago'])."', 
                                                                        uso_cfdi='".strtolower($_POST['uso_cfdi'])."' 
                                                                    WHERE 
                                                                        id_cliente=".$_POST['id_cliente']." AND 
                                                                        id_emisor=".$_SESSION['id_emisor'] ." AND 
                                                                        id_perfil = ".$_POST['id_perfil'].";";
            $resultado=mysqli_query($conexion, $updateCliente);
            if($resultado)
            {
                echo '1';
            }
            else
            {
                echo "2";
            }
            
        }
    }
?>


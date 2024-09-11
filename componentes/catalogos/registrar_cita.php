<?php
session_start();
require("../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $caracteres = array("&", '"', "'");
    $reemplazo = array("&amp;", "&quot;", "&apos;");
    $nuevo_social = str_replace($caracteres, $reemplazo, strtoupper($_POST['id_cliente']));

    if ($_POST['tipo_gestion'] == 0) {

        // primera validacion, ¿hay alguna cita para el cliente mismo dia y misma hora?
        $hay_cita = "SELECT * FROM emisores_agenda WHERE id_cliente = " . intval($_POST['id_cliente']) . " AND fecha_agenda = '" . $_POST['fecha_hora'] . "'";
        $resultado = mysqli_query($conexion, $hay_cita);
        $filas = mysqli_fetch_assoc($resultado);

        if (!$filas) { // validacion de cliente y hora

            // segunda validacion, ¿esta ocuado el conultorio el dia y misma hora?
            $consultorio_ocupado = "SELECT * FROM emisores_agenda WHERE id_consultorio = " . intval($_POST['id_consultorio']) . " AND fecha_agenda = '" . $_POST['fecha_hora'] . "'";
            $resultado = mysqli_query($conexion, $consultorio_ocupado);
            $filas = mysqli_fetch_assoc($resultado);

            if (!$filas) { // validacion de consultorio ocupado

                // tercera validacion, ¿esta ocupado el terapeuta el dia y misma hora?
                $terapeuta_ocupado = "SELECT * FROM emisores_agenda WHERE id_terapeuta = " . intval($_POST['id_terapeuta']) . " AND fecha_agenda = '" . $_POST['fecha_hora'] . "'";
                $resultado = mysqli_query($conexion, $terapeuta_ocupado);
                $filas = mysqli_fetch_assoc($resultado);

                if (!$filas) { // validacion de terapeuta ocupado

                    $fecha_alta = date("Y-m-d");

                    $selectMAX = "SELECT COALESCE(MAX(id_folio),0) AS no_registro FROM emisores_agenda WHERE id_emisor =" . $_SESSION['id_emisor'];
                    $resMAX = mysqli_query($conexion, $selectMAX);
                    $max = mysqli_fetch_array($resMAX);
                    $ultimo = $max['no_registro'] + 1;

                    $insertCliente = "INSERT INTO emisores_agenda VALUES( " . $_SESSION['id_emisor'] . "," . $ultimo . "," . intval($_POST['id_cliente']) . "," . intval($_POST['id_consultorio']) . "," . intval($_POST['id_terapeuta']) . "," . intval($_POST['tipo_servicio']) . "," . intval($_POST['tipo_cita']) . ",'" . $fecha_alta . "','" . strtoupper(trim($_POST['fecha_hora'])) . "','" . strtoupper($_POST['observaciones']) . "', 2)";
                    $resultado = mysqli_query($conexion, $insertCliente);
                    if ($resultado) {
                        echo "ok";
                    } else {
                        echo "error";
                    }
                } else {
                    echo 3; // validacion de terapeuta ocupado
                }
            } else {
                echo 2; // validacion de consultorio ocupado
            }
        } else {
            echo 1; // validacion de cliente y hora 
        }
    } else {
            $updateCliente = "UPDATE emisores_clientes SET rfc='".strtoupper(trim($_POST['rfc']))."', nombre_social='".trim($nuevo_social)."', calle='".strtoupper(trim($_POST['calle']))."', no_exterior='".strtoupper(trim($_POST['no_exterior']))."', no_interior='".strtoupper(trim($_POST['no_interior']))."', codigo_postal='".strtoupper($_POST['codigo_postal'])."', colonia='".strtoupper(trim($_POST['colonia']))."', municipio='".strtoupper($_POST['municipio'])."', estado='".strtoupper($_POST['estado'])."', pais='".strtoupper($_POST['pais'])."', regimen_fiscal='".strtoupper($_POST['regimen'])."', metodo_pago='".strtoupper($_POST['metodo_pago'])."', forma_pago='".strtoupper($_POST['forma_pago'])."', uso_cfdi='".strtoupper($_POST['uso_cfdi'])."', correo='".strtolower($_POST['correo'])."', telefono='".$_POST['telefono']."', tipo_cliente=".$_POST['tipo_cliente']." WHERE id_cliente=".$_POST['id_cliente']." AND id_emisor=".$_SESSION['id_emisor'];
            $resultado=mysqli_query($conexion, $updateCliente);
            if($resultado)
            {
                echo "ok";
            }
            else
            {
                echo "error";
            }
    }
}
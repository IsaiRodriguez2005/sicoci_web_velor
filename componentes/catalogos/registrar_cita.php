<?php
session_start();
require("../conexion.php");
include '../correos/enviar_correo.php';

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


                        $datosCliente = getDatosClientes(intval($_POST['id_cliente']), $conexion);

                        $datosTerap = getDatosTerapeuta(intval($_POST['id_terapeuta']), $conexion);

                        $datosConsultorio =  getDatosConsultorio(intval($_POST['id_consultorio']), $conexion);


                        if ($datosCliente['correo']) {
                            $fecha = obtenerFechaEspaniol($_POST['fecha_hora']);
                            $asunto = 'CITA AGENDADA!';
                            $mensaje = '
                                Que tal <b>' . strtoupper($datosCliente['nombre_cliente']) . '</b>.<br><br>
                                Para confirmar el registro de la cita con el fisioterapeuta <b>' . $datosTerap['nombre_personal'] . '</b> para el día <b>' . $fecha['dia'] . ' '.$fecha['num_dia'].' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b> en el <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                            $correo = enviarCorreo($datosCliente['correo'], $asunto, $mensaje);
                        }

                        if ($datosTerap['correo']) {

                            $fecha = obtenerFechaEspaniol($_POST['fecha_hora']);
                            $asunto = 'CITA AGENDADA!';
                            $mensaje = '
                                Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>,.<br><br>
                                El cliente/paciente <b>' . $datosCliente['nombre_cliente'] . '</b> agendó una cita contigo para el día <b>' . $fecha['dia'] . ' '.$fecha['num_dia'].' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b> en el <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                                <b>Observaciones:</b> '.strtoupper($_POST['observaciones']).'
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                            $correo = enviarCorreo($datosCliente['correo'], $asunto, $mensaje);
                        }

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
    }
}

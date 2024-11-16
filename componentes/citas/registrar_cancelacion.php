<?php
session_start();
require("../conexion.php");
require('../correos/enviar_correo.php');
date_default_timezone_set('America/Mexico_City');
error_reporting(0);

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $fecha_cancelado = date('Y-m-d');
    $sql_cancelacion = "INSERT INTO emisores_cancelaciones VALUES(" . $_SESSION['id_emisor'] . ", 0, " . $_POST['id_folio'] . ", '" . $fecha_cancelado . "', 0, 0, 0, 0, '" . strtoupper($_POST['motivo']) . "', 1)";
    $cancelado = mysqli_query($conexion, $sql_cancelacion);
    if ($cancelado) {
        $sql_estatus = "UPDATE emisores_agenda SET estatus = 2 WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_folio = " . $_POST['id_folio'];
        $estatus = mysqli_query($conexion, $sql_estatus);
        if ($estatus) {

            $datosAgenda = getDatosAgenda($_POST['id_folio'], $conexion);
            
            $datosCliente = getDatosClientes(intval($datosAgenda['id_cliente']), $conexion);

            $datosTerap = getDatosTerapeuta(intval($datosAgenda['id_terapeuta']), $conexion);

            $datosConsultorio =  getDatosConsultorio(intval($datosAgenda['id_consultorio']), $conexion);

            if ($datosCliente['correo']) {
                
                $fecha = obtenerFechaEspaniol($datosAgenda['fecha_agenda']);

                $asunto = 'CITA CANCELADA!';
                $mensaje = '
                        Que tal <b>' . strtoupper($datosCliente['nombre_cliente']) . '</b>.<br><br>
                        Tu cita agendada el día para el día <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                        a las <b>' . $fecha['hora'] . '</b> ha sido <b>CANCELADA</b> <br><br>
                        PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                        ';
                $correo = enviarCorreo($datosCliente['correo'], $asunto, $mensaje);
            }

            if ($datosTerap['correo']) {

                $fecha = obtenerFechaEspaniol($datosAgenda['fecha_agenda']);

                $asunto = 'CITA CANCELADA!';
                $mensaje = '
                        Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>,.<br><br>
                        El cliente/paciente: <b>' . $datosCliente['nombre_cliente'] . '</b>  ha <b>CANCELADO</b> la cita agendada contigo para el día <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                        a las <b>' . $fecha['hora'] . '</b> en el <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                        PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                        ';
                $correo = enviarCorreo($datosTerap['correo'], $asunto, $mensaje);
            }
            // print_r($fecha);
            echo "ok";
        } else {
            echo "error2";
        }
    } else {
        echo "error";
    }
}

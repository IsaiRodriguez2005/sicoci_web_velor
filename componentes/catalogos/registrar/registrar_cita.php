<?php
session_start();
require("../../conexion.php");
include '../../correos/enviar_correo.php';

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

    $fecha_hora = $_POST['fecha_cita'] . 'T' . $_POST['hora_cita'];

    if (!$_POST['tipo_gestion']) {

        $id_cliente = intval($_POST['id_cliente']);
        $id_consultorio = intval($_POST['id_consultorio']);
        $id_terapeuta = intval($_POST['id_terapeuta']);
        $id_emisor = $_SESSION['id_emisor'];
        $tipo_servicio = intval($_POST['tipo_servicio']);
        $tipo_cita = intval($_POST['tipo_cita']);
        $observaciones = strtoupper($_POST['observaciones']);

        $validacion = "SELECT 
                            CASE 
                                WHEN COUNT(ag.id_cliente) > 0 THEN 'cliente_ocupado'
                                WHEN COUNT(con.id_consultorio) > 0 THEN 'consultorio_ocupado'
                                WHEN COUNT(ter.id_terapeuta) > 0 THEN 'terapeuta_ocupado'
                                ELSE 'disponible'
                            END AS estado
                        FROM 
                            emisores_agenda ag
                            LEFT JOIN emisores_agenda con ON ag.fecha_agenda = con.fecha_agenda AND con.id_consultorio = ?
                            LEFT JOIN emisores_agenda ter ON ag.fecha_agenda = ter.fecha_agenda AND ter.id_terapeuta = ?
                        WHERE 
                            (ag.id_cliente = ? AND ag.fecha_agenda = ?)
                            OR (con.fecha_agenda = ? AND con.id_consultorio = ?)
                            OR (ter.fecha_agenda = ? AND ter.id_terapeuta = ?);";

        $stmt = mysqli_prepare($conexion, $validacion);

        mysqli_stmt_bind_param(
            $stmt,
            "iiissisi",  //* referenciamos el tipo de dato de cada variale
            $id_consultorio,
            $id_terapeuta, // Parámetros para LEFT JOIN
            $id_cliente,
            $fecha_hora,      // Parámetros para cliente ocupado
            $fecha_hora,
            $id_consultorio,  // Parámetros para consultorio ocupado
            $fecha_hora,
            $id_terapeuta     // Parámetros para terapeuta ocupado
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $fila = mysqli_fetch_assoc($result);



        switch ($fila['estado']) {
            case 'cliente_ocupado':
                echo json_encode(['mensaje' => 'clo']); // Cliente ocupado
                exit;
            case 'consultorio_ocupado':
                echo json_encode(['mensaje' => 'co']); // Consultorio ocupado
                exit;
            case 'terapeuta_ocupado':
                echo json_encode(['mensaje' => 'to']); // Terapeuta ocupado
                exit;
            case 'disponible':

                $fecha_alta = date("Y-m-d");

                $selectMAX = "SELECT COALESCE(MAX(id_folio),0) AS no_registro FROM emisores_agenda WHERE id_emisor =" . $_SESSION['id_emisor'];
                $resMAX = mysqli_query($conexion, $selectMAX);
                $max = mysqli_fetch_array($resMAX);
                $ultimo = $max['no_registro'] + 1;

                $insertCliente = "INSERT INTO emisores_agenda (
                                                                `id_emisor`, 
                                                                `id_folio`, 
                                                                `id_cliente`, 
                                                                `id_consultorio`, 
                                                                `id_terapeuta`, 
                                                                `tipo_servicio`, 
                                                                `tipo_cita`, 
                                                                `fecha_emision`, 
                                                                `fecha_agenda`, 
                                                                `observaciones`, 
                                                                `estatus`, 
                                                                `conf_ct_ter`) VALUES ( ?,?,?,?,?,?,?,?,?,?,2,0);";
                $stmt = mysqli_prepare($conexion, $insertCliente);
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiiiiiisss",  //* referenciamos el tipo de dato de cada variale
                    $id_emisor,
                    $ultimo,
                    $id_cliente,
                    $id_consultorio,  // Parámetros para consultorio ocupado
                    $id_terapeuta, // Parámetros para LEFT JOIN
                    $tipo_servicio,      // Parámetros para cliente ocupado
                    $tipo_cita,
                    $fecha_alta,
                    strtoupper(trim($fecha_hora)),
                    $observaciones    // Parámetros para terapeuta ocupado
                );
                
                if (mysqli_stmt_execute($stmt)) {

                    $datosCliente = getDatosClientes(intval($_POST['id_cliente']), $conexion);
                    $datosTerap = getDatosTerapeuta(intval($_POST['id_terapeuta']), $conexion);
                    $datosConsultorio =  getDatosConsultorio(intval($_POST['id_consultorio']), $conexion);
                    $idCliente = $_POST['id_cliente'];
                    $idTerapeuta = $_POST['id_terapeuta'];

                    //* Correo Cliente
                    if ($datosCliente['correo']) {
                        $fecha = obtenerFechaEspaniol($fecha_hora);
                        $asunto = 'CITA AGENDADA!';
                        if (intval($_POST['tipo_servicio']) == 1) {
                            $mensaje = '
                                Que tal <b>' . strtoupper($datosCliente['nombre_cliente']) . '</b>.<br><br>
                                Para confirmar el registro de la cita con el fisioterapeuta <b>' . $datosTerap['nombre_personal'] . '</b> para el día <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b> en el <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                        } else {
                            $mensaje = '
                                Que tal <b>' . strtoupper($datosCliente['nombre_cliente']) . '</b>.<br><br>
                                Para confirmar el registro de la cita con el fisioterapeuta <b>' . $datosTerap['nombre_personal'] . '</b> para el día <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b> en su <b>DOMICILIO</b> <br><br>
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                        }

                        $correo = enviarCorreo($datosCliente['correo'], $asunto, $mensaje);
                    }

                    //* Correo Terapeuta */
                    if ($datosTerap['correo']) {

                        $fecha = obtenerFechaEspaniol($fecha_hora);
                        $asunto = 'CITA AGENDADA!';
                        if (intval($_POST['tipo_servicio']) == 1) {
                            $mensaje = '
                                Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>,.<br><br>
                                El cliente/paciente <b>' . $datosCliente['nombre_cliente'] . '</b> agendó una cita contigo para el día <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b> en <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                                <b>Observaciones:</b> ' . strtoupper($_POST['observaciones']) . '
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                        } else {
                            $mensaje = '
                                Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>,.<br><br>
                                El cliente/paciente <b>' . $datosCliente['nombre_cliente'] . '</b> agendó una cita contigo para el día <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b> en su <b>DOMICILIO</b> <br><br>
                                <b>Observaciones:</b> ' . strtoupper($_POST['observaciones']) . '
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                        }
                        $correo = enviarCorreo($datosTerap['correo'], $asunto, $mensaje, $ultimo, idTerapeuta: $idTerapeuta);
                    }

                    $respuesta = [
                        'id_folio' => $ultimo,
                        'actualizacion' => false,
                        'mensaje' => '',
                        'correo' => $correo,
                    ];

                    echo json_encode($respuesta);
                } else {
                    $respuesta = [
                        'mensaje' => "error"
                    ];
                    echo json_encode($respuesta);
                }
                break;



            default:
                echo json_encode(['mensaje' => 'error en validación']);
                exit;
        }

        return;
    } else {

        $sqlUpdate = "UPDATE emisores_agenda SET 
                                    id_cliente=" . intval($_POST['id_cliente']) . ", 
                                    id_consultorio=" . intval($_POST['id_consultorio']) . ", 
                                    id_terapeuta=" . intval($_POST['id_terapeuta']) . ", 
                                    tipo_servicio=" . intval($_POST['tipo_servicio']) . ", 
                                    tipo_cita=" . intval($_POST['tipo_cita']) . ", 
                                    fecha_agenda='" . $fecha_hora . "', 
                                    observaciones='" . strtoupper($_POST['observaciones']) . "',
                                    conf_ct_ter = 0
                                        WHERE 
                                    id_emisor = " . intval($_SESSION['id_emisor']) . " 
                                        AND 
                                    id_folio=" . intval($_POST['tipo_gestion']);
        mysqli_query($conexion, $sqlUpdate);

        if (mysqli_affected_rows($conexion) > 0) {

            $idCliente = $_POST['id_cliente'];
            $idTerapeuta = $_POST['id_terapeuta'];
            $idFolio = $_POST['tipo_gestion'];

            $datosCliente = getDatosClientes(intval($idCliente), $conexion);

            $datosTerap = getDatosTerapeuta(intval($idTerapeuta), $conexion);

            $datosConsultorio =  getDatosConsultorio(intval($_POST['id_consultorio']), $conexion);


            if ($datosCliente['correo']) {
                $fecha = obtenerFechaEspaniol($fecha_hora);
                $asunto = 'CITA ACTUALIZADA!';
                $mensaje = '
                                Que tal <b>' . strtoupper($datosCliente['nombre_cliente']) . '</b><br><br>
                                Se ha registrado un cambio en tu cita previamente programada:<br>
                                Fisioterapeuta: <b>' . $datosCliente['nombre_cliente'] . '</b><br>
                                Fecha: <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b><br>
                                Consultorio: <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                                
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                $correo = enviarCorreo($datosCliente['correo'], $asunto, $mensaje);
            }

            if ($datosTerap['correo']) {

                $fecha = obtenerFechaEspaniol($fecha_hora);
                $asunto = 'CITA ACTUALIZADA!';
                $mensaje = '
                                Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>,.<br><br>
                                Se ha registrado un cambio en una consulta programada contigo quedando de la siguiente manera:<br>
                                El cliente/paciente:<b>' . $datosCliente['nombre_cliente'] . '</b><br>
                                Fecha: <b>' . $fecha['dia'] . ' ' . $fecha['num_dia'] . ' de ' . $fecha['mes'] . '</b> del <b>' . $fecha['anio'] . '</b> 
                                a las <b>' . $fecha['hora'] . '</b><br>
                                Consultorio: <b>' . $datosConsultorio['nombre'] . '</b> <br><br>
                                <b>Observaciones:</b> ' . strtoupper($_POST['observaciones']) . '
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                $correo = enviarCorreo($datosTerap['correo'], $asunto, $mensaje, $idFolio, idTerapeuta: $idTerapeuta);
            }
        }
        $respuesta = [
            'id_folio' => $_POST['tipo_gestion'],
            'actualizacion' => true,
            'correo' => $correo,
        ];
        echo json_encode($respuesta);
    }
}

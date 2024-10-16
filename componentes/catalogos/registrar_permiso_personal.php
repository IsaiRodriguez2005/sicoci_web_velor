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

    if ($_POST['id_personal']) {
        if (empty($_POST['id_permiso'])) {
            $fecha_alta = date("Y-m-d");
            $selectMAX = "SELECT COALESCE(MAX(id_permiso),0) AS no_registro FROM emisores_personal_permisos WHERE id_emisor=" . $_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;

            $insertPermiso = "INSERT INTO emisores_personal_permisos VALUES(" . $ultimo . ", " . $_SESSION['id_emisor'] . ",'" . intval($_POST['id_personal']) . "','" . $_POST['fecha_inicial'] . "','" . $_POST['fecha_final'] . "','" . strtoupper($_POST['motivo_permiso']) . "','" . $fecha_alta . "', 1)";
            $resultado = mysqli_query($conexion, $insertPermiso);


            if ($resultado) {

                $datosTerap = getDatosTerapeuta(intval($_POST['id_personal']), $conexion);

                if ($datosTerap['correo']) {

                    $fecha_inicial = obtenerFechaEspaniol($_POST['fecha_inicial']);
                    $fecha_final = obtenerFechaEspaniol($_POST['fecha_final']);
                    $asunto = 'PERMISO AGENDADO!';
                    $mensaje = '
                                    Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>.<br><br>
                                    Se ha registrado un permiso con los siguientes datos:<br>
                                    # permiso: <b>' . $ultimo . '</b><br>
                                    Fecha inicio: <b>' . $fecha_inicial['dia'] . ' ' . $fecha_inicial['num_dia'] . ' de ' . $fecha_inicial['mes'] . '</b> del <b>' . $fecha_inicial['anio'] . '</b> 
                                    a las <b>' . $fecha_inicial['hora'] . '</b><br>
                                    Fecha fin: <b>' . $fecha_final['dia'] . ' ' . $fecha_final['num_dia'] . ' de ' . $fecha_final['mes'] . '</b> del <b>' . $fecha_final['anio'] . '</b> 
                                    a las <b>' . $fecha_final['hora'] . '</b><br><br><br>
                                    <b>Observaciones:</b> ' . strtoupper($_POST['motivo_permiso']) . '
                                    PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                    ';
                    $correo = enviarCorreo($datosTerap['correo'], $asunto, $mensaje);
                }
                echo "ok";
            } else {
                echo "error";
            }
        } else {

            $updatePermiso = "UPDATE emisores_personal_permisos SET 
                                                                    fecha_inicial = '" . $_POST['fecha_inicial'] . "', 
                                                                    fecha_final = '" . $_POST['fecha_final'] . "', 
                                                                    motivo = '" . $_POST['motivo_permiso'] . "' 
                                                                WHERE 
                                                                    id_permiso = " . $_POST['id_permiso'] . " AND 
                                                                    id_personal = " . $_POST['id_personal'] . ";";
            $resultado = mysqli_query($conexion, $updatePermiso);
            if ($resultado) {
                $datosTerap = getDatosTerapeuta(intval($_POST['id_personal']), $conexion);

                if ($datosTerap['correo']) {


                    $fecha_inicial = obtenerFechaEspaniol($_POST['fecha_inicial']);
                    $fecha_final = obtenerFechaEspaniol($_POST['fecha_final']);
                    $asunto = 'PERMISO ACTUALIZADA!';
                    $mensaje = '
                                    Que tal <b>' . strtoupper($datosTerap['nombre_personal']) . '</b>.<br><br>
                                    Se realizó un cambio en tu permiso con folio: <b>' . $_POST['id_permiso'] . '</b><br>
                                    Fecha inicio: <b>' . $fecha_inicial['dia'] . ' ' . $fecha_inicial['num_dia'] . ' de ' . $fecha_inicial['mes'] . '</b> del <b>' . $fecha_inicial['anio'] . '</b> 
                                    a las <b>' . $fecha_inicial['hora'] . '</b><br>
                                    Fecha fin: <b>' . $fecha_final['dia'] . ' ' . $fecha_final['num_dia'] . ' de ' . $fecha_final['mes'] . '</b> del <b>' . $fecha_final['anio'] . '</b> 
                                    a las <b>' . $fecha_final['hora'] . '</b><br><br><br>
                                    <b>Observaciones:</b> ' . strtoupper($_POST['motivo_permiso']) . '
                                    PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                    ';
                    echo $correo = enviarCorreo($datosTerap['correo'], $asunto, $mensaje);
                }
                echo "ok";
            } else {
                echo "error";
            }
        }
    }
}

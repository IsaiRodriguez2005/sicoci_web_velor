<?php
header('Content-Type: application/json');

session_start();
date_default_timezone_set('America/Mexico_City');
require("../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo json_encode(['error' => 'Sesión no válida']);
    exit;
}

$fecha = !empty($_POST['fecha_hora']) ? date('Y-m-d', strtotime($_POST['fecha_hora'])) : date('Y-m-d');
$fechaActual = date('Y-m-d');
$dia_semana = date('w', strtotime($fecha));

if ($fecha < $fechaActual) {
    echo json_encode(['error' => 'No hay horarios disponibles para fechas pasadas']);
    exit;
}

if (
    $_SESSION['hora_entrada'] == $_SESSION['hora_salida'] ||
    $_SESSION['hora_salida'] < $_SESSION['hora_entrada'] ||
    $_SESSION['hora_comida_inicio'] < $_SESSION['hora_entrada'] ||
    $_SESSION['hora_comida_fin'] > $_SESSION['hora_salida'] ||
    $_SESSION['hora_comida_inicio'] >= $_SESSION['hora_comida_fin'] ||
    $_SESSION['hora_salida_sabado'] < $_SESSION['hora_entrada_sabado'] ||
    $_SESSION['rango_citas'] <= 0
) {
    echo json_encode([
        'alert_error' => 'Hay inconsistencias en la configuración de horarios. <br>
        Verifica que los horarios de entrada, salida, comida y citas sean válidos y estén correctamente configurados en: <strong>Configuraciones > Parámetros Fiscales</strong>.'
    ]);
    exit;
}


if ($dia_semana != 0) {
    $query = "SELECT TIME(fecha_agenda) AS hora_ocupada 
                FROM emisores_agenda 
                WHERE DATE(fecha_agenda) = '$fecha' 
                AND id_terapeuta = " . intval($_POST['id_terapeuta']) . " AND id_emisor = " . $_SESSION['id_emisor'];
    $resultado = mysqli_query($conexion, $query);

    $horariosOcupados = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $horariosOcupados[] = (new DateTime($fila['hora_ocupada']))->format('H:i');
    }

    $horaInicio = new DateTime($dia_semana == 6 ? $_SESSION['hora_entrada_sabado'] : $_SESSION['hora_entrada']);
    $horaSalida = new DateTime($dia_semana == 6 ? $_SESSION['hora_salida_sabado'] : $_SESSION['hora_salida']);
    $horaComidaInicio = new DateTime($_SESSION['hora_comida_inicio']);
    $horaComidaFin = new DateTime($_SESSION['hora_comida_fin']);
    $rango_citas = $_SESSION['rango_citas'];
    $horaActual = (new DateTime())->format('H:i');

    $horariosDisponibles = [];
    while ($horaInicio <= $horaSalida) {
        $horaActualIterada = $horaInicio->format('H:i');
        if (
            !in_array($horaActualIterada, $horariosOcupados)
            && ($horaInicio < $horaComidaInicio || $horaInicio >= $horaComidaFin)
            && !($fecha === $fechaActual && $horaActualIterada < $horaActual)
        ) {
            $horariosDisponibles[] = $horaActualIterada;
        }
        $horaInicio->modify("+$rango_citas minutes");
    }

    echo json_encode(['horarios' => $horariosDisponibles]);
} else {
    echo json_encode(['error' => 'Día no laboral']);
}

<?php
session_start();
date_default_timezone_set('America/Mexico_City');
require("../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "<script>window.location = 'index.html';</script>";
    exit;
}

$fecha = !empty($_POST['fecha_hora']) ? date('Y-m-d', strtotime($_POST['fecha_hora'])) : date('Y-m-d');
$fechaActual = date('Y-m-d');
$dia_semana = date('w', strtotime($fecha));

// Solo procesar si no es domingo

if ($fecha < $fechaActual) {
    echo "<option>No hay horarios disponibles para fechas pasadas</option>";
    exit;
} else {
    if ($dia_semana != 0) {
        $query = "SELECT fecha_agenda FROM emisores_agenda WHERE 
                    ((fecha_agenda >= '" . $fecha . "T" . $_SESSION['hora_entrada'] . "' AND fecha_agenda <= '" . $fecha . "T" . $_SESSION['hora_comida_inicio'] . "') 
                    OR (fecha_agenda >= '" . $fecha . "T" . $_SESSION['hora_comida_fin'] . "' AND fecha_agenda <= '" . $fecha . "T" . $_SESSION['hora_salida'] . "'))
                    AND id_terapeuta = " . intval($_POST['id_terapeuta']);
        $resultado = mysqli_query($conexion, $query);

        // Establecer horarios de inicio y salida, y horario de comida
        $horaInicio = new DateTime($dia_semana == 6 ? $_SESSION['hora_entrada_sabado'] : $_SESSION['hora_entrada']);
        $horaSalida = new DateTime($dia_semana == 6 ? $_SESSION['hora_salida_sabado'] : $_SESSION['hora_salida']);
        $rango_citas = $_SESSION['rango_citas'];

        $horarios = [];
        if ($dia_semana != 6) {
            $horaInicioComida = new DateTime($_SESSION['hora_comida_inicio']);
            $horaFinComida = new DateTime($_SESSION['hora_comida_fin']);

            // Generar horarios antes de la comida
            while ($horaInicio < $horaInicioComida) {
                $horarios[] = $horaInicio->format('H:i');
                $horaInicio->modify("+$rango_citas minutes");
            }
            $horaInicio = $horaFinComida; // Retomar después de la comida
        }

        // Generar horarios desde la hora de inicio o fin de comida hasta la hora de salida
        while ($horaInicio <= $horaSalida) {
            $horarios[] = $horaInicio->format('H:i');
            $horaInicio->modify("+$rango_citas minutes");
        }

        // Obtener horarios ocupados
        $horariosOcupados = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $horariosOcupados[] = date('H:i', strtotime($fila['fecha_agenda']));
        }

        // Calcular horarios disponibles y generar opciones HTML
        $horariosDisponibles = array_diff($horarios, $horariosOcupados);
        $html_horarios_disponibles = '';
        $horaActual = (new DateTime())->format('H:i'); // Obtener la hora actual en formato 'H:i'

        foreach ($horariosDisponibles as $h) {
            if ($fecha === $fechaActual && $h < $horaActual) {
                continue; // Omitir horarios que ya pasaron si es el día actual
            }
            $html_horarios_disponibles .= "<option value='$h'>$h</option>";
        }

        // Agregar horario seleccionado si existe folio de gestión
        if (intval($_POST['folio_gestion']) != 0) {
            $horario = date('H:i', strtotime($_POST['hora_gestion']));
            $html_horarios_disponibles .= "<option value='$horario' selected>$horario</option>";
        }

        echo $html_horarios_disponibles;
    } else {
        $html_horarios_disponibles = "<option value='' selected>Dia no laboral</option>";
        echo $html_horarios_disponibles;
    }
}

<?php
session_start();
require("../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    if (!$_POST['fecha_hora']) {
        $fecha = date('Y-m-d');
    } else {
        $fecha = date('Y-m-d', strtotime($_POST['fecha_hora']));
    }

    // Convertir la fecha inicial a un objeto DateTime para realizar operaciones
    $fecha_objeto = new DateTime($fecha);

    // Obtener la fecha formateada
    $fecha = $fecha_objeto->format('Y-m-d');
    $dia_semana = $fecha_objeto->format('w');

    //$horaEntrada = $_SESSION['hora_entrada'];
    //$horaSalida = $_SESSION['hora_salida'];


    //echo $_POST['id_terapeuta'];
    if (intval($dia_semana) != 0) {
        // buscamos los horarios que esten ocudos en el dia establecido por el usuario
        $query = "SELECT id_terapeuta, fecha_agenda FROM emisores_agenda WHERE 
                                                (fecha_agenda >= '" . $fecha . "T" . $_SESSION['hora_entrada'] . "' AND fecha_agenda <= '" . $fecha . "T" . $_SESSION['hora_comida_inicio'] . "') 
                                            OR
                                                (fecha_agenda >= '" . $fecha . "T" . $_SESSION['hora_comida_fin'] . "' AND  fecha_agenda <= '" . $fecha . "T" . $_SESSION['hora_salida'] . "')
                                            AND 
                                                id_terapeuta = " . intval($_POST['id_terapeuta']) . "";
        //echo $query;
        $resultado = mysqli_query($conexion, $query);

        // array para almacenar horarios en formato de el [rango] hora
        $horarios = [];

        //! HORARIOS (Margen)--------------------

        if (intval($dia_semana) == 6) {
            $horaInicio = new DateTime($_SESSION['hora_entrada_sabado']);
            $horaSalida = new DateTime($_SESSION['hora_salida_sabado']);
        } else {
            $horaInicio = new DateTime($_SESSION['hora_entrada']);
            $horaSalida = new DateTime($_SESSION['hora_salida']);
            $horaInicioComida = new DateTime($_SESSION['hora_comida_inicio']);
            $horaFinComida = new DateTime($_SESSION['hora_comida_fin']);
        }
        //! FIN HORARIOS (Margen)--------------------

        $rango_citas = $_SESSION['rango_citas'];

        if (intval($dia_semana) == 6) {
            while ($horaInicio <= $horaSalida) {
                $horarios[] = $horaInicio->format('H:i');
                $horaInicio->modify('+' . $rango_citas . ' minutes'); //! 1 hora
            }
        } else {
            // iteramos para giardar la lista de los horarios
            while ($horaInicio <= $horaInicioComida) {
                $horarios[] = $horaInicio->format('H:i');
                $horaInicio->modify('+' . $rango_citas . ' minutes'); //! 1 hora
            }

            while ($horaFinComida <= $horaSalida) {
                $horarios[] = $horaFinComida->format('H:i');
                $horaFinComida->modify('+' . $rango_citas . ' minutes'); //! 1 hora
            }
        }

        print_r($horarios);

        $horariosOcupados = [];

        // guardamos los horarios redondeados en el formato de 30min
        while ($fila = mysqli_fetch_assoc($resultado)) {

            $horario = date('H:i', strtotime($fila['fecha_agenda']));
            //$horario = redondearHora($horario);
            $horariosOcupados[] = $horario;
        }

        print_r($horariosOcupados);

        // quitaos los horarios ocupados de los hoarios disponibles
        $horariosDisponibles = array_diff($horarios, $horariosOcupados);

        $html_horarios_disponbles = '';

        // de los horarios disponibles, vamos a recorrerlos todos y por cada uno , haremos un [spam] para mostrar la hora disponible
        foreach ($horariosDisponibles as $h) {
            $html_horarios_disponbles .= '';
            $html_horarios_disponbles .= '<option value="' . $h . '">' . $h . '</option>';
        }

        echo $_POST['folio_gestion'];
        if(intval($_POST['folio_gestion']) != 0){
            $horario = date('H:i', strtotime($_POST['hora_gestion']));
            $html_horarios_disponbles .= '<option value="' . $horario . '" selected>' . $horario . '</option>';
        }


        echo $html_horarios_disponbles;
    }
}

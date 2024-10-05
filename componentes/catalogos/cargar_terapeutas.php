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
    //TODO: SOLO MOSTRARA LA DISPONIBILIDAD EN HORARIO ESTABLECIDO EN EL INPUT--------------------------------------------------------------------------------
    if ($_POST['movimiento'] == '1') {
        $html = '<option value="" selected disabled>Selecciona Terapeuta</option>';

        $fecha = date('Y-m-d', strtotime($_POST['fecha_hora']));
        // revisamos la fecha de la agenda, para sacar los terapeutas que estan en ese dia y hora ocupados
        $terapeutas_Activos = "SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '" . $_POST['fecha_hora'] . "' 
                                                                                UNION 
                                SELECT id_personal FROM emisores_personal_permisos WHERE fecha_inicial >= '" . $fecha . "' OR fecha_final <= '" . $fecha . "'";
        echo $terapeutas_Activos;
        $resultado = mysqli_query($conexion, $terapeutas_Activos);

        // si contiene alguna respuesa
        if ($resultado->num_rows > 0) {

            // inicializamos una variable, para poder concatenar los ids
            $ids = '';
            // hacemos un ciclo para concatenar todos los ids
            while ($filas = $resultado->fetch_assoc()) {

                $ids .= $filas['id_terapeuta'] . ",";
            }
            // cerramos con 0 para que no falle por la {,}
            $ids .= "0";

            // si es que hubo, vamos ahacr una busqueda en emisores_personal para descartar los terapeutas que esten ocuados en ese dia y hora
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND id_personal NOT IN (" . $ids . ") ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);

            // echo $consultaTerapeuta;

            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                // cargamos los terapeutas que estan disponibles
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
        // si no hay ningun terapeuta a esa hora, cargaremos todos 
        else {
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND estatus = 1 ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
        //TODO: MOSTRARA LA DISPONIBILIDAD DE TERAPEUTAS EN LOS PROXIMOS 7 DIAS, PARTIENDO DE LA FECHA ESTABLECIDA EN EL INPUT---------------------------------
    } else {

        //$nombreDia = date('l', strtotime($_POST['fecha_hora']));

        $html = '';
        $html .= '
            <table class="table table-striped table-hover" id="tabla_disponibilidad_terapeutas" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Terapeuta</th>
                        ';

        //var_dump($_POST['fecha_hora']);
        if (!$_POST['fecha_hora']) {
            $_fecha = date('Y-m-d');
        } else {
            $_fecha = date('Y-m-d', strtotime($_POST['fecha_hora']));
        }


        // convertir la fecha inicial a un objeto DateTime para realizar operaciones
        $fecha_tabla = new DateTime($_fecha);

        // array para almacenar los nombres de los dias en español
        $dias_semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $contador_dias = 0;
        // Recorrer los próximos 7 dias
        while ($contador_dias < 7) {

            // Obtener el numero del día de la semana (0 = domingo, 6 = sabado)
            $dia_semana = $fecha_tabla->format('w');

            if (intval($dia_semana) != 0) { // Obtener la fecha formateada y el nombre del dia
                $fecha_formateada = $fecha_tabla->format('Y-m-d');
                $nombre_dia = $dias_semana[$dia_semana];

                // Imprimir o utilizar la fecha y el nombre del día formateados
                $html .= '<th class="text-center text-sm">' . $nombre_dia . '<br>' . $fecha_tabla->format('d-m-Y') . '</th>';

                $contador_dias++;
            }
            // Incrementar la fecha en un día
            $fecha_tabla->modify('+1 day');
        }


        $html .= '
                    </tr>
                </thead>
                <tbody id="mostrar_clientes">
            ';

        $queryTerapeutas = "SELECT id_personal, nombre_personal FROM emisores_personal WHERE tipo = 2 AND id_emisor = " . $_SESSION['id_usuario'] . "";
        $resultadotTerapeutas = mysqli_query($conexion, $queryTerapeutas);
        while ($terapeuta = mysqli_fetch_assoc($resultadotTerapeutas)) {

            $nombre_terapeuta = $terapeuta['nombre_personal'];

            if (!$_POST['fecha_hora']) {
                $fecha = date('Y-m-d');
            } else {
                $fecha = date('Y-m-d', strtotime($_POST['fecha_hora']));
            }

            $html .= "
                            <tr>
                                <td class='text-center'> " . $nombre_terapeuta . "</td>
                                
                        ";

            // Convertir la fecha inicial a un objeto DateTime para realizar operaciones
            $fecha_objeto = new DateTime($fecha);

            $contador_dias = 0;
            // Recorrer los próximos 7 días
            while ($contador_dias < 7) {

                // Obtener la fecha formateada
                $fecha = $fecha_objeto->format('Y-m-d');
                $dia_semana = $fecha_objeto->format('w');

                if (intval($dia_semana) != 0) {
                    // buscamos los horarios que esten ocudos en el dia establecido por el usuario
                    $query = "SELECT id_terapeuta, fecha_agenda FROM emisores_agenda WHERE fecha_agenda >= '" . $fecha . "T" . $_SESSION['hora_entrada'] . "' AND fecha_agenda <= '" . $fecha . "T" . $_SESSION['hora_salida'] . "' AND id_terapeuta = " . $terapeuta['id_personal'] . "";
                    $resultado = mysqli_query($conexion, $query);

                    // array para almacenar horarios en formato de 1hora
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

                    $horariosOcupados = [];

                    // guardamos los horarios redondeados en el formato de 30min
                    while ($fila = mysqli_fetch_assoc($resultado)) {

                        $horario = date('H:i', strtotime($fila['fecha_agenda']));
                        //$horario = redondearHora($horario);
                        $horariosOcupados[] = $horario;
                    }

                    //print_r($horariosOcupados);

                    // quitaos los horarios ocupados de los hoarios disponibles
                    $horariosDisponibles = array_diff($horarios, $horariosOcupados);

                    $html_horarios_disponbles = '';

                    // de los horarios disponibles, vamos a recorrerlos todos y por cada uno , haremos un [spam] para mostrar la hora disponible
                    foreach ($horariosDisponibles as $h) {
                        $html_horarios_disponbles .= '';
                        $html_horarios_disponbles .= '<span class="badge badge-custom">' . $h . '</span>';
                    }

                    $html .= "
                            
                            <td class='text-center''> 
                                <div class='calendar-container'> 
                                    " . $html_horarios_disponbles . "
                                </div>
                            </td>

                    ";

                    $contador_dias++;
                }

                // Incrementar la fecha en un día
                $fecha_objeto->modify('+1 day');
            }
            $html .= "</tr>";
        }




        $html .= "
                </tbody>
            </table>
            ";

        echo $html;
    }
}

function redondearHora($hora)
{
    // Convertir la hora a timestamp
    $timestamp = strtotime($hora);
    //echo $timestamp;
    // Sacar los minutos actuales
    $minutos = date('i', $timestamp);

    /*
    //* esto era para redondear por 30min
    // Redondear a los 30 minutos más cercanos
    if ($minutos < 15) {
        // Redondear hacia abajo a la hora exacta
        return date('H:00', $timestamp);
    } elseif ($minutos < 45) {
        // Redondear a los 30 minutos
        return date('H:30', $timestamp);
    } else {
        // Redondear hacia la hora completa siguiente
        return date('H:00', strtotime('+1 hour', $timestamp));
    }
        */
    // Redondear a la hora completa más cercana
    if ($minutos < 30) {
        // Redondear hacia abajo a la hora exacta
        return date('H:00', $timestamp);
    } else {
        // Redondear hacia la hora completa siguiente
        return date('H:00', strtotime('+1 hour', $timestamp));
    }
}


function rangoHorarios() {}

?>

<script>
    $(function() {
        $('#tabla_disponibilidad_terapeutas').DataTable({
            "destroy": true,
            "paging": true,
            "pageLength": 2,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            }
        });
    });
</script>
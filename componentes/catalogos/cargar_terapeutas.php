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
    // SOLO MOSTRARA LA DISPONIBILIDAD EN HORARIO ESTABLECIDO EN EL INPUT--------------------------------------------------------------------------------
    if ($_POST['movimiento'] == '1') {
        $html = '<option value="" selected disabled>Selecciona Terapeuta</option>';

        $fecha = date('Y-m-d', strtotime($_POST['fecha_hora']));
        // revisamos la fecha de la agenda, para sacar los terapeutas que estan en ese dia y hora ocupados
        $terapeutas_Activos = "SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '".$_POST['fecha_hora']."' UNION SELECT id_personal FROM emisores_personal_permisos WHERE fecha_permiso = '".$fecha."'";
        $resultado = mysqli_query($conexion, $terapeutas_Activos);

        // si contiene alguna respuesa
        if ($resultado->num_rows > 0) {

            // inicializamos una variable, para poder concatenar los ids
            $ids = '';
            //hacemos un ciclo para concatenar todos los ids
            while($filas = $resultado->fetch_assoc()){
                
                $ids .= $filas['id_terapeuta'] . ",";

            }
            // cerramos con 0 para que no falle por la {,}
            $ids .= "0";

            // si es que hubo, vamos ahacr una busqueda en emisores_personal para descartar los terapeutas que esten ocuados en ese dia y hora
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND id_personal NOT IN (" . $ids . ") ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);

            echo $consultaTerapeuta;

            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                // cargamos los terapeutas que estan disponibles
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
        // si no hay ningun terapeuta a esa hora, cargaremos todos 
        else {
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
    // MOSTRARA LA DISPONIBILIDAD DE TERAPEUTAS EN LOS PROXIMOS 7 DIAS, PARTIENDO DE LA FECHA ESTABLECIDA EN EL INPUT---------------------------------
    } else {

    }
}




/*
    // SOLO MOSTRARA LA DISPONIBILIDAD EN HORARIO ESTABLECIDO EN EL INPUT--------------------------------------------------------------------------------
    if ($_POST['movimiento'] == 1) {
        $html = '<option value="" selected disabled>Selecciona Terapeuta</option>';
        // revisamos la fecha de la agenda, para sacar los terapeutas que estan en ese dia y hora ocupados
        $terapeutas_Activos = "SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '" . $_POST['fecha_hora'] . "'";
        $resultado = mysqli_query($conexion, $terapeutas_Activos);
        $filas = mysqli_fetch_assoc($resultado);
        // validamos si hay alguno
        if ($filas) {
            
            // si es que hubo, vamos ahacr una busqueda en emisores_personal para descartar los terapeutas que esten ocuados en ese dia y hora
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND id_personal != " . $filas['id_terapeuta'] . " ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                // cargamos los terapeutas que estan disponibles
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
        // si no hay ningun terapeuta a esa hora, cargaremos todos 
        else {
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
    // MOSTRARA LA DISPONIBILIDAD DE TERAPEUTAS EN LOS PROXIMOS 7 DIAS, PARTIENDO DE LA FECHA ESTABLECIDA EN EL INPUT---------------------------------
    } else {

    }

*/

/*

    // SOLO MOSTRARA LA DISPONIBILIDAD EN HORARIO ESTABLECIDO EN EL INPUT--------------------------------------------------------------------------------
    if ($_POST['movimiento'] == '1') {

        $html = '<option value="" selected disabled>Selecciona Terapeuta</option>';
        // revisamos la fecha de la agenda, para sacar los terapeutas que estan en ese dia y hora ocupados
        $terapeutas_Activos = "SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '" . $_POST['fecha_hora'] . "'";
        $resultado = mysqli_query($conexion, $terapeutas_Activos);
        $filas = mysqli_fetch_assoc($resultado);
        // validamos si hay alguno
        var_dump($filas);
        if ($filas) {

            // si es que hubo, vamos hacer una busqueda en emisores_personal para descartar los terapeutas que esten ocuados en ese dia y hora
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND id_personal != " . $filas['id_terapeuta'] . " ORDER BY nombre_personal ASC";
            $filas = mysqli_query($conexion, $consultaTerapeuta);
        echo 'hola';
            if ($filas) {
                var_dump($filas);
                $date = date('Y-m-d', strtotime($_POST['fecha_hora']));

                $consultaTerapeuta = "SELECT id_personal FROM emisores_personal_permisos WHERE id_personal = " . $filas['id_personal'] . " AND fecha_permiso = '".$date."' ORDER BY nombre_personal ASC";
                $filas = mysqli_query($conexion, $consultaTerapeuta);

                echo $consultaTerapeuta;
                echo $filas['id_personal'];
                if ($filas) {

                    $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND id_personal != " . $filas['id_personal'] . " ORDER BY nombre_personal ASC";
                    $filas = mysqli_query($conexion, $consultaTerapeuta);
                    while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                        // cargamos los terapeutas que estan disponibles
                        $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
                    }

                    echo $html;
                }
            }
        }
        // si no hay ningun terapeuta a esa hora, cargaremos todos 
        else {
            $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 ORDER BY nombre_personal ASC";
            $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
            while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
                $html .= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
            }

            echo $html;
        }
        // MOSTRARA LA DISPONIBILIDAD DE TERAPEUTAS EN LOS PROXIMOS 7 DIAS, PARTIENDO DE LA FECHA ESTABLECIDA EN EL INPUT---------------------------------
    } else {
    }




    SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '2024-09-10 09:30:00' UNION SELECT id_personal FROM emisores_personal_permisos WHERE fecha_permiso = '2024-09-10'

    SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '2024-09-10 09:30:00' UNION SELECT id_personal FROM emisores_personal_permisos WHERE fecha_permiso = '2024-10-30';
*/

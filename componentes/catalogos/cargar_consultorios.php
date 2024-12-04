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

    $fecha_hora = $_POST['fecha_cita'] . 'T' . $_POST['hora_cita'];

    $html = '<option value="" selected disabled>Selecciona Consultorio</option>';
    // revisamos la fecha de la agenda, para sacar los cosltorios que estan en ese dia y hora ocupados
    $consultorios_Activos = "SELECT id_consultorio FROM emisores_agenda WHERE fecha_agenda = '" . $fecha_hora . "' AND id_emisor = ".$_SESSION['id_emisor']."";
    $resultado = mysqli_query($conexion, $consultorios_Activos);
    $filas = mysqli_fetch_assoc($resultado);
    // validamos si hay alguno
    if ($filas) {

        // si es que hubo, vamos ahacr una busqueda en emisores_personal para descartar los cosltorios que esten ocuados en ese dia y hora
        $consultaTerapeuta = "SELECT * FROM emisores_consultorios WHERE id_consultorio != ".$filas['id_consultorio']." AND id_emisor = ".$_SESSION['id_emisor']." ORDER BY nombre ASC";
        $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
        while ($consultorio = mysqli_fetch_array($resTerapeutas)) {
            // cargamos los cosltorios que estan disponibles
            $html.= "<option value='" . $consultorio['id_consultorio'] . "'>" . $consultorio['nombre'] . "</option>";
        }

        echo $html;
    } 
    // si no hay ningun consultorio a esa hora, cargaremos todos 
    else 
    {
        $consultaTerapeuta = "SELECT * FROM emisores_consultorios WHERE id_emisor = ".$_SESSION['id_emisor']." ORDER BY nombre ASC";
        $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
        while ($consultorio = mysqli_fetch_array($resTerapeutas)) {
            $html.= "<option value='" . $consultorio['id_consultorio'] . "'>" . $consultorio['nombre'] . "</option>";
        }

        echo $html;
    }
}
?>

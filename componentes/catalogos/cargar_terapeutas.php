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

    $html = '<option value="" selected disabled>Selecciona Terapeuta</option>';
    // revisamos la fecha de la agenda, para sacar los terapeutas que estan en ese dia y hora ocupados
    $terapeutas_Activos = "SELECT id_terapeuta FROM emisores_agenda WHERE fecha_agenda = '" . $_POST['fecha_hora'] . "'";
    $resultado = mysqli_query($conexion, $terapeutas_Activos);
    $filas = mysqli_fetch_assoc($resultado);
    // validamos si hay alguno
    if ($filas) {

        // si es que hubo, vamos ahacr una busqueda en emisores_personal para descartar los terapeutas que esten ocuados en ese dia y hora
        $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 AND id_personal != ".$filas['id_terapeuta']." ORDER BY nombre_personal ASC";
        $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
        while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
            // cargamos los terapeutas que estan disponibles
            $html.= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
        }

        echo $html;
    } 
    // si no hay ningun terapeuta a esa hora, cargaremos todos 
    else 
    {
        $consultaTerapeuta = "SELECT * FROM emisores_personal WHERE tipo = 2 ORDER BY nombre_personal ASC";
        $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
        while ($terapeuta = mysqli_fetch_array($resTerapeutas)) {
            $html.= "<option value='" . $terapeuta['id_personal'] . "'>" . $terapeuta['nombre_personal'] . "</option>";
        }

        echo $html;
    }
}
?>

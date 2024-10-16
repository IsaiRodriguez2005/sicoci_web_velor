<?php
session_start();
require("../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    $html = '<option value="0" selected disabled>Seleccione la ocupacion</option>';
    $consOcupaciones = "SELECT nombre_ocupacion, id_ocupacion FROM emisores_ocupaciones WHERE id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consOcupaciones);

    if ($resultado) {
        while ($filas = mysqli_fetch_array($resultado)) {
            $html .= '<option value="'.$filas['id_ocupacion'].'">'.$filas['nombre_ocupacion'].'</option>';
        }
    }

    echo $html;
}

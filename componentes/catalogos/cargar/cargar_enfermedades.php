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

    $html = '<option value="0" selected disabled>Seleccione una enfermedad</option>';
    $consEnfermededades = "SELECT nombre, id_enfermedad FROM emisores_enfermedades WHERE id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consEnfermededades);

    if ($resultado) {
        while ($filas = mysqli_fetch_array($resultado)) {
            $html .= '<option value="'.$filas['id_enfermedad'].'">'.$filas['nombre'].'</option>';
        }
    }

    echo $html;
}

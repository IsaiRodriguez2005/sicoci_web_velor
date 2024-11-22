<?php
session_start();
require("../../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    $consPerfil = "SELECT p.nombre_personal as nombre, p.id_personal 
                                FROM emisores_personal p LEFT JOIN usuarios u on p.id_personal = u.id_personal 
                                WHERE p.id_emisor = " . $_SESSION['id_emisor'] . " AND p.tipo = 2 AND u.id_personal IS NULL;";
    $resultado = mysqli_query($conexion, $consPerfil);

    if ($resultado) {
        $options = '<option value="0" selected>Terapeuta Ligado</option>';
        while ($filas = mysqli_fetch_array($resultado)) {
            $options .= "<option value='" . $filas['id_personal'] . "'>" . $filas['nombre'] . "</option>";
        }
    }

    echo $options;
}

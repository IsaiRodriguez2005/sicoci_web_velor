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

    $consTerapeutas = "SELECT nombre_personal, correo FROM emisores_personal WHERE tipo = 2 AND id_personal = " . $_POST['id_terapeuta'] . "";
    $resultado = mysqli_query($conexion, $consTerapeutas);

    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }

    echo json_encode($respuesta);
}

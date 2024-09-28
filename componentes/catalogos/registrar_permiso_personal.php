<?php
session_start();
require("../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    if ($_POST['id_personal']) {
        if (!$_POST['id_permiso']) {
            $fecha_alta = date("Y-m-d");
            $selectMAX = "SELECT COALESCE(MAX(id_permiso),0) AS no_registro FROM emisores_personal_permisos WHERE id_emisor=" . $_SESSION['id_emisor'];
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_registro'] + 1;

            $insertPermiso = "INSERT INTO emisores_personal_permisos VALUES(" . $ultimo . ", " . $_SESSION['id_emisor'] . ",'" . intval($_POST['id_personal']) . "','" . $_POST['fecha_inicial'] . "','" . $_POST['fecha_final'] . "','" . strtoupper($_POST['motivo_permiso']) . "','" . $fecha_alta . "', 1)";
            $resultado = mysqli_query($conexion, $insertPermiso);
            if ($resultado) {
                echo "ok";
            } else {
                echo "error";
            }
        } else {

            $updatePermiso = "UPDATE emisores_personal_permisos SET 
                                                                    fecha_inicial = '" . $_POST['fecha_inicial'] . "', 
                                                                    fecha_final = '" . $_POST['fecha_final'] . "', 
                                                                    motivo = '" . $_POST['motivo_permiso'] . "' 
                                                                WHERE 
                                                                    id_permiso = " . $_POST['id_permiso'] . " AND 
                                                                    id_personal = " . $_POST['id_personal'] . ";";
            $resultado = mysqli_query($conexion, $updatePermiso);
            if ($resultado) {
                echo "ok";
            } else {
                echo "error";
            }
        }
    }
}

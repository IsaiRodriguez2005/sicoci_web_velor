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

    $checkQuery = "SELECT COUNT(*) AS count FROM emisores_historial_expediente_enfermedades WHERE id_folio = " .$_POST['id_folio_cita']. " AND  id_enfermedad = " .$_POST['id_enfermedad']."";
    $result = mysqli_query($conexion, $checkQuery);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        echo 'e';
    } else {
        $selectMAX = "SELECT COALESCE(MAX(id_partida),0) AS no_registro FROM emisores_historial_expediente_enfermedades WHERE id_emisor = " . $_SESSION['id_emisor'];
        $resMAX = mysqli_query($conexion, $selectMAX);
        $max = mysqli_fetch_array($resMAX);
        $ultimo = $max['no_registro'] + 1;

        $insertenfermedades = "INSERT INTO emisores_historial_expediente_enfermedades 
                                            VALUES(" . $ultimo . ", 
                                                    " . $_SESSION['id_emisor'] . ", 
                                                    " . $_POST['id_folio_cita'] . ",
                                                    " . $_POST['id_enfermedad'] . ",
                                                    '" . strtoupper($_POST['tiempo']) . "',
                                                    '" . strtoupper($_POST['toma_medicamento']) . "'
                                                    )";
        
        $resultado = mysqli_query($conexion, $insertenfermedades);

        if ($resultado) {
            echo 'ok';
        }
    }
}

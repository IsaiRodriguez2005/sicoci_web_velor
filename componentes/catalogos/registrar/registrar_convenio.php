<?php
session_start();
require("../../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $caracteres = array("&", '"', "'");
    $reemplazo = array("&amp;", "&quot;", "&apos;");
    $nuevo_social = str_replace($caracteres, $reemplazo, strtoupper($_POST['nombre']));

    if ($_POST['id_convenio'] == 0) {
        // $fecha_alta = date("Y-m-d");

        $selectMAX = "SELECT COALESCE(MAX(id_convenio),0) AS no_registro FROM emisores_convenios WHERE id_emisor=" . $_SESSION['id_emisor'];
        $resMAX = mysqli_query($conexion, $selectMAX);
        $max = mysqli_fetch_array($resMAX);
        $ultimo = $max['no_registro'] + 1;

        $insertConvenio = "INSERT INTO emisores_convenios VALUES(
                                                                " . $ultimo . ", 
                                                                " . $_SESSION['id_emisor'] . ",
                                                                '" . trim($nuevo_social) . "',
                                                                '" . $_POST['tipo'] . "',
                                                                " . $_POST['pct_consul'] . ",
                                                                " . $_POST['cost_consul'] . ", 
                                                                1)";
        $resultado = mysqli_query($conexion, $insertConvenio);
        if ($resultado) {
            echo "ok";
        } else {
            echo "error";
        }
    } else {
        // print_r($_POST);
        $updateConvenio = "UPDATE emisores_convenios SET nombre='" . trim($nuevo_social) . "', 
                                                            tipo='" . ($_POST['tipo']) . "', 
                                                            pct_consul =" . $_POST['pct_consul'] . ",
                                                            cost_consul = " . $_POST['cost_consul'] . "
                                                            WHERE id_convenio=" . $_POST['id_convenio'] . " AND id_emisor=" . $_SESSION['id_emisor'];
        // return;
        $resultado = mysqli_query($conexion, $updateConvenio);
        if ($resultado) {
            echo "actualizado";
        } else {
            echo "error";
        }
    }
}

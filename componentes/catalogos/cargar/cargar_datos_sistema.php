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

    if (empty($_SESSION['id_personal'])) {

        $sql_citas = "SELECT COUNT(*) as citas_hoy FROM emisores_agenda WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND fecha_agenda = '" . date('Y-m-d') . "' AND estatus = 2 ";
        $res_citas = mysqli_query($conexion, $sql_citas);
        $c = mysqli_fetch_array($res_citas);

        $sql_citas2 = "SELECT COUNT(*) as citas_sin_cobrar FROM emisores_agenda WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND fecha_agenda < '" . date('Y-m-d') . "' AND estatus = 2";
        $res_citas2 = mysqli_query($conexion, $sql_citas2);
        $citas2 = mysqli_fetch_array($res_citas2);

        $citas = [
            'citas_hoy' => $c['citas_hoy'],
            'citas_sin_cobrar' => $citas2['citas_sin_cobrar']
        ];

        echo json_encode($citas);
    } else {

        $sql_citas = "SELECT COUNT(*) as citas_hoy FROM emisores_agenda WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND fecha_agenda = '" . date('Y-m-d') . "' AND estatus = 2 AND id_terapeuta = ".$_SESSION['id_personal']."";
        $res_citas = mysqli_query($conexion, $sql_citas);
        $c = mysqli_fetch_array($res_citas);

        $sql_citas2 = "SELECT COUNT(*) as citas_sin_cobrar FROM emisores_agenda WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND fecha_agenda < '" . date('Y-m-d') . "' AND estatus = 2 AND id_terapeuta = ".$_SESSION['id_personal']."";
        $res_citas2 = mysqli_query($conexion, $sql_citas2);
        $citas2 = mysqli_fetch_array($res_citas2);

        $citas = [
            'citas_hoy' => $c['citas_hoy'],
            'citas_sin_cobrar' => $citas2['citas_sin_cobrar']
        ];

        echo json_encode($citas);
    }
}

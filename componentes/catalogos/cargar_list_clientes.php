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
    $html = "";

    $sql = "SELECT id_cliente, nombre_cliente FROM emisores_clientes WHERE id_emisor = ".$_SESSION['id_emisor'].";";
    $res = mysqli_query($conexion, $sql);
    if (mysqli_num_rows($res) == 0) {
        $html.= "<option value='No existen clientes'></option>";
    } else {
        while ($cliente = mysqli_fetch_array($res)) {
            $html.= "<option value='" . $cliente['nombre_cliente'] . "'></option>";
        }
    }

    echo $html;
}
?>
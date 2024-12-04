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

    $consTerapeutas = "SELECT a.*, c.nombre_cliente, c.est_civ, c.telefono, c.fec_nac, c.ocupacion, 
                                (SELECT COUNT(*) FROM emisores_agenda AS a WHERE a.id_cliente = c.id_cliente) AS total_registros
                                FROM emisores_agenda AS a
                                INNER JOIN emisores_clientes AS c ON c.id_cliente = a.id_cliente AND c.id_emisor = a.id_emisor
                                WHERE a.id_cliente = " . intval($_POST['id_cliente']) . " AND a.id_folio = " . intval($_POST['folio']) . " AND a.id_emisor = " . intval($_SESSION['id_emisor']) . ";";
    //echo $consTerapeutas;
    $resultado = mysqli_query($conexion, $consTerapeutas);

    if ($resultado) {
        while ($filas = mysqli_fetch_assoc($resultado)) {
            $respuesta[] = $filas;
        }
    }
    if (intval($_POST['tipo_consulta']) != 1) {
        //* Conusa para saer si el cliente, a la ultima consulta, asistio.
        $consUltimoEstado = "SELECT estatus, id_folio FROM emisores_agenda WHERE id_cliente = " . intval($_POST['id_cliente']) . " AND id_folio < " . intval($_POST['folio']) . " AND id_emisor = " . intval($_SESSION['id_emisor']) . " ORDER BY id_folio DESC LIMIT 1;";
        $resultado = mysqli_query($conexion, $consUltimoEstado);

        $res = mysqli_fetch_array($resultado);
        $res['estatus'] == 3 ? $respuesta['continuo'] =  true : $respuesta['continuo'] = false;
    }


    echo json_encode($respuesta);
}

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

    if (empty($_POST['id_terapeuta'])) {
        $consulta = " SELECT a.id_folio, 
                                a.id_cliente,
                                a.estatus,
                                a.fecha_agenda,
                                p.nombre_personal,
                                c.nombre_cliente 
                            FROM 
                                emisores_agenda a 
                                LEFT JOIN emisores_personal p ON a.id_terapeuta = p.id_personal AND a.id_emisor = p.id_emisor AND p.tipo = 2
                                LEFT JOIN emisores_clientes c ON a.id_cliente = c.id_cliente AND a.id_emisor = c.id_emisor 
                                WHERE a.estatus = 2";

        $resCitas = mysqli_query($conexion, $consulta);

        $citas = array();
        if (mysqli_num_rows($resCitas) > 0) {
            while ($fila = mysqli_fetch_assoc($resCitas)) {
                $citas[] = $fila;
            }
        }

        echo json_encode($citas);
    } else {
        $id_terapeuta = intval($_POST['id_terapeuta']);
        $consulta = "SELECT a.id_folio, 
                    a.id_cliente,
                    a.estatus,
                    a.fecha_agenda,
                    p.nombre_personal,
                    c.nombre_cliente 
            FROM 
                    emisores_agenda a 
                    LEFT JOIN emisores_personal p ON a.id_terapeuta = p.id_personal 
                                                AND a.id_emisor = p.id_emisor 
                                                AND p.tipo = 2
                    LEFT JOIN emisores_clientes c ON a.id_cliente = c.id_cliente 
                                                AND a.id_emisor = c.id_emisor 
            WHERE 
                    a.estatus = 2 AND
                    a.id_terapeuta = " . $id_terapeuta;

        //print_r($consulta);
        $resCitas = mysqli_query($conexion, $consulta);

        $citas = array();
        if (mysqli_num_rows($resCitas) > 0) {
            while ($fila = mysqli_fetch_assoc($resCitas)) {
                $citas[] = $fila;
            }
        }

        echo json_encode($citas);
    }
}

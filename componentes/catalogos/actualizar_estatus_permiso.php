<?php
session_start();
require("../conexion.php");
include '../correos/enviar_correo.php';

date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $sqlEmisor = "UPDATE emisores_personal_permisos SET estatus=" . $_POST['estatus'] . " WHERE id_emisor = " . $_SESSION['id_emisor'] . " AND id_permiso=" . $_POST['id_permiso'];
    //echo $sqlEmisor;
    $resultado = mysqli_query($conexion, $sqlEmisor);

    if ($resultado) {


        $datosPermiso = getDatosPermisos(intval($_POST['id_permiso']), $conexion);
        $datosTerap = getDatosTerapeuta(intval($datosPermiso['id_personal']), $conexion);
        if ($datosTerap['correo']) {
            $asunto = 'PERMISO CANCELADO';
            $mensaje = '
                            Que tal ' . strtoupper($datosTerap['nombre_personal']) . '.                
                            El permiso con folio: <b>' . $_POST['id_permiso'] . ' ha sido <b>CANCELADO</b>.</b> <br><br>
                            PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                            ';

            enviarCorreo($datosTerap['correo'], $asunto, $mensaje);
        }
    }
    echo $resultado;
}

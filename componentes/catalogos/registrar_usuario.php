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
    $selectTerap = "SELECT id_personal FROM usuarios WHERE id_personal=" . $_POST['id_personal'];
    $resTerap = mysqli_query($conexion, $selectTerap);
    
    if (mysqli_num_rows($resTerap) > 0) {
        echo 1;
    } else {
        $sqlUsuario = "SELECT COALESCE(MAX(id_usuario), 0) AS contar FROM usuarios WHERE id_emisor = " . $_SESSION['id_emisor'];
        $resUsuario = mysqli_query($conexion, $sqlUsuario);
        $usuario = mysqli_fetch_array($resUsuario);

        $nuevo_usuario = $usuario['contar'] + 1;

        $insertUsuario = "INSERT INTO usuarios VALUES(" . $_SESSION['id_emisor'] . ", " . $nuevo_usuario . "," . $_POST['id_personal'] . ", '" . strtoupper($_POST['nombre']) . "', '" . $_POST['correo'] . "', '" . $_POST['password'] . "', 1)";
        $resutado = mysqli_query($conexion, $insertUsuario);
        $insertPermisos = "INSERT INTO usuarios_permisos VALUES(" . $_SESSION['id_emisor'] . ", " . $nuevo_usuario . ", " . $_POST['configuraciones'] . ", " . $_POST['agenda'] . ", " . $_POST['clientes'] . ", " . $_POST['usuarios'] . ", " . $_POST['productos'] . ", " . $_POST['proveedores'] . ", " . $_POST['personal'] . ", " . $_POST['tickets'] . ", " . $_POST['facturacion'] . ", " . $_POST['pago_proveedores'] . ", " . $_POST['reportes'] . ", " . $_POST['dash_directivo'] . ")";
        mysqli_query($conexion, $insertPermisos);

        $selectRazon = "SELECT nombre_social FROM emisores WHERE id_emisor=" . $_SESSION['id_emisor'];
        $resRazon = mysqli_query($conexion, $selectRazon);
        $razon = mysqli_fetch_array($resRazon);
        $razon_social = $razon['nombre_social'];

        if ($_POST['correo']) {
            $asunto = 'BIENVENIDO A COSERA';
            $mensaje = '
            Que tal ' . strtoupper($_POST['nombre']) . '<br><br>
            Cosera te da la bienvenida y hacemos de tu conocimiento que el administrador de la empresa ' . strtoupper($_SESSION['nombre_comercial']) . ' te ha registrado como un nuevo usuario del sistema por lo que te compartimos tus datos de acceso:<br>
            <b>Enlace:</b> https://www.velor.mx/cosera/<br><br>
            <b>Usuario:</b> ' . $_POST['correo'] . '<br>
            <b>Contrase&ntilde;a:</b> ' . $_POST['password'] . '<br>
            <b>Pin:</b> ' . $_SESSION['id_emisor'] . '<br><br>
            PD. Este correo es informativo por lo que no es necesario responder dicho correo.
            ';
            enviarCorreo($_POST['correo'], $asunto, $mensaje);
        }
        
        echo 1;
    }
}

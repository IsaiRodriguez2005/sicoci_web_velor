<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
        $sqlUsuario = "UPDATE usuarios SET nombre='".strtoupper($_POST['nombre'])."', correo='".$_POST['correo']."', password='".$_POST['password']."' WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_usuario=".$_POST['id_usuario'];
        mysqli_query($conexion, $sqlUsuario);
        
        $sqlPermisos = "UPDATE usuarios_permisos SET configuraciones=".$_POST['configuraciones'].", agenda=".$_POST['agenda'].", clientes=".$_POST['clientes'].", usuarios=".$_POST['usuarios'].", productos=".$_POST['productos'].", proveedores=".$_POST['proveedores'].", personal=".$_POST['personal'].", tickets=".$_POST['tickets'].", facturacion=".$_POST['facturacion'].", pago_proveedores=".$_POST['pago_proveedores'].", reportes=".$_POST['reportes'].", dash_directivo=".$_POST['dash_directivo']." WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_usuario=".$_POST['id_usuario'];
        mysqli_query($conexion, $sqlPermisos);

        $selectRazon = "SELECT nombre_social FROM emisores WHERE id_emisor=".$_SESSION['emisor'];
        $resRazon = mysqli_query($conexion, $selectRazon);
        $razon = mysqli_fetch_array($resRazon);
        $razon_social = $razon['nombre_social'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'mail.velor.mx';
            $mail->SMTPAuth   = true;
            
            $mail->Username   = 'notificaciones@velor.mx';
            $mail->Password   = '+N$eFTs?6c#L';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('notificaciones@velor.mx', 'VELOR NOTIFICADOR'); 
            $mail->addAddress($correo);

            $mail->isHTML(true);
            $mail->Subject = 'BIENVENIDO A VELOR ERP';
            $mensaje = '
                Bienvenido.<br><br>
                <b>Raz&oacute;n Social:</b> '.$razon_social.'<br>
                <b>Nombre Usuario:</b> '.strtoupper($_POST['nombre']).'<br><br>
                Se han realizado modificaciones a tus datos de acceso:<br>
                <b>Enlace:</b> https://www.velor.mx/intranet/<br>
                <b>Correo:</b> '.$_POST['correo'].'<br>
                <b>Contrase&ntilde;a:</b> '.$_POST['password'].'<br>
                <b>Pin:</b> '.$_POST['emisor'].'<br>
                ';
            $mail->Body = $mensaje;

            $mail->send();
        } catch (Exception $e) {
            echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
        }
    }
?>
<?php
    session_start();
    require("../conexion.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require('../../phpmailer/Exception.php');
    require('../../phpmailer/PHPMailer.php');
    require('../../phpmailer/SMTP.php');
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
        $sqlUsuario = "SELECT COALESCE(MAX(id_usuario), 0) AS contar FROM usuarios WHERE id_emisor = ".$_SESSION['id_emisor'];
        $resUsuario = mysqli_query($conexion, $sqlUsuario);
        $usuario =mysqli_fetch_array($resUsuario);

        $nuevo_usuario = $usuario['contar'] + 1;

        $insertUsuario = "INSERT INTO usuarios VALUES(".$_SESSION['id_emisor'].", ".$nuevo_usuario.", '".strtoupper($_POST['nombre'])."', '".$_POST['correo']."', '".$_POST['password']."', 1)";
        mysqli_query($conexion, $insertUsuario);

        $insertPermisos = "INSERT INTO usuarios_permisos VALUES(".$_SESSION['id_emisor'].", ".$nuevo_usuario.", ".$_POST['configuraciones'].", ".$_POST['agenda'].", ".$_POST['clientes'].", ".$_POST['usuarios'].", ".$_POST['productos'].", ".$_POST['proveedores'].", ".$_POST['personal'].", ".$_POST['tickets'].", ".$_POST['facturacion'].", ".$_POST['pago_proveedores'].", ".$_POST['reportes'].", ".$_POST['dash_directivo'].")";
        mysqli_query($conexion, $insertPermisos);

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
                Nos complace tenerte con nosotros, trabajaremos de la mano para que tengas la mejor experiencia de uso de nuestra plataforma VELOR ERP, te compartimos tus datos de acceso:<br>
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
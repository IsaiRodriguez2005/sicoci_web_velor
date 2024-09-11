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
        $insertUsuario = "INSERT INTO usuarios VALUES(NULL, ".$_POST['emisor'].", '".strtoupper($_POST['nombre'])."', '".$_POST['correo']."', '".$_POST['password']."', 1)";
        mysqli_query($conexion, $insertUsuario);

        if($_POST['emisor'] != 0)
        {
            $selectMAX = "SELECT MAX(id_usuario) AS no_usuario FROM usuarios";
            $resMAX = mysqli_query($conexion, $selectMAX);
            $max = mysqli_fetch_array($resMAX);
            $ultimo = $max['no_usuario'] + 1;

            $insertPermisos = "INSERT INTO usuarios_permisos VALUES(NULL, ".$_POST['emisor'].", ".$ultimo.", 0)";
            mysqli_query($conexion, $insertPermisos);
        }

        if($_POST['emisor'] == 0)
        {
            $razon_social = "NO APLICA";
        }
        else
        {
            $selectRazon = "SELECT nombre_social FROM emisores WHERE id_emisor=".$_POST['emisor'];
            $resRazon = mysqli_query($conexion, $selectRazon);
            $razon = mysqli_fetch_array($resRazon);
            $razon_social = $razon['nombre_social'];
        }

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
                <b>Enlace:</b> https://erp.velor.mx<br>
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
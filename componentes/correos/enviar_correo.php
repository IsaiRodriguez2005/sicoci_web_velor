<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

function enviarCorreo($correoDestinatario, $a, $c)
{
    $mail = new PHPMailer();


    try {
        // Configuraciones del servidor SMTP
        $mail->isSMTP();                                            // Usar SMTP
        $mail->Host       = 'mail.velor.mx';                       // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
        $mail->Username   = 'notificaciones@velor.mx';                   // Tu correo de Gmail
        $mail->Password   = 'notificaciones@velor.mx';                  // Contraseña o token de Gmail
        $mail->SMTPSecure = 'ssl';         // Habilitar TLS (seguridad)
        $mail->Port       = 465;                                    // Puerto SMTP para TLS

        // Configuración del correo
        $mail->setFrom('notificaciones@velor.mx', 'VELOR NOTIFICADOR');          // Dirección del remitente
        $mail->addAddress($correoDestinatario);   // Dirección del destinatario
        $mail->isHTML(true);           
        $mail->Subject = $a;
        $mail->Body    = $c;

        // Enviar correo
        $mail->send();
        echo 'Correo enviado exitosamente.';
    } catch (Exception $e) {
        echo "Error al enviar el correo. Error: {$mail->ErrorInfo}";
    }
}

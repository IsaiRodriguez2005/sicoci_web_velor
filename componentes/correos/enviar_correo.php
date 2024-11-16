<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ .'../../../vendor/autoload.php';
require __DIR__ .'../../correos/estructura_mensaje.php';
include __DIR__ .'../../correos/funciones.php';

function enviarCorreo($correoDestinatario, $a, $c)
{
    $mail = new PHPMailer();

    $body = estuturaCorreoHTML($c);
    try {
        // Configuraciones del servidor SMTP
        $mail->isSMTP();                                            // Usar SMTP
        $mail->Host       = 'mail.velor.mx';                       // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
        $mail->Username   = 'cosera@velor.mx';                   // Tu correo de Gmail
        $mail->Password   = 'JlkTbGyv?F@B';                  // Contraseña o token de Gmail
        $mail->SMTPSecure = 'ssl';         // Habilitar TLS (seguridad)
        $mail->Port       = 465;                                    // Puerto SMTP para TLS

        // Configuración del correo
        $mail->setFrom('cosera@velor.mx', 'COSERA NOTIFICADOR');          // Dirección del remitente
        $mail->addAddress($correoDestinatario);   // Dirección del destinatario 
        $mail->isHTML(true);           
        $mail->Subject = $a;
        $mail->Body    = $body;

        // Enviar correo
        if($mail->send()){
            return 1;
        } else {
            return 0;
        }
    } catch (Exception $e) {
        return 2;
    }
}


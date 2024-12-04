<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ .'../../../vendor/autoload.php';
require __DIR__ .'../../correos/estructura_mensaje.php';
include __DIR__ .'../../correos/funciones.php';

function enviarCorreo($correoDestinatario, $a, $c, $folioCita = null, $idCliente = null, $idTerapeuta = null)
{
    
    $mail = new PHPMailer();

    // print_r($mail);
    $body = estuturaCorreoHTML($c, intval($folioCita), intval($idCliente), intval($idTerapeuta));
    try {
        // Configuraciones del servidor SMTP
        $mail->isSMTP();                                            // Usar SMTP
        $mail->Host       = 'mail.kyosoft.mx';                       // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
        $mail->Username   = 'cosera.notificaciones@kyosoft.mx';                   // Tu correo de Gmail
        $mail->Password   = '%;!uQ5frX7t;';                  // Contraseña o token de Gmail
        $mail->SMTPSecure = 'tls';         // Habilitar TLS (seguridad)
        $mail->Port       = 587;                                    // Puerto SMTP para TLS

        // Configuración del correo
        $mail->setFrom('cosera.notificaciones@kyosoft.mx', 'COSERA NOTIFICADOR');          // Dirección del remitente
        $mail->addAddress($correoDestinatario);   // Dirección del destinatario 
        $mail->isHTML(true);           
        $mail->Subject = $a;
        $mail->Body    = $body;
        
        //* depuracion 
        //$mail->SMTPDebug = 3; // Nivel de depuración. Más detalles en el nivel 3.
        //$mail->Debugoutput = 'html'; // Formato de salida de la depuración (puede ser 'html' o 'echo').


        // Enviar correo
        if($mail->send()){
            return 1;
        } else {
            return $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        return 2;
    }
}

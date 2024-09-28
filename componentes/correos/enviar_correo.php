<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer();

try {
    // Configuraciones del servidor SMTP
    $mail->isSMTP();                                            // Usar SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Servidor SMTP de Gmail
    $mail->SMTPAuth   = true;                                   // Habilitar autenticación SMTP
    $mail->Username   = 'tu_email@gmail.com';                   // Tu correo de Gmail
    $mail->Password   = 'tu_password_o_token';                  // Contraseña o token de Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Habilitar TLS (seguridad)
    $mail->Port       = 587;                                    // Puerto SMTP para TLS

    // Configuración del correo
    $mail->setFrom('tu_email@gmail.com', 'Tu Nombre');          // Dirección del remitente
    $mail->addAddress('destinatario@ejemplo.com');              // Dirección del destinatario
    $mail->Subject = 'Asunto del correo';
    $mail->Body    = 'Este es el cuerpo del correo en texto plano.';
    
    // Enviar correo
    $mail->send();
    echo 'Correo enviado exitosamente.';
} catch (Exception $e) {
    echo "Error al enviar el correo. Error: {$mail->ErrorInfo}";
}
?>

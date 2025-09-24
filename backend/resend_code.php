<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

include("conexion.php");

// Función para generar código de 6 dígitos
function generarCodigoVerificacion() {
    return sprintf('%06d', rand(0, 999999));
}

// Función para enviar email con PHPMailer
function enviarEmailVerificacion($correo, $nombre, $codigo) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreplysafepath@gmail.com';
        $mail->Password   = 'kqas eaih tefe pxuj';  // Usa contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Configuración del correo
        $mail->setFrom('noreplysafepath@gmail.com', 'SafePath');
        $mail->addAddress($correo, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'SafePath - Nuevo Código de Verificación';
        $mail->Body    = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; color: #1E2A78; margin-bottom: 20px; }
                .code { font-size: 32px; font-weight: bold; color: #2196F3; text-align: center; 
                       letter-spacing: 8px; margin: 20px 0; padding: 20px; 
                       background-color: #f8f9fa; border-radius: 8px; border: 2px solid #2196F3; }
                .footer { color: #666; font-size: 12px; text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; }
                .warning { background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🛡️ SafePath</h1>
                    <h2>Nuevo Código de Verificación</h2>
                </div>
                <p>Hola <strong>$nombre</strong>,</p>
                <p>Has solicitado un nuevo código de verificación para tu cuenta de SafePath.</p>
                
                <p>Tu nuevo código de verificación es:</p>
                <div class='code'>$codigo</div>
                
                <div class='warning'>
                    <strong>⚠️ Importante:</strong> Este código expira en 24 horas por seguridad.
                </div>
                
                <p>Si no solicitaste este código, puedes ignorar este correo de forma segura.</p>
                
                <div class='footer'>
                    <p>© 2025 SafePath. Todos los derechos reservados.</p>
                    <p>Este es un correo automático, por favor no respondas a esta dirección.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error enviando correo: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['email'] ?? '';
    
    if (empty($correo)) {
        echo json_encode(["success" => false, "message" => "Email requerido"]);
        exit;
    }
    
    // Buscar usuario por correo (que no esté verificado)
    $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE correo = ? AND verificado = 0");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($id_usuario, $nombre);
    $stmt->fetch();
    $stmt->close();
    
    if (!$id_usuario) {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado o ya verificado"]);
        exit;
    }
    
    // Eliminar tokens existentes para este usuario
    $delete_stmt = $conn->prepare("DELETE FROM tokens_verificacion WHERE id_usuario = ?");
    $delete_stmt->bind_param("i", $id_usuario);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    // Generar nuevo código
    $codigo = generarCodigoVerificacion();
    
    // Insertar nuevo token
    $insert_stmt = $conn->prepare("INSERT INTO tokens_verificacion (id_usuario, token, fecha_expira) 
                                  VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))");
    $insert_stmt->bind_param("is", $id_usuario, $codigo);
    
    if ($insert_stmt->execute()) {
        // Enviar email
        if (enviarEmailVerificacion($correo, $nombre, $codigo)) {
            echo json_encode(["success" => true, "message" => "Código reenviado correctamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al enviar el correo"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al generar nuevo código"]);
    }
    
    $insert_stmt->close();
    $conn->close();
}
?>
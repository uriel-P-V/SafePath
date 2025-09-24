<?php
// Incluir PHPMailer
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
        // Configuración del servidor SMTP - CORREGIDA
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreplysafepath@gmail.com';
        // CAMBIAR POR TU CONTRASEÑA DE APLICACIÓN REAL
        $mail->Password   = 'kqas eaih tefe pxuj';  // Cambiar esta línea
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Configuración adicional para debugging
        $mail->SMTPDebug = 0; // Cambiar a 2 para ver errores detallados
        $mail->Debugoutput = 'html';

        // Configuración del correo
        $mail->setFrom('noreplysafepath@gmail.com', 'SafePath');
        $mail->addAddress($correo, $nombre);
        $mail->addReplyTo('noreplysafepath@gmail.com', 'SafePath');

        // Configuración del contenido
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'SafePath - Código de Verificación';
        
        $mail->Body = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    background-color: #f4f4f4; 
                    margin: 0; 
                    padding: 20px; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    padding: 30px; 
                    border-radius: 10px; 
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
                }
                .header { 
                    text-align: center; 
                    color: #1E2A78; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #2196F3;
                    padding-bottom: 20px;
                }
                .code { 
                    font-size: 36px; 
                    font-weight: bold; 
                    color: #2196F3; 
                    text-align: center; 
                    letter-spacing: 10px; 
                    margin: 30px 0; 
                    padding: 25px; 
                    background-color: #f8f9fa; 
                    border-radius: 10px; 
                    border: 3px solid #2196F3; 
                }
                .footer { 
                    color: #666; 
                    font-size: 12px; 
                    text-align: center; 
                    margin-top: 30px; 
                    border-top: 1px solid #eee; 
                    padding-top: 15px; 
                }
                .warning { 
                    background-color: #fff3cd; 
                    border: 1px solid #ffeeba; 
                    color: #856404; 
                    padding: 15px; 
                    border-radius: 8px; 
                    margin: 20px 0; 
                }
                .logo {
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .content {
                    line-height: 1.6;
                    color: #333;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🛡️ SafePath</div>
                    <h2>Verificación de Correo Electrónico</h2>
                </div>
                
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>¡Bienvenido a SafePath! Para completar tu registro y activar tu cuenta, necesitamos verificar tu dirección de correo electrónico.</p>
                    
                    <p>Ingresa el siguiente código de verificación en la aplicación:</p>
                    <div class='code'>$codigo</div>
                    
                    <div class='warning'>
                        <strong>⚠️ Importante:</strong> Este código expira en 24 horas por seguridad.
                    </div>
                    
                    <p>Si no solicitaste esta cuenta, puedes ignorar este correo de forma segura.</p>
                    <p>¡Gracias por elegir SafePath para tu seguridad!</p>
                </div>
                
                <div class='footer'>
                    <p>© 2025 SafePath. Todos los derechos reservados.</p>
                    <p>Este es un correo automático, por favor no respondas a esta dirección.</p>
                </div>
            </div>
        </body>
        </html>";

        // Versión texto plano como alternativa
        $mail->AltBody = "
        SafePath - Código de Verificación
        
        Hola $nombre,
        
        Tu código de verificación es: $codigo
        
        Este código expira en 24 horas.
        
        © 2025 SafePath. Todos los derechos reservados.
        ";

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log más detallado del error
        error_log("Error PHPMailer: " . $e->getMessage());
        error_log("SMTP Error: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar datos de entrada
    $nombre   = htmlspecialchars(trim($_POST["fullname"]), ENT_QUOTES, 'UTF-8');
    $correo   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirmPassword"]);
    $telefono = htmlspecialchars(trim($_POST["phone"]), ENT_QUOTES, 'UTF-8');

    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($password) || empty($telefono)) {
        die("Error: Todos los campos son requeridos.");
    }

    // Validar email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Error: Email inválido.");
    }

    // Verificar contraseñas iguales
    if ($password !== $confirm) {
        die("Error: Las contraseñas no coinciden.");
    }

    // Verificar longitud de contraseña
    if (strlen($password) < 8) {
        die("Error: La contraseña debe tener al menos 8 caracteres.");
    }

    // Verificar reCAPTCHA
    $captcha_response = $_POST['g-recaptcha-response'] ?? '';
    $secret_key = "6Lcg_8srAAAAAOEWODLXPtMRPlgdyiXH1nRrjgaJ"; 
    
    if (!empty($captcha_response)) {
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
        $captcha_success = json_decode($verify);
        
        if (!$captcha_success->success) {
            die("Error: Verificación reCAPTCHA fallida.");
        }
    } else {
        die("Error: Completa la verificación reCAPTCHA.");
    }

    // Verificar si correo ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        die("Error en la preparación de consulta: " . $conn->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->close();
        die("Error: El correo ya está registrado.");
    }
    $stmt->close();

    // Hashear contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario (SIN verificar aún)
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, verificado, fecha_registro, telefono) 
                            VALUES (?, ?, ?, 0, NOW(), ?)");
    
    if (!$stmt) {
        die("Error en la preparación de consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $telefono);

    if ($stmt->execute()) {
        $id_usuario = $conn->insert_id;
        
        // Generar código de verificación
        $codigo = generarCodigoVerificacion();
        
        // Limpiar tokens expirados antes de insertar nuevo
        $clean_stmt = $conn->prepare("DELETE FROM tokens_verificacion WHERE fecha_expira < NOW()");
        if ($clean_stmt) {
            $clean_stmt->execute();
            $clean_stmt->close();
        }
        
        // Guardar token en base de datos
        $stmt_token = $conn->prepare("INSERT INTO tokens_verificacion (id_usuario, token, fecha_expira) 
                                     VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 DAY))");
        
        if (!$stmt_token) {
            die("Error preparando token: " . $conn->error);
        }
        
        $stmt_token->bind_param("is", $id_usuario, $codigo);
        
        if ($stmt_token->execute()) {
            // Enviar email
            if (enviarEmailVerificacion($correo, $nombre, $codigo)) {
                echo "<script>
                        alert('✅ ¡Registro exitoso! Hemos enviado un código de verificación a tu correo. Revisa también tu carpeta de SPAM.');
                        window.location.href = '../pages/Email_Verification/Verification.html?email=" . urlencode($correo) . "';
                      </script>";
            } else {
                echo "<script>
                        if (confirm('✅ Usuario registrado correctamente.\\n\\n⚠️ Hubo un problema enviando el correo de verificación.\\n\\n¿Quieres ir a la página de verificación para intentar reenviar el código?')) {
                            window.location.href = '../pages/Email_Verification/Verification.html?email=" . urlencode($correo) . "';
                        } else {
                            window.location.href = '../pages/login/login.html';
                        }
                      </script>";
            }
        } else {
            echo "Error al crear token de verificación: " . $stmt_token->error;
        }
        $stmt_token->close();
        
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
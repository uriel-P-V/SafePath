<?php
include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST["fullname"]);
    $correo   = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirmPassword"]);
    $telefono = trim($_POST["phone"]);

    // 1. Verificar contraseñas iguales
    if ($password !== $confirm) {
        die("Error: Las contraseñas no coinciden.");
    }

    // 2. Validar que sea correo UDG
    if (!preg_match('/^[A-Za-z0-9._%+-]+@(?:[A-Za-z0-9-]+\.)?udg\.mx$/', $correo)) {
        die("Error: Solo se permiten correos @udg.mx");
    }

    // 3. Verificar si correo ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Error: El correo ya está registrado.");
    }
    $stmt->close();

    // 4. Hashear contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 5. Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, verificado, fecha_registro, telefono) 
                            VALUES (?, ?, ?, 0, NOW(), ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $telefono);

    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;

        // 6. Generar token de 6 dígitos
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 7. Guardar token en la tabla tokens_verificacion (10 minutos de vigencia)
        $stmtToken = $conn->prepare("INSERT INTO tokens_verificacion (id_usuario, token, fecha_expira) 
                                     VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        $stmtToken->bind_param("is", $id_usuario, $token);
        $stmtToken->execute();
        $stmtToken->close();

        // 8. Enviar correo con PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '@gmail.com';   // 👈 correo de Gmail que envia los codigos de verificacion 
            $mail->Password = '######';             // 👈 contraseña de aplicación de Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('TU_CORREO@gmail.com', 'SafePath');
            $mail->addAddress($correo, $nombre);

            $mail->isHTML(true);
            $mail->Subject = 'Codigo de verificacion SafePath';
            $mail->Body    = "
                <p>Hola <b>$nombre</b>,</p>
                <p>Tu código de verificación es:</p>
                <h2 style='letter-spacing:5px;'>$token</h2>
                <p>Expira en 10 minutos.</p>
                <br>
                <small>SafePath</small>
            ";

            $mail->send();

            echo "<script>
                    alert('Registro exitoso. Revisa tu correo institucional UDG para verificar tu cuenta.');
                    window.location.href = '../pages/Email_Verification/Verification.html?email=$correo';
                  </script>";
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error en el registro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

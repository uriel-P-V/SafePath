<?php
// Ambos archivos están en la carpeta backend/, así que es include directo
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST["fullname"]);
    $correo   = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirmPassword"]);
    $telefono = trim($_POST["phone"]);

    // Verificar contraseñas iguales
    if ($password !== $confirm) {
        die("Error: Las contraseñas no coinciden.");
    }

    // Verificar reCAPTCHA
    $captcha_response = $_POST['g-recaptcha-response'];
    $secret_key = "6Lcg_8srAAAAAOEWODLXPtMRPlgdyiXH1nRrjgaJ"; 
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$captcha_response}");
    $captcha_success = json_decode($verify);

    if (!$captcha_success->success) {
        die("Error: Verificación reCAPTCHA fallida.");
    }

    // Verificar si correo ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Error: El correo ya está registrado.");
    }
    $stmt->close();

    // Hashear contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, verificado, fecha_registro, telefono) 
                            VALUES (?, ?, ?, 0, NOW(), ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $telefono);

    if ($stmt->execute()) {
        echo "<script>
                alert('✅ Registro exitoso. Ahora puedes iniciar sesión');
                window.location.href = '../pages/login/login.html';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
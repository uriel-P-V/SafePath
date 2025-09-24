<?php
session_start();

// Incluir conexión
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    // Validar campos vacíos
    if (empty($correo) || empty($password)) {
        echo '<script>alert("Por favor completa todos los campos"); window.history.back();</script>';
        exit();
    }
    
    // Buscar usuario
    $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, contraseña, verificado FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        echo '<script>alert("Error en el servidor"); window.history.back();</script>';
        exit();
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar contraseña
        if (password_verify($password, $usuario['contraseña'])) {
            // Crear sesión
            $_SESSION['user_id'] = $usuario['id_usuario'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_email'] = $usuario['correo'];
            $_SESSION['logged_in'] = true;
            
            // Página de éxito moderna
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Login Exitoso - SafePath</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        color: white;
                    }
                    .container {
                        text-align: center;
                        background: rgba(255, 255, 255, 0.1);
                        padding: 40px;
                        border-radius: 15px;
                        backdrop-filter: blur(10px);
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                        max-width: 400px;
                    }
                    h1 {
                        font-size: 2.5em;
                        margin-bottom: 20px;
                    }
                    .checkmark {
                        font-size: 4em;
                        color: #4CAF50;
                        margin-bottom: 20px;
                        animation: scaleIn 0.5s ease-out;
                    }
                    .welcome-message {
                        font-size: 1.2em;
                        margin-bottom: 10px;
                    }
                    .user-name {
                        color: #4CAF50;
                        font-weight: bold;
                    }
                    .loading {
                        margin-top: 30px;
                        font-size: 1.1em;
                        opacity: 0.8;
                    }
                    .spinner {
                        border: 3px solid rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        border-top: 3px solid #4CAF50;
                        width: 30px;
                        height: 30px;
                        animation: spin 1s linear infinite;
                        margin: 20px auto;
                    }
                    @keyframes scaleIn {
                        from { transform: scale(0); }
                        to { transform: scale(1); }
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="checkmark">✅</div>
                    <h1>¡Bienvenido!</h1>
                    <p class="welcome-message">Hola <span class="user-name"><?php echo htmlspecialchars($usuario['nombre']); ?></span></p>
                    <p>Has iniciado sesión exitosamente</p>
                    <div class="loading">
                        <div class="spinner"></div>
                        Redirigiendo al inicio...
                    </div>
                </div>
                
                <script>
                    // Redirigir después de 2 segundos
                    setTimeout(function() {
                        window.location.href = '../index.php';
                    }, 2000);
                </script>
            </body>
            </html>
            <?php
            
        } else {
            echo '<script>alert("La contraseña es incorrecta"); window.history.back();</script>';
        }
        
    } else {
        echo '<script>alert("No existe una cuenta con este correo"); window.history.back();</script>';
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    // Si no es POST, redirigir al login
    header("Location: ../pages/login/login.html");
    exit();
}
?>
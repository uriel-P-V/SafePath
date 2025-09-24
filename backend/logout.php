<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también se debe borrar la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al index con mensaje de éxito
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Cerrada - SafePath</title>
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
            font-size: 2em;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 4em;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 1.1em;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon"></div>
        <h1>¡Hasta luego!</h1>
        <p class="message">Tu sesión ha sido cerrada exitosamente. Gracias por usar SafePath.</p>
        <a href="../index.php" class="btn">Volver al Inicio</a>
    </div>
    
    <script>
        // Redirigir automáticamente después de 3 segundos
        setTimeout(function() {
            window.location.href = '../index.php';
        }, 3000);
    </script>
</body>
</html>
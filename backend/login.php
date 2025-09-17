<?php
session_start();
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
    $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, contraseña, verificado 
                            FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $usuario['contraseña'])) {
            
            // Verificar si está validado
            if ($usuario['verificado'] == 0) {
                echo '<script>alert("Tu cuenta aún no está verificada. Revisa tu correo."); window.history.back();</script>';
                exit();
            }

            // Crear sesión
            $_SESSION['user_id'] = $usuario['id_usuario'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_email'] = $usuario['correo'];
            $_SESSION['logged_in'] = true;

            // Redirigir a home
            header("Location: ../pages/hello.php");
            exit();

        } else {
            echo '<script>alert("La contraseña es incorrecta"); window.history.back();</script>';
        }
    } else {
        echo '<script>alert("No existe una cuenta con este correo"); window.history.back();</script>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo '<script>alert("Método inválido"); window.history.back();</script>';
}
?>

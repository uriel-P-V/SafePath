<?php
// Activar errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug Login</title></head><body>";
echo "<h1>DEBUG - Procesando Login</h1>";

session_start();

echo "<p>✅ Sesión iniciada</p>";

// Verificar si es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<p>✅ Método POST recibido</p>";
    
    // Mostrar datos recibidos
    echo "<h3>Datos recibidos:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Incluir conexión
    include("conexion.php");
    echo "<p>✅ Conexión incluida</p>";
    
    $correo = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    echo "<p><strong>Email:</strong> " . htmlspecialchars($correo) . "</p>";
    echo "<p><strong>Password length:</strong> " . strlen($password) . " caracteres</p>";
    
    // Validar campos vacíos
    if (empty($correo) || empty($password)) {
        echo "<p>❌ Campos vacíos detectados</p>";
        echo '<script>alert("Por favor completa todos los campos"); window.history.back();</script>';
        exit();
    }
    
    // Buscar usuario
    $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, contraseña, verificado FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        echo "<p>❌ Error en prepare: " . $conn->error . "</p>";
        exit();
    }
    
    echo "<p>✅ Query preparada</p>";
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<p><strong>Usuarios encontrados:</strong> " . $result->num_rows . "</p>";
    
    if ($result->num_rows == 1) {
        echo "<p>✅ Usuario encontrado</p>";
        $usuario = $result->fetch_assoc();
        
        echo "<p><strong>Usuario en BD:</strong> " . htmlspecialchars($usuario['nombre']) . "</p>";
        
        // Verificar contraseña
        if (password_verify($password, $usuario['contraseña'])) {
            echo "<p>✅ Contraseña correcta</p>";
            
            // Crear sesión
            $_SESSION['user_id'] = $usuario['id_usuario'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_email'] = $usuario['correo'];
            $_SESSION['logged_in'] = true;
            
            echo "<p>✅ Sesión creada</p>";
            echo "<p><strong>Redireccionando a hello.html...</strong></p>";
            
            // RUTA CORREGIDA: desde backend/ hacia pages/hello.html
            echo '<script>
                    setTimeout(function(){
                        window.location.href = "../pages/hello.html";
                    }, 3000);
                  </script>';
            
        } else {
            echo "<p>❌ Contraseña incorrecta</p>";
            echo '<script>alert("La contraseña es incorrecta"); window.history.back();</script>';
        }
        
    } else {
        echo "<p>❌ Usuario no encontrado</p>";
        echo '<script>alert("No existe una cuenta con este correo"); window.history.back();</script>';
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    echo "<p>❌ No es método POST</p>";
    echo "<p>Método recibido: " . $_SERVER["REQUEST_METHOD"] . "</p>";
}

echo "</body></html>";
?>
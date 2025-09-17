<?php
include '../backend/conexion.php';

// Recibir datos del formulario
$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];
$telefono = $_POST['telefono'];

// Validar que no exista el correo
$check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
$check->bind_param("s", $correo);
$check->execute();
$check->store_result();

if($check->num_rows > 0){
    echo "El correo ya está registrado";
    exit;
}

// Hashear la contraseña
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Insertar usuario en la base de datos
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, telefono) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $correo, $hash, $telefono);

if($stmt->execute()){
    echo "Usuario registrado correctamente";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

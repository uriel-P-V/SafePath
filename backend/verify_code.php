<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['email'] ?? '';
    $codigo = $_POST['code'] ?? '';

    // Buscar id_usuario por correo
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo=?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($id_usuario);
    $stmt->fetch();
    $stmt->close();

    if (!$id_usuario) {
        echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
        exit;
    }

    // Validar token
    $stmt = $conn->prepare("SELECT id_token FROM tokens_verificacion 
                            WHERE id_usuario=? AND token=? AND fecha_expira > NOW()");
    $stmt->bind_param("is", $id_usuario, $codigo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Marcar usuario como verificado
        $update = $conn->prepare("UPDATE usuarios SET verificado=1 WHERE id_usuario=?");
        $update->bind_param("i", $id_usuario);
        $update->execute();
        $update->close();

        // Eliminar token usado
        $del = $conn->prepare("DELETE FROM tokens_verificacion WHERE id_usuario=?");
        $del->bind_param("i", $id_usuario);
        $del->execute();
        $del->close();

        echo json_encode(["success" => true, "message" => "Cuenta verificada correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Código inválido o expirado"]);
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
// Iniciar sesión
session_start();

// Configurar headers para JSON y permitir CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar si el usuario está logueado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Usuario logueado
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'],
        'user_email' => $_SESSION['user_email']
    ]);
} else {
    // Usuario no logueado
    echo json_encode([
        'logged_in' => false
    ]);
}
?>
<?php
session_start();
header('Content-Type: application/json');

// Si el usuario está logueado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Abreviar nombre 
    $nombre = $_SESSION['user_name'];
    $partes = explode(" ", trim($nombre));
    $abreviado = $partes[0];
    if (count($partes) > 1) {
        $abreviado .= " " . strtoupper(substr($partes[1], 0, 1)) . ".";
    }

    echo json_encode([
        "logged_in" => true,
        "user_name" => $abreviado,
        "email" => $_SESSION['user_email']
    ]);
} else {
    echo json_encode([
        "logged_in" => false
    ]);
}

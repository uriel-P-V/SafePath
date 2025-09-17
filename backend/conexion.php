<?php
$host = "localhost";
$usuario = "root";
$password = ""; // contraseña por defecto en XAMPP
$base_datos = "safepath";

$conn = new mysqli($host, $usuario, $password, $base_datos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
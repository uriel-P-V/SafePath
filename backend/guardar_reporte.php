<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verificar si el reporte debe ser anónimo
    $es_anonimo = isset($_POST['anonimo']) && $_POST['anonimo'] == '1';
    
    if ($es_anonimo) {
        // Usar usuario anónimo (ID -1)
        $id_usuario = -1;
    } else {
        // Verificar que haya sesión activa para reportes no anónimos
        if (!isset($_SESSION['user_id'])) {
            echo "Error: Debes iniciar sesión para enviar reportes no anónimos";
            exit;
        }
        $id_usuario = $_SESSION['user_id'];
    }

    $ubicacion   = $_POST['ubicacion'];
    $fecha_hora  = !empty($_POST['fecha_hora']) ? $_POST['fecha_hora'] : date("Y-m-d H:i:s"); 
    $tipo        = $_POST['tipo'];
    $riesgo      = $_POST['riesgo'];
    $descripcion = $_POST['descripcion'];
    $latitud     = $_POST['latitud'] ?? 0;
    $longitud    = $_POST['longitud'] ?? 0;

    $nivel_peligro = match($riesgo) {
        "Bajo"     => 1,
        "Medio"    => 2,
        "Alto"     => 3,
        "Crítico"  => 5,
        default    => 1
    };

    // Rango horario
    $hora = (int)date("H", strtotime($fecha_hora));
    if ($hora >= 0 && $hora < 6) $rango = "Madrugada";
    elseif ($hora >= 6 && $hora < 12) $rango = "Día";
    elseif ($hora >= 12 && $hora < 18) $rango = "Tarde";
    else $rango = "Noche";

    // Imagen
    $imagen = NULL;
    if (!empty($_FILES["evidencia"]["name"])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        // Para reportes anónimos, usar un nombre más genérico para proteger la privacidad
        if ($es_anonimo) {
            $fileName = "anon_" . uniqid() . "_" . pathinfo($_FILES["evidencia"]["name"], PATHINFO_EXTENSION);
        } else {
            $fileName = time() . "_" . basename($_FILES["evidencia"]["name"]);
        }
        
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["evidencia"]["tmp_name"], $targetFile)) {
            $imagen = $fileName;
        } else {
            echo "Error al subir el archivo";
            exit;
        }
    }

    $sql = "INSERT INTO reportes 
    (id_usuario, tipo_incidente, descripcion, imagen, latitud, longitud, direccion, fecha_reporte, nivel_peligro, rango_horario)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssddssis", $id_usuario, $tipo, $descripcion, $imagen, $latitud, $longitud, $ubicacion, $fecha_hora, $nivel_peligro, $rango);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
}
?>
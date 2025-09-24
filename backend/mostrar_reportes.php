<?php
include("conexion.php");

// Consulta: reportes + datos del usuario
$sql = "SELECT r.*, u.nombre, u.correo
        FROM reportes r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        ORDER BY r.fecha_reporte DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes Registrados</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 20px;
    }
    .reporte {
      background: #fff;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }
    .reporte h3 {
      margin: 0 0 10px;
      color: #333;
    }
    .reporte p {
      margin: 5px 0;
      color: #555;
    }
    .reporte img {
      margin-top: 10px;
      max-width: 300px;
      border-radius: 8px;
      border: 1px solid #ddd;
    }
  </style>
</head>
<body>

<h2>Lista de Reportes</h2>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){ ?>
        <div class="reporte">
            <h3><?= htmlspecialchars($row['tipo_incidente']) ?></h3>
            <p><b>Descripción:</b> <?= htmlspecialchars($row['descripcion']) ?></p>
            <p><b>Reportado por:</b> <?= htmlspecialchars($row['nombre']) ?> (<?= htmlspecialchars($row['correo']) ?>)</p>
            <p><b>Fecha:</b> <?= $row['fecha_reporte'] ?></p>
            <p><b>Rango horario:</b> <?= $row['rango_horario'] ?></p>
            <p><b>Nivel de peligro:</b> <?= $row['nivel_peligro'] ?></p>
            <p><b>Ubicación:</b> <?= htmlspecialchars($row['direccion']) ?> (<?= $row['latitud'] ?>, <?= $row['longitud'] ?>)</p>
            <?php if(!empty($row['imagen'])): ?>
                <img src="../uploads/<?= $row['imagen'] ?>" alt="Evidencia">
            <?php else: ?>
                <p><i>Sin evidencia</i></p>
            <?php endif; ?>
        </div>
<?php }
} else {
    echo "<p>No hay reportes aún.</p>";
}
?>

</body>
</html>

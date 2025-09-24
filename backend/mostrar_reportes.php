<?php
include("conexion.php");

// Consulta: reportes + datos del usuario (incluyendo reportes anónimos)
$sql = "SELECT r.*, 
               CASE 
                   WHEN r.id_usuario = 0 THEN 'Anónimo'
                   ELSE COALESCE(u.nombre, 'Usuario Eliminado')
               END as nombre,
               CASE 
                   WHEN r.id_usuario = 0 THEN 'reporte.anonimo@sistema.local'
                   ELSE COALESCE(u.correo, 'correo@eliminado.com')
               END as correo
        FROM reportes r
        LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario AND r.id_usuario != 0
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
      position: relative;
    }
    .reporte.anonimo {
      border-left: 4px solid #6c757d;
      background: #f8f9fa;
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
    .anonymous-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #6c757d;
      color: white;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 12px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .stats-bar {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: flex;
      justify-content: space-around;
      text-align: center;
    }
    .stat-item {
      flex: 1;
    }
    .stat-number {
      font-size: 24px;
      font-weight: bold;
      color: #007bff;
    }
    .stat-label {
      color: #666;
      font-size: 14px;
    }
  </style>
</head>
<body>

<h2>Lista de Reportes</h2>

<?php
// Calcular estadísticas
$stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN id_usuario = 0 THEN 1 ELSE 0 END) as anonimos,
                SUM(CASE WHEN id_usuario != 0 THEN 1 ELSE 0 END) as identificados
              FROM reportes";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<div class="stats-bar">
  <div class="stat-item">
    <div class="stat-number"><?= $stats['total'] ?></div>
    <div class="stat-label">Total Reportes</div>
  </div>
  <div class="stat-item">
    <div class="stat-number"><?= $stats['identificados'] ?></div>
    <div class="stat-label">Con Identidad</div>
  </div>
  <div class="stat-item">
    <div class="stat-number"><?= $stats['anonimos'] ?></div>
    <div class="stat-label">Anónimos</div>
  </div>
</div>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){ 
        $isAnonymous = ($row['id_usuario'] == 0);
        ?>
        <div class="reporte <?= $isAnonymous ? 'anonimo' : '' ?>">
            <?php if($isAnonymous): ?>
                <div class="anonymous-badge">
                    <i class="fa-solid fa-user-secret"></i>
                    Anónimo
                </div>
            <?php endif; ?>
            
            <h3><?= htmlspecialchars($row['tipo_incidente']) ?></h3>
            <p><b>Descripción:</b> <?= htmlspecialchars($row['descripcion']) ?></p>
            
            <?php if($isAnonymous): ?>
                <p><b>Reportado por:</b> Usuario Anónimo</p>
                <p style="font-size: 12px; color: #888;"><i>Este reporte fue enviado de forma anónima para proteger la privacidad del usuario</i></p>
            <?php else: ?>
                <p><b>Reportado por:</b> <?= htmlspecialchars($row['nombre']) ?> (<?= htmlspecialchars($row['correo']) ?>)</p>
            <?php endif; ?>
            
            <p><b>Fecha:</b> <?= $row['fecha_reporte'] ?></p>
            <p><b>Rango horario:</b> <?= $row['rango_horario'] ?></p>
            <p><b>Nivel de peligro:</b> <?= $row['nivel_peligro'] ?></p>
            <p><b>Ubicación:</b> <?= htmlspecialchars($row['direccion']) ?> 
                <?php if($row['latitud'] != 0 && $row['longitud'] != 0): ?>
                    (<?= $row['latitud'] ?>, <?= $row['longitud'] ?>)
                <?php endif; ?>
            </p>
            
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
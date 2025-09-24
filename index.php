<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SafePath - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/global.css">
  <style>
    /* Estilos para el dropdown del usuario */
    .user-menu {
      position: relative;
      display: inline-block;
    }
    
    .user-greeting {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 10px 15px;
      border-radius: 25px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
      border: none;
      text-decoration: none;
      font-size: 14px;
    }
    
    .user-greeting:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .user-greeting i {
      font-size: 14px;
      transition: transform 0.3s ease;
    }
    
    .dropdown-menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      padding: 10px 0;
      min-width: 180px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
      border: 1px solid #e1e5e9;
    }
    
    .dropdown-menu.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    
    .dropdown-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 20px;
      color: #333;
      text-decoration: none;
      transition: background-color 0.2s;
      font-size: 14px;
    }
    
    .dropdown-item:hover {
      background-color: #f8f9fa;
    }
    
    .dropdown-item.logout {
      color: #dc3545;
      border-top: 1px solid #e1e5e9;
      margin-top: 5px;
    }
    
    .dropdown-item.logout:hover {
      background-color: #fff5f5;
    }
    
    /* Estilos para los enlaces de login cuando no hay sesión */
    nav a {
      color: #333;
      text-decoration: none;
      margin-left: 20px;
      padding: 10px 20px;
      border-radius: 25px;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    
    nav a:first-child {
      background: transparent;
      border: 2px solid #667eea;
      color: #667eea;
    }
    
    nav a:first-child:hover {
      background: #667eea;
      color: white;
    }
    
    nav a:last-child {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
    }
    
    nav a:last-child:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    /* Estilos para tarjetas deshabilitadas */
    .card.disabled {
      opacity: 0.6;
      position: relative;
      overflow: hidden;
    }
    
    .card.disabled::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.3);
      z-index: 1;
    }

    .card.disabled a {
      background: #ccc !important;
      color: #666 !important;
      cursor: not-allowed;
      pointer-events: none;
      position: relative;
      z-index: 2;
    }

    .card.disabled a:hover {
      transform: none !important;
      box-shadow: none !important;
    }

    /* Mensaje de requerimiento de sesión */
    .login-required-msg {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(220, 53, 69, 0.9);
      color: white;
      padding: 8px 12px;
      border-radius: 5px;
      font-size: 12px;
      z-index: 3;
      white-space: nowrap;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .card.disabled:hover .login-required-msg {
      opacity: 1;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .user-greeting {
        padding: 8px 12px;
        font-size: 13px;
      }
      
      .dropdown-menu {
        min-width: 160px;
      }
      
      nav a {
        margin-left: 10px;
        padding: 8px 15px;
        font-size: 13px;
      }

      .login-required-msg {
        font-size: 11px;
        padding: 6px 10px;
      }
    }
  </style>
</head>
<body>

  <!-- HEADER MEJORADO -->
  <header>
    <div class="logo">
      <i class="fas fa-shield-alt logo-icon"></i>
      <h1>SafePath</h1>
    </div>
    <nav>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <!-- Usuario logueado - mostrar menú de usuario -->
        <div class="user-menu">
          <button class="user-greeting" onclick="toggleDropdown()">
            <i class="fas fa-user-circle"></i>
            ¡Hola <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            <i class="fas fa-chevron-down" id="dropdownIcon"></i>
          </button>
          <div class="dropdown-menu" id="dropdownMenu">
            <a href="pages/profile/profile.html" class="dropdown-item">
              <i class="fas fa-user"></i> Mi Perfil
            </a>
            <a href="pages/report/mis_reportes.html" class="dropdown-item">
              <i class="fas fa-clipboard-list"></i> Mis Reportes
            </a>
            <a href="backend/mostrar_reportes.php" class="dropdown-item">
              <i class="fas fa-list"></i> Ver Reportes
            </a>
            <a href="backend/logout.php" class="dropdown-item logout">
              <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Usuario no logueado - mostrar enlaces de login -->
        <a href="pages/login/login.html">Iniciar Sesión</a>
        <a href="pages/Account_Creation/Account_Creation.html">Crear Cuenta</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main>
    <!-- HERO MEJORADO -->
    <section class="hero">
      <h2>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
          Bienvenido de nuevo,<br> <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <?php else: ?>
          Bienvenido a <span>SafePath</span>
        <?php endif; ?>
      </h2>
      <p>Tu plataforma segura para registrar incidentes y planear trayectorias confiables. Selecciona una de las opciones para continuar.</p>
    </section>

    <!-- CARDS MEJORADAS -->
    <section class="cards">
      <div class="card <?php echo (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) ? 'disabled' : ''; ?>">
        <i class="fa-solid fa-clipboard-list"></i>
        <h3>Generar Reporte</h3>
        <p>Informa un incidente de manera rápida y sencilla para ayudar a mantener segura tu comunidad.</p>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
          <a href="pages/report/Reporte.html">Reportar Ahora</a>
        <?php else: ?>
          <a href="#" onclick="return false;">Reportar Ahora</a>
          <div class="login-required-msg">
            <i class="fas fa-lock"></i> Inicia sesión para reportar
          </div>
        <?php endif; ?>
      </div>

      <div class="card">
        <i class="fa-solid fa-route"></i>
        <h3>Trayectoria Segura</h3>
        <p>Consulta y planifica rutas seguras en tu zona basadas en reportes recientes de la comunidad.</p>
        <a href="trayectoria.html">Ver Trayectoria</a>
      </div>
    </section>

    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
    <!-- Mensaje informativo cuando no hay sesión -->
   
    <?php endif; ?>
  </main>

  <!-- FOOTER -->
  <footer>
    <p>&copy; 2025 SafePath - Todos los derechos reservados</p>
  </footer>

  <script>
    // Función para mostrar/ocultar el dropdown
    function toggleDropdown() {
      const dropdown = document.getElementById('dropdownMenu');
      const icon = document.getElementById('dropdownIcon');
      
      if (dropdown && icon) {
        dropdown.classList.toggle('show');
        
        // Animar el icono
        if (dropdown.classList.contains('show')) {
          icon.style.transform = 'rotate(180deg)';
        } else {
          icon.style.transform = 'rotate(0deg)';
        }
      }
    }

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(event) {
      const userMenu = document.querySelector('.user-menu');
      const dropdown = document.getElementById('dropdownMenu');
      
      if (dropdown && userMenu && !userMenu.contains(event.target)) {
        dropdown.classList.remove('show');
        const icon = document.getElementById('dropdownIcon');
        if (icon) {
          icon.style.transform = 'rotate(0deg)';
        }
      }
    });
  </script>

</body>
</html>
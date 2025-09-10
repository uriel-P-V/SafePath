document.addEventListener("DOMContentLoaded", function() {
  fetch('assets/html/navbar.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById("navbar-container").innerHTML = data;
      // Funcionalidad responsive estilo W3Schools
      const navbar = document.getElementById("myNavbar");
      const icon = navbar.querySelector('.icon');
      if (icon && navbar) {
        icon.addEventListener('click', function() {
          if (navbar.className === "navbar") {
            navbar.className += " responsive";
          } else {
            navbar.className = "navbar";
          }
        });
      }
    });
});
// login.js: lógica básica para el formulario de login

document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.login-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      // Aquí puedes agregar la lógica de autenticación
      alert('Inicio de sesión simulado.');
    });
  }
});

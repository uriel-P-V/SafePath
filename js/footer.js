document.addEventListener("DOMContentLoaded", function() {
  fetch('assets/html/footer.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById("footer-container").innerHTML = data;
    });
});

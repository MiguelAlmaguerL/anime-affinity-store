<!-- /admin/login.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión - Panel de Administración</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      border-radius: 12px;
      background: #ffffff;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h3 class="text-center mb-4">Panel de Administración</h3>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="procesar_login.php" method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="usuario" class="form-label">Correo Electrónico</label>
        <input type="text" name="usuario" id="usuario" class="form-control" required>
        <div class="invalid-feedback">Ingresa un correo electrónico.</div>
      </div>

      <div class="mb-3">
        <label for="contrasena" class="form-label">Contraseña</label>
        <div class="input-group">
          <input type="password" name="contrasena" id="contrasena" class="form-control" required>
          <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="bi bi-eye" id="iconPassword"></i>
          </button>
          <div class="invalid-feedback">Ingresa tu contraseña.</div>
        </div>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación de formulario -->
<script>
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>

  <script>
  document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('contrasena');
    const icon = document.getElementById('iconPassword');
    const isPassword = passwordInput.type === 'password';

    passwordInput.type = isPassword ? 'text' : 'password';
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
  });
</script>
</body>
</html>

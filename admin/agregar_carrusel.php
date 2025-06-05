<?php
session_start();
if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Imagen al Carrusel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .img-wrapper {
            max-width: 300px;
            position: relative;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }

        .preview-img {
            max-height: 250px;
            object-fit: contain;
            width: 100%;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Agregar Imagen al Carrusel</h2>

    <?php if (isset($_SESSION['error_carrusel'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_carrusel']; unset($_SESSION['error_carrusel']); ?>
        </div>
    <?php endif; ?>

    
    <form action="procesar_agregar_carrusel.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <!-- Título -->    
    <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
            <div class="invalid-feedback">Ingrese un título para la imagen.</div>
        </div>

        <!-- Orden -->
        <div class="mb-3">
            <label for="orden" class="form-label">Orden</label>
            <input type="number" name="orden" id="orden" class="form-control" min="0" required>
            <div class="invalid-feedback">Ingrese el orden de aparición en el carrusel.</div>
        </div>

        <!-- Esatus de la imagen -->
        <div class="mb-3">
            <label for="activo" class="form-label">¿La imagen estara activa en el carrusel?</label>
            <select name="activo" id="activo" class="form-select" required>
                <option value="" selected disabled>Seleccione una opción</option>
                <option value="true">Sí</option>
                <option value="false">No</option>
            </select>
            <div class="invalid-feedback">Seleccione si la imagen estará activa en el carrusel.</div>
        </div>

        <!-- Selección de imagen -->
        <div class="mb-3">
            <label for="imagen" class="form-label">Seleccionar Imagen</label>
            <input class="form-control" type="file" name="imagen" id="imagen" accept="image/*" required>
            <div class="invalid-feedback">Debe seleccionar una imagen válida.</div>
        </div>

        <!-- Previsualización de la imagen -->
        <div class="mb-3" id="preview-container" style="display: none;">
            <div class="img-wrapper">
                <img id="preview-img" src="#" alt="Previsualización" class="preview-img">
                <!-- Botón flotante tipo ❌ -->
                <button type="button" id="remove-img-btn" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" title="Eliminar imagen">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <!-- Botones de acción -->
        <button type="submit" class="btn btn-success">Agregar al Carrusel</button>
        <a href="productos.php" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>

<!-- Validación de formulario -->
<script>
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<!-- Previsualización de imagen -->
<script>
    function clearFileInput() {
        const fileInput = document.getElementById('imagen');
        fileInput.value = ''; // Resetea directamente el input file (esto sí funciona)

        // También ocultamos la previsualización
        document.getElementById('preview-img').src = '#';
        document.getElementById('preview-container').style.display = 'none';
    }

    function handleImageChange(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('preview-container');
        const previewImg = document.getElementById('preview-img');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            clearFileInput();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('imagen').addEventListener('change', handleImageChange);

        document.getElementById('remove-img-btn').addEventListener('click', function () {
            clearFileInput();
        });
    });
</script>



</body>
</html>

<!-- Barra de navegación personalizada -->
<nav class="navbar navbar-expand-lg custom-navbar fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">

    <!-- Logo y links a la izquierda -->
    <div class="d-flex align-items-center">
      <a class="navbar-brand me-4" href="index.php">
        <img src="assets/img/logo.jpg" alt="Logo Anime Affinity" height="70">
      </a>

      <!-- Botón del menú (visible en móvil) -->
      <button class="navbar-toggler d-lg-none border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <ul class="navbar-nav d-none d-lg-flex flex-row gap-4">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="productos.php">Ver Productos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="preventas.php">Productos Preventa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="pedidos.php">Pedidos Especiales</a>
        </li>
      </ul>
    </div>

    <!-- Buscador a la derecha -->
    <div class="search-wrapper">
      <input type="text" class="form-control search-input" placeholder="Buscar producto..." id="inputBusqueda">
      <button class="btn search-btn" type="submit">
        <img src="assets/icons/lupa.png" alt="Buscar">
      </button>
      <ul id="resultados" class="list-group position-absolute"></ul>
    </div>
  </div>
</nav>


<!-- Búsqueda vista móvil -->
<div id="mobile-search-container" class="d-lg-none px-3 py-2" style="display: none;">
  <input type="text" class="form-control search-input w-100" placeholder="Buscar..." id="mobileInputBusqueda">
  <ul id="mobileResultados" class="list-group mt-2"></ul>
</div>

<!-- MENÚ DESPLEGABLE (OFFCANVAS) -->
<div class="offcanvas offcanvas-start custom-offcanvas" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasMenuLabel">Anime Affinity Store</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>

  <div class="offcanvas-body">
    <!-- Búsqueda en modo móvil -->
    <form class="d-flex mb-3" role="search" action="buscar.php" method="GET">
      <input class="form-control me-2" type="search" name="query" placeholder="Buscar..." aria-label="Buscar">
      <button class="btn btn-outline-light" type="submit">
        <img src="assets/icons/lupa.png" alt="Buscar" style="height: 20px;">
      </button>
    </form>

    <!-- Links -->
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link text-white" href="index.php">Inicio</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="productos.php">Ver Productos</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="preventas.php">Productos Preventa</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="pedidos.php">Pedidos Especiales</a></li>
    </ul>
  </div>
</div>


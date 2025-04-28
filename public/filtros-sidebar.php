<!-- filtros-sidebar.php -->
<div class="filtros-sidebar <?php if(isset($desdeOffcanvas) && $desdeOffcanvas) echo 'desde-offcanvas'; ?>">
  <h5>Filtrar por:</h5>

  <!-- Filtro de Categoría (Escalas) -->
  <div class="filtro-seccion">
    <h6>Categoría</h6>
    <select class="form-select mb-3" id="categoriaFiltro">
      <option value="todas">Todas</option>
      <option value="1-6">1/6 Scale</option>
      <option value="1-4">1/4 Scale</option>
      <option value="1-7">1/7 Scale</option>
      <option value="1-8">1/8 Scale</option>
    </select>
  </div>

  <!-- Filtro de Orden -->
  <div class="filtro-seccion">
    <h6>Ordenar</h6>
    <select class="form-select mb-3" id="ordenFiltro">
      <option value="az">Nombre (A - Z)</option>
      <option value="za">Nombre (Z - A)</option>
      <option value="precio-menor">Precio Menor</option>
      <option value="precio-mayor">Precio Mayor</option>
    </select>
  </div>

  <!-- Filtro de Estudio -->
  <div class="filtro-seccion">
    <h6>Estudio</h6>
    <select class="form-select mb-3" id="estudioFiltro">
      <option value="todos">Todos</option>
      <option value="banpresto">Banpresto</option>
      <option value="goodsmile">Good Smile Company</option>
      <option value="animester">Animester</option>
      <option value="freeing">FREEing</option>
      <option value="skytube">Skytube</option>
      <option value="hobbysakura">Hobby Sakura</option>
    </select>
  </div>

  <!-- Filtro de Precio -->
  <div class="filtro-seccion">
    <h6>Precio</h6>
    <ul class="lista-filtros">
      <li><input type="checkbox" id="p1"> <label for="p1">Menos de $1,000</label></li>
      <li><input type="checkbox" id="p2"> <label for="p2">$1,000 - $5,000</label></li>
      <li><input type="checkbox" id="p3"> <label for="p3">Más de $5,000</label></li>
    </ul>
  </div>

  <!-- Botón Aplicar Filtros -->
  <div class="d-grid mt-3">
    <button class="btn btn-danger">Aplicar Filtros</button>
  </div>
</div>

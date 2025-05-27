<!-- filtros-sidebar.php -->
  <div class="filtros-sidebar <?php if(isset($desdeOffcanvas) && $desdeOffcanvas) echo 'desde-offcanvas'; ?>">
    <form method="GET" action="">
      <h5>Filtrar por:</h5>

      <!-- Filtro de Categoría -->
      <div class="filtro-seccion">
        <h6>Categoría</h6>
        <ul class="lista-filtros">
          <?php foreach ($categorias as $cat): ?>
            <li>
              <label>
                <input type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>"
                  <?= (isset($_GET['categorias']) && in_array($cat['id'], $_GET['categorias'])) ? 'checked' : '' ?>>
                <?= htmlspecialchars($cat['nombre']) ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>


      <!-- Filtro de Marcas -->
      <div class="filtro-seccion">
        <h6>Marcas</h6>
        <ul class="lista-filtros">
          <?php foreach ($marcas as $mar): ?>
            <li>
              <label>
                <input type="checkbox" name="marcas[]" value="<?= $mar['id'] ?>"
                  <?= (isset($_GET['marcas']) && in_array($mar['id'], $_GET['marcas'])) ? 'checked' : '' ?>>
                <?= htmlspecialchars($mar['nombre']) ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Filtro de Series -->
      <div class="filtro-seccion">
        <h6>Series</h6>
        <ul class="lista-filtros">
          <?php foreach ($series as $ser): ?>
            <li>
              <label>
                <input type="checkbox" name="series[]" value="<?= $ser['id'] ?>"
                  <?= (isset($_GET['series']) && in_array($ser['id'], $_GET['series'])) ? 'checked' : '' ?>>
                <?= htmlspecialchars($ser['nombre']) ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Filtro de Escalas -->
      <div class="filtro-seccion">
        <h6>Escalas</h6>
        <ul class="lista-filtros">
          <?php foreach ($escalas as $esc): ?>
            <li>
              <label>
                <input type="checkbox" name="escalas[]" value="<?= $esc['id'] ?>"
                  <?= (isset($_GET['escalas']) && in_array($esc['id'], $_GET['escalas'])) ? 'checked' : '' ?>>
                <?= htmlspecialchars($esc['nombre']) ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Filtro de Orden -->
      <div class="filtro-seccion">
        <h6>Orden alfabetico</h6>
        <select class="form-select mb-3" id="ordenFiltro" name="orden">
          <option value="">Sin orden</option>
          <option value="az">Nombre (A - Z)</option>
          <option value="za">Nombre (Z - A)</option>
          <!--<option value="precio-menor">Precio Menor</option>
          <option value="precio-mayor">Precio Mayor</option>-->
        </select>
      </div>

      <!-- Filtro de Precio -->
      <div class="filtro-seccion">
        <h6>Precio</h6>
          <ul class="lista-filtros">
            <li>
              <label>
                <input type="checkbox" name="precio[]" id="p1" value="menos1000"
                  <?= (isset($_GET['precio']) && in_array('menos1000', $_GET['precio'])) ? 'checked' : '' ?>>
                Menos de $1,000
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" name="precio[]" id="p2" value="1000a5000"
                  <?= (isset($_GET['precio']) && in_array('1000a5000', $_GET['precio'])) ? 'checked' : '' ?>>
                $1,000 - $5,000
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" name="precio[]" id="p3" value="mas5000"
                  <?= (isset($_GET['precio']) && in_array('mas5000', $_GET['precio'])) ? 'checked' : '' ?>>
                Más de $5,000
              </label>
            </li>
          </ul>
      </div>

      <!-- Botón Aplicar Filtros -->
      <div class="d-grid mt-3">
        <button type="submit" class="btn btn-danger">Aplicar Filtros</button>
      </div>
    </form>
  </div>
<?php $title = 'Alerta de Stock Bajo'; include 'views/templates/header.php'; ?>

<h2 class="mb-4">⚠️ Alerta de Stock Bajo por Insumo</h2>
<p class="text-muted">
  Aquí se muestran los insumos individuales con <strong>5 unidades o menos</strong>, agrupados por modelo y color. Ingresa la cantidad que deseas pedir para cada uno y crea un nuevo pedido de reposición.
</p>

<div class="card mb-4">
    <div class="card-header"><strong>🔍 Filtrar Alertas</strong></div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>stock/lowStockReport" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="include_zero_stock" value="1" id="include_zero_stock" <?php if (!empty($data['filters']['include_zero_stock'])) echo 'checked'; ?>>
                    <label class="form-check-label" for="include_zero_stock">Incluir insumos con stock 0</label>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                <a href="<?php echo BASE_URL; ?>stock/lowStockReport" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<form action="<?php echo BASE_URL; ?>stock/createReplenishmentOrder" method="POST">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover">
        <thead class="table-danger">
          <tr>
            <th>Modelo</th>
            <th>Color</th>
            <th>Tipo</th>
            <th>Cantidad Actual</th>
            <th>Departamento(s) de Uso</th>
            <th style="width: 150px;">Cantidad a Pedir</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($data['low_stock_items']) && $data['low_stock_items']->num_rows > 0): ?>
            <?php $group_key = 0; ?>
            <?php while ($row = $data['low_stock_items']->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['model_name']); ?></td>
                <td>
                    <?php 
                      $color_class = 'bg-dark';
                      if ($row['color_display_name'] == 'Negro') {
                          $color_class = 'badge-negro';
                      } elseif ($row['color_display_name'] == 'Color') {
                          $color_class = 'badge-gradient-color';
                      }
                    ?>
                    <span class="badge <?php echo $color_class; ?>">
                      <?php echo htmlspecialchars($row['color_display_name']); ?>
                    </span>
                </td>
                <td>
                  <span class="badge <?php echo $row['type'] === 'Tinta' ? 'bg-primary' : 'bg-secondary'; ?>">
                    <?php echo htmlspecialchars($row['type']); ?>
                  </span>
                </td>
                <td>
                  <strong class="text-danger"><?php echo htmlspecialchars($row['calculated_quantity']); ?></strong>
                </td>
                <td><?php echo htmlspecialchars($row['departments'] ?? 'N/A'); ?></td>
                <td>
                  <input type="hidden" name="items[<?php echo $group_key; ?>][stock_ids]" value="<?php echo $row['stock_ids']; ?>">
                  <input type="number" name="items[<?php echo $group_key; ?>][quantity]" class="form-control" min="0" placeholder="Ej: 10">
                </td>
              </tr>
              <?php $group_key++; ?>
            <?php endwhile; ?>
          <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-success">
                  <strong class="fs-5">¡Buenas noticias!</strong><br>
                  <em>No hay insumos con niveles bajos de stock.</em>
                </td>
              </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if (isset($data['low_stock_items']) && $data['low_stock_items']->num_rows > 0): ?>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary btn-lg">📝 Crear Pedido de Reposición</button>
        </div>
    <?php endif; ?>
</form>

<?php include 'views/templates/footer.php'; ?>
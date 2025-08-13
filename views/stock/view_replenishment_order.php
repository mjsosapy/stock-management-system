<?php $title = 'Detalle del Pedido';
include 'views/templates/header.php'; ?>

<style>
  .modelo-resaltado {
    font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
    font-size: 1.1rem;
    /* Un poco más grande */
    font-weight: 600;
    /* Semibold */
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
  <div>
    <h2 class="mb-1">📄 Detalle del Pedido de Reposición #<?php echo htmlspecialchars($data['order_id']); ?></h2>
    <p class="text-muted mb-0">
      Para modificar las cantidades, habilita la edición.
    </p>
  </div>
  <div class="ms-auto">
    <a href="<?php echo BASE_URL; ?>stock/generateOrderPdf/<?php echo $data['order_id']; ?>"
      target="_blank"
      class="btn btn-danger me-3">
      <i class="fas fa-file-pdf"></i> Generar PDF
    </a>
  </div>
</div>


<form action="<?php echo BASE_URL; ?>stock/updateReplenishmentOrder/<?php echo $data['order_id']; ?>" method="POST">

  <div class="card shadow-sm mb-4">
    <div class="card-body text-end" id="action-buttons">
      <?php if (isset($data['order_details']) && $data['order_details']->num_rows > 0): ?>
        <button type="button" id="enable-edit-btn" class="btn btn-warning">
          ✏️ Habilitar Edición
        </button>
        <button type="submit" id="save-changes-btn" class="btn btn-success d-none">
          💾 Guardar Cambios
        </button>
        <button type="button" id="cancel-edit-btn" class="btn btn-danger d-none">
          Cancelar
        </button>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Modelo</th>
              <th>Color</th>
              <th>Tipo</th>
              <th class="text-center" style="width: 150px;">Cant. Solicitada</th>
              <th class="text-center" style="width: 150px;">Cant. Recibida</th>
              <th class="text-center" style="width: 150px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($data['order_details']) && $data['order_details']->num_rows > 0): ?>
              <?php while ($item = $data['order_details']->fetch_assoc()): ?>
                <tr>
                  <td class="modelo-resaltado"><?php echo htmlspecialchars($item['model_name']); ?></td>
                  <td>
                    <?php
                    $color_class = 'bg-dark'; // Clase por defecto
                    if ($item['color_display_name'] == 'Negro') {
                      $color_class = 'badge-negro';
                    } elseif ($item['color_display_name'] == 'Color') {
                      $color_class = 'badge-gradient-color';
                    }
                    ?>
                    <span class="badge <?php echo $color_class; ?>">
                      <?php echo htmlspecialchars($item['color_display_name']); ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge <?php echo $item['type'] === 'Tinta' ? 'bg-primary' : 'bg-secondary'; ?>">
                      <?php echo htmlspecialchars($item['type']); ?>
                    </span>
                  </td>
                  <td>
                    <input type="number"
                      class="form-control text-center quantity-input"
                      name="quantities[<?php echo $item['replenishment_item_id']; ?>]"
                      value="<?php echo htmlspecialchars($item['quantity_requested']); ?>"
                      min="0"
                      readonly
                      style="background-color: #e9ecef; border: none;">
                  </td>
                  <td class="text-center">
                    <?php 
                      $received = $item['quantity_received'];
                      $requested = $item['quantity_requested'];
                      $status_class = 'text-danger';
                      if ($received >= $requested) $status_class = 'text-success';
                      elseif ($received > 0) $status_class = 'text-warning';
                    ?>
                    <strong class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($received); ?></strong>
                  </td>
                  <td class="text-center">
                    <a href="<?php echo BASE_URL; ?>stock/replenish/<?php echo $item['replenishment_item_id']; ?>" class="btn btn-sm btn-info">
                      Registrar Ingreso
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">
                  <em>No se encontraron artículos en este pedido.</em>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</form>

<?php include 'views/templates/footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const enableEditBtn = document.getElementById('enable-edit-btn');
    const saveChangesBtn = document.getElementById('save-changes-btn');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const quantityInputs = document.querySelectorAll('.quantity-input');

    if (enableEditBtn) {
      enableEditBtn.addEventListener('click', function() {
        quantityInputs.forEach(input => {
          input.removeAttribute('readonly');
          input.classList.add('bg-white');
        });
        enableEditBtn.classList.add('d-none');
        saveChangesBtn.classList.remove('d-none');
        cancelEditBtn.classList.remove('d-none');
      });

      cancelEditBtn.addEventListener('click', function() {
        // Recargamos la página para descartar cualquier cambio no guardado
        location.reload();
      });
    }
  });
</script>
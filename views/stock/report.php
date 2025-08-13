<?php $title = 'Stock Actual'; include 'views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h2 class="mb-0 me-3">📦 Stock Actual</h2>
    <div class="d-flex flex-wrap">
        <div class="card text-center me-2 mb-2">
            <div class="card-header"><strong>Total General</strong></div>
            <div class="card-body py-2"><h3 class="card-title mb-0"><?php echo $total_item_count; ?></h3></div>
        </div>
        <?php if (!empty(array_filter($filters))): ?>
            <div class="card text-center bg-light mb-2">
                <div class="card-header"><strong>Total en Vista Filtrada</strong></div>
                <div class="card-body py-2"><h3 class="card-title mb-0"><?php echo $filtered_item_count; ?></h3></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><strong>🔍 Filtrar Inventario</strong></div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>stock/report" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3"><label for="keyword" class="form-label">Marca o Modelo</label><input type="text" class="form-control" id="keyword" name="keyword" value="<?php echo htmlspecialchars($filters['keyword'] ?? ''); ?>"></div>
            <div class="col-md-2"><label for="type" class="form-label">Tipo</label><select id="type" name="type" class="form-select"><option value="">Todos</option><option value="Tinta" <?php if (($filters['type'] ?? '') == 'Tinta') echo 'selected'; ?>>Tinta</option><option value="Tóner" <?php if (($filters['type'] ?? '') == 'Tóner') echo 'selected'; ?>>Tóner</option></select></div>
            <div class="col-md-2"><label for="supplier_id" class="form-label">Proveedor</label><select id="supplier_id" name="supplier_id" class="form-select"><option value="">Todos</option><?php while($s = $suppliers->fetch_assoc()): ?><option value="<?php echo $s['id']; ?>" <?php if (($filters['supplier_id'] ?? '') == $s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['name']); ?></option><?php endwhile; ?></select></div>
            <div class="col-md-2"><label for="department_id" class="form-label">Departamento</label><select id="department_id" name="department_id" class="form-select"><option value="">Todos</option><?php while($d = $departments->fetch_assoc()): ?><option value="<?php echo $d['id']; ?>" <?php if (($filters['department_id'] ?? '') == $d['id']) echo 'selected'; ?>><?php echo htmlspecialchars($d['name']); ?></option><?php endwhile; ?></select></div>
            <div class="col-md-3 align-self-center">
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="include_zero_stock" value="1" id="include_zero_stock" <?php if (!empty($filters['include_zero_stock'])) echo 'checked'; ?>>
                    <label class="form-check-label" for="include_zero_stock">Incluir stock en cero</label>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary w-100 me-2">Filtrar</button>
                <a href="<?php echo BASE_URL; ?>stock/report" class="btn btn-secondary w-100 me-2">Limpiar</a>
                <a href="#" id="generate-stock-pdf-btn" class="btn btn-danger w-100" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr><th>Marca</th><th>Modelo</th><th>Color</th><th>Proveedor</th><th>Tipo</th><th>Cantidad Actual</th><th>Impresora Compatible</th><th>Departamento(s)</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (isset($stock) && $stock->num_rows > 0): while ($row = $stock->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['brand_name']); ?></td>
            <td><?php echo htmlspecialchars($row['display_model_name']); ?></td>
            <td>
              <?php 
                $color_class = 'bg-dark';
                if ($row['color_display_name'] == 'Negro') { $color_class = 'badge-negro'; } 
                elseif ($row['color_display_name'] == 'Color') { $color_class = 'badge-gradient-color'; }
              ?>
              <?php if (!empty($row['color_display_name']) && $row['color_display_name'] !== 'Sin especificar'): ?>
                <span class="badge <?php echo $color_class; ?>"><?php echo htmlspecialchars($row['color_display_name']); ?></span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
            <td><span class="badge <?php echo $row['type'] === 'Tinta' ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo htmlspecialchars($row['type']); ?></span></td>
            <td><strong class="<?php echo ($row['calculated_quantity'] ?? 0) <= 5 ? 'text-danger' : 'text-success'; ?>"><?php echo htmlspecialchars($row['calculated_quantity'] ?? 0); ?></strong></td>
            <td><?php echo htmlspecialchars(($row['printer_brand_name'] ?? '') . ' ' . ($row['printer_model_name'] ?? 'N/A')); ?></td>
            <td><?php echo htmlspecialchars($row['departments'] ?? 'N/A'); ?></td>
            <td>
              <div class="btn-group" role="group">
                <a href="<?php echo BASE_URL; ?>stock/edit/<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Editar">✏️</a>
                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#deactivateModal" data-id="<?php echo $row['id']; ?>" title="Dar de Baja">📉</button>
              </div>
            </td>
          </tr>
      <?php endwhile; else: ?>
          <tr><td colspan="9" class="text-center text-muted"><em>No se encontraron productos con los filtros seleccionados.</em></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (isset($total_pages) && $total_pages > 1): ?>
<nav aria-label="Paginación del stock">
  <ul class="pagination justify-content-center">
    <?php $query_params = http_build_query(array_filter($filters)); ?>
    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>"><a class="page-link" href="<?php echo BASE_URL; ?>stock/report?page=<?php echo $page - 1; ?>&<?php echo $query_params; ?>" aria-label="Anterior"<?php echo ($page <= 1) ? ' tabindex="-1" aria-disabled="true"' : ''; ?>>&laquo; Anterior</a></li>
    <?php
    $start_page = max(1, $page - 2);
    $end_page = min($total_pages, $page + 2);
    if ($start_page > 1): ?>
      <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>stock/report?page=1&<?php echo $query_params; ?>">1</a></li>
      <?php if ($start_page > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
    <?php endif; ?>
    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
      <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>"><a class="page-link" href="<?php echo BASE_URL; ?>stock/report?page=<?php echo $i; ?>&<?php echo $query_params; ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <?php if ($end_page < $total_pages): ?>
      <?php if ($end_page < $total_pages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
      <li class="page-item"><a class="page-link" href="<?php echo BASE_URL; ?>stock/report?page=<?php echo $total_pages; ?>&<?php echo $query_params; ?>"><?php echo $total_pages; ?></a></li>
    <?php endif; ?>
    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>"><a class="page-link" href="<?php echo BASE_URL; ?>stock/report?page=<?php echo $page + 1; ?>&<?php echo $query_params; ?>" aria-label="Siguiente"<?php echo ($page >= $total_pages) ? ' tabindex="-1" aria-disabled="true"' : ''; ?>>Siguiente &raquo;</a></li>
  </ul>
</nav>
<?php endif; ?>

<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="deactivateModalLabel">Confirmar Baja de Producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
      <form action="<?php echo BASE_URL; ?>stock/deactivate" method="POST">
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input type="hidden" name="stock_id" id="deactivate_stock_id">
          <div class="mb-3"><label for="reason" class="form-label"><strong>Motivo de la baja:</strong></label><textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Ej: Producto descontinuado, obsoleto, defectuoso, etc."></textarea></div>
          <div class="alert alert-warning" role="alert"><small><strong>Nota:</strong> El producto no se eliminará permanentemente, pero se ocultará de las listas y no se podrá utilizar en nuevas operaciones. Esta acción se registrará en el historial.</small></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning"><i class="fas fa-exclamation-triangle"></i> Confirmar Baja</button></div>
      </form>
    </div>
  </div>
</div>

<?php include 'views/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var deactivateModal = document.getElementById('deactivateModal');
    if(deactivateModal) {
        deactivateModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var stockId = button.getAttribute('data-id');
            var modalInput = deactivateModal.querySelector('#deactivate_stock_id');
            modalInput.value = stockId;
        });
        deactivateModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('reason').value = '';
            document.getElementById('deactivate_stock_id').value = '';
        });
    }

    const keywordInput = document.getElementById('keyword');
    const typeInput = document.getElementById('type');
    const supplierIdInput = document.getElementById('supplier_id');
    const departmentIdInput = document.getElementById('department_id');
    const pdfBtn = document.getElementById('generate-stock-pdf-btn');

    function updateStockPdfLink() {
        const keyword = keywordInput.value;
        const type = typeInput.value;
        const supplierId = supplierIdInput.value;
        const departmentId = departmentIdInput.value;
        
        const url = `<?php echo BASE_URL; ?>stock/generateStockPdf?keyword=${encodeURIComponent(keyword)}&type=${type}&supplier_id=${supplierId}&department_id=${departmentId}`;
        pdfBtn.href = url;
    }

    keywordInput.addEventListener('keyup', updateStockPdfLink);
    typeInput.addEventListener('change', updateStockPdfLink);
    supplierIdInput.addEventListener('change', updateStockPdfLink);
    departmentIdInput.addEventListener('change', updateStockPdfLink);

    updateStockPdfLink();
});
</script>
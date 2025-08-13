<?php $title = 'Historial de Movimientos'; include 'views/templates/header.php'; ?>
<h2 class="mb-4">📜 Historial de Movimientos de Inventario (Auditoría)</h2>
<p class="text-muted">Aquí se registra cada entrada y salida de stock. Las salidas o creaciones erróneas pueden ser revertidas para corregir el inventario.</p>
<p class="text-muted">Utiliza los filtros para auditar cada entrada y salida de stock. Las salidas o creaciones erróneas pueden ser revertidas para corregir el inventario.</p>

<!-- Enlaces rápidos -->
<div class="mb-3">
    <a href="<?php echo BASE_URL; ?>stock/stockOut" class="btn btn-warning btn-sm">📤 Ver Solo Salidas</a>
    <a href="<?php echo BASE_URL; ?>history/movements" class="btn btn-info btn-sm">📜 Ver Historial Completo</a>
    <a href="<?php echo BASE_URL; ?>stock/issue" class="btn btn-primary btn-sm">➕ Registrar Nueva Salida</a>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header"><strong>🔍 Filtros de Auditoría</strong></div>
    <div class="card-body">
        <form id="history-filters-form" class="row g-3 align-items-end">
            <div class="col-md-3"><label for="start_date" class="form-label">Fecha Desde</label><input type="date" id="start_date" name="start_date" class="form-control filter-input" value="<?php echo htmlspecialchars($data['filters']['start_date'] ?? ''); ?>"></div>
            <div class="col-md-3"><label for="end_date" class="form-label">Fecha Hasta</label><input type="date" id="end_date" name="end_date" class="form-control filter-input" value="<?php echo htmlspecialchars($data['filters']['end_date'] ?? ''); ?>"></div>
            <div class="col-md-3"><label for="keyword" class="form-label">Artículo (Marca/Modelo)</label><input type="text" id="keyword" name="keyword" class="form-control filter-input" placeholder="Ej: HP 85A" value="<?php echo htmlspecialchars($data['filters']['keyword'] ?? ''); ?>"></div>
            <div class="col-md-3"><label for="movement_type" class="form-label">Tipo Movimiento</label><select id="movement_type" name="movement_type" class="form-select filter-input">
                    <option value="">Todos</option>
                    <option value="INITIAL_STOCK" <?php if (($data['filters']['movement_type'] ?? '') == 'INITIAL_STOCK') echo 'selected'; ?>>Stock Inicial</option>
                    <option value="REPLENISHMENT" <?php if (($data['filters']['movement_type'] ?? '') == 'REPLENISHMENT') echo 'selected'; ?>>Reposición</option>
                    <option value="SALE" <?php if (($data['filters']['movement_type'] ?? '') == 'SALE') echo 'selected'; ?>>Salida / Entrega</option>
                    <option value="RETURN_DEFECTIVE" <?php if (($data['filters']['movement_type'] ?? '') == 'RETURN_DEFECTIVE') echo 'selected'; ?>>Devolución por Falla</option>
                    <option value="SALE_REVERSAL" <?php if (($data['filters']['movement_type'] ?? '') == 'SALE_REVERSAL') echo 'selected'; ?>>Reversión de Salida</option>
                    <option value="DEACTIVATED" <?php if (($data['filters']['movement_type'] ?? '') == 'DEACTIVATED') echo 'selected'; ?>>Dado de Baja</option>
                </select></div>
            <div class="col-md-3"><label for="recipient_id" class="form-label">Entregado a</label><select id="recipient_id" name="recipient_id" class="form-select filter-input">
                    <option value="">Todos</option>
                    <?php $data['recipients']->data_seek(0); while($r = $data['recipients']->fetch_assoc()): ?>
                        <option value="<?php echo $r['id']; ?>" <?php if (($data['filters']['recipient_id'] ?? '') == $r['id']) echo 'selected'; ?>><?php echo htmlspecialchars($r['name']); ?></option>
                    <?php endwhile; ?>
                </select></div>
            <div class="col-md-3"><label for="department_id" class="form-label">Departamento</label><select id="department_id" name="department_id" class="form-select filter-input">
                    <option value="">Todos</option>
                    <?php $data['departments']->data_seek(0); while($d = $data['departments']->fetch_assoc()): ?>
                        <option value="<?php echo $d['id']; ?>" <?php if (($data['filters']['department_id'] ?? '') == $d['id']) echo 'selected'; ?>><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endwhile; ?>
                </select></div>
            <div class="col-md-3"><label for="supplier_id" class="form-label">Proveedor</label><select id="supplier_id" name="supplier_id" class="form-select filter-input">
                    <option value="">Todos</option>
                    <?php $data['suppliers']->data_seek(0); while($s = $data['suppliers']->fetch_assoc()): ?>
                        <option value="<?php echo $s['id']; ?>" <?php if (($data['filters']['supplier_id'] ?? '') == $s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['name']); ?></option>
                    <?php endwhile; ?>
                </select></div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" id="clear-filters-btn" class="btn btn-secondary w-100 me-2">Limpiar Filtros</button>
                <a href="#" id="generate-pdf-btn" class="btn btn-danger w-100" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h5 class="card-title">Resumen de Cambios (Filtrado)</h5>
                <p class="card-text fs-4 fw-bold" id="total-change-summary">
                    <?php echo $data['total_change'] ?? 0; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div id="movements-table-container">
    <?php include '_movements_table.php'; ?>
</div>

<!-- Modal para Revertir Movimiento -->
<div class="modal fade" id="reversalModal" tabindex="-1" aria-labelledby="reversalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reversalModalLabel">Revertir Movimiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo BASE_URL; ?>history/reverse" method="POST">
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input type="hidden" name="movement_id" id="movement_id_input">
          <div class="mb-3">
            <label for="reason" class="form-label"><strong>Motivo de la reversión:</strong></label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Ej: Registro duplicado, cantidad incorrecta, etc."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">Confirmar Reversión</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'views/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('history-filters-form');
    const tableContainer = document.getElementById('movements-table-container');
    const totalChangeSummary = document.getElementById('total-change-summary');
    const pdfBtn = document.getElementById('generate-pdf-btn');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');
    let debounceTimer;

    function updatePdfLink() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        pdfBtn.href = `<?php echo BASE_URL; ?>history/generatePdf?${params.toString()}`;
    }

    function fetchMovements(page = 1) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.append('page', page);
        
        tableContainer.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        fetch(`<?php echo BASE_URL; ?>history/ajaxFilter?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                tableContainer.innerHTML = data.html;
                totalChangeSummary.textContent = data.total_change;
            })
            .catch(error => {
                tableContainer.innerHTML = '<div class="alert alert-danger">Error al cargar los datos.</div>';
                console.error('Error:', error);
            });
    }

    form.addEventListener('input', function(e) {
        if (e.target.classList.contains('filter-input')) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchMovements(1);
                updatePdfLink();
            }, 500);
        }
    });

    clearFiltersBtn.addEventListener('click', function() {
        form.reset();
        fetchMovements(1);
        updatePdfLink();
    });

    document.body.addEventListener('click', function(e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            const page = url.searchParams.get('page');
            fetchMovements(page);
        }
    });

    const reversalModal = document.getElementById('reversalModal');
    if (reversalModal) {
        reversalModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const movementId = button.getAttribute('data-movement-id');
            const modalInput = reversalModal.querySelector('#movement_id_input');
            modalInput.value = movementId;
        });
    }

    updatePdfLink();
});
</script>

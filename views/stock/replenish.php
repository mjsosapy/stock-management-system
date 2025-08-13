<?php $title = 'Reponer Stock'; include 'views/templates/header.php'; ?>

<style>
  #search-results {
    position: absolute;
    width: calc(100% - 2rem);
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ccc;
    border-top: none;
    z-index: 1000;
    background-color: white;
  }
  .search-result-item {
    padding: 10px;
    cursor: pointer;
    background-color: #FFDAB9;
    border-bottom: 1px solid #ddd;
  }
  .search-result-item:hover {
    background-color: #FFA07A;
  }
</style>

<h2 class="mb-4">🔄 Reponer Stock</h2>
<p class="text-muted">Utilice este formulario para registrar la entrada de nueva mercancía. Se actualizará el proveedor del producto con el que ingrese aquí.</p>

<form id="replenish-form" method="POST" action="<?php echo BASE_URL; ?>stock/doReplenish" class="card p-4 shadow-sm">
  
  <?php 
    $prefill_item_name = '';
    if (isset($data['prefill'])) {
        $prefill_item_name = htmlspecialchars($data['prefill']['brand_name'] . ' ' . $data['prefill']['model_name'] . ' - ' . $data['prefill']['color']);
    }
  ?>

  <?php if (isset($data['prefill']['id'])): ?>
    <div class="alert alert-info">
        Estás registrando el ingreso para el artículo del pedido <strong>#<?php echo htmlspecialchars($data['prefill']['order_id']); ?></strong>.
    </div>
    <input type="hidden" name="replenishment_order_item_id" value="<?php echo htmlspecialchars($data['prefill']['id']); ?>">
  <?php endif; ?>
  
  <div class="mb-3 position-relative">
    <label for="product-search" class="form-label">Buscar Artículo a Reponer</label>
    <input type="text" id="product-search" class="form-control" placeholder="Escribe la marca o modelo..." autocomplete="off" value="<?php echo $prefill_item_name; ?>" required>
    <input type="hidden" name="stock_id" id="stock_id_input" value="<?php echo htmlspecialchars($data['prefill']['stock_id'] ?? ''); ?>" required>
    <div id="search-results"></div>
    <div class="form-text">Si el producto recibido no está en la lista, puedes <a href="<?php echo BASE_URL; ?>stock/add" target="_blank">agregarlo al inventario aquí</a>.</div>
  </div>
  
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Cantidad a Añadir</label>
      <input type="number" name="quantity_added" class="form-control" required min="1">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Proveedor</label>
      <div class="input-group">
        <select name="supplier_id" id="supplier_select" class="form-select" required>
          <option value="">-- Selecciona --</option>
          <?php while($supplier = $data['suppliers']->fetch_assoc()): ?>
            <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option>
          <?php endwhile; ?>
        </select>
        <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Supplier')">+</button>
      </div>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Motivo o Referencia (Ej: Factura #123)</label>
    <input type="text" name="reason" class="form-control" maxlength="255">
  </div>
  <button type="submit" class="btn btn-info text-white">Confirmar Reposición</button>
</form>

<div class="modal fade" id="categoryModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="categoryModalLabel"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="categoryForm"><div class="mb-3"><label for="categoryName" class="form-label">Nombre</label><input type="text" class="form-control" id="categoryName" required></div><div id="category-error" class="text-danger"></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="button" class="btn btn-primary" onclick="saveCategory()">Guardar</button></div></div></div></div>

<?php include 'views/templates/footer.php'; ?>

<script>
let currentCategoryType = '';
let categoryModal;

document.addEventListener('DOMContentLoaded', function() {
    categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const searchInput = document.getElementById('product-search');
    const resultsContainer = document.getElementById('search-results');
    const stockIdInput = document.getElementById('stock_id_input');
    const form = document.getElementById('replenish-form');

    searchInput.addEventListener('keyup', function() {
        const term = searchInput.value;
        resultsContainer.innerHTML = '';
        stockIdInput.value = '';

        if (term.length < 1) { // Puede buscar desde el primer caracter
            return;
        }

        fetch(`<?php echo BASE_URL; ?>stock/search?term=${term}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(item => {
                        const itemDiv = document.createElement('div');
                        
                        // CORRECCIÓN: Construir el nombre completo incluyendo el color
                        let displayName = `${item.brand} ${item.model_name}`;
                        if (item.color) {
                            displayName += ` - ${item.color}`;
                        }
                        
                        itemDiv.textContent = displayName;
                        itemDiv.className = 'search-result-item';
                        itemDiv.setAttribute('data-id', item.id);
                        itemDiv.setAttribute('data-name', displayName); // Usar el nombre completo
                        
                        resultsContainer.appendChild(itemDiv);
                    });
                } else {
                    resultsContainer.innerHTML = '<div class="p-2 text-muted">No se encontraron resultados.</div>';
                }
            });
    });

    resultsContainer.addEventListener('click', function(event) {
        if (event.target && event.target.matches('div.search-result-item')) {
            const selectedId = event.target.getAttribute('data-id');
            const selectedName = event.target.getAttribute('data-name');
            
            searchInput.value = selectedName;
            stockIdInput.value = selectedId;
            resultsContainer.innerHTML = '';
        }
    });

    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target)) {
            resultsContainer.innerHTML = '';
        }
    });

    form.addEventListener('submit', function(event) {
        if (!stockIdInput.value) {
            alert('Por favor, busca y selecciona un artículo de la lista.');
            event.preventDefault();
        }
    });
});

function openCategoryModal(type) {
    currentCategoryType = type;
    const titles = { Supplier: 'Proveedor' };
    document.getElementById('categoryModalLabel').textContent = `Agregar Nuevo ${titles[type]}`;
    document.getElementById('categoryForm').reset();
    document.getElementById('category-error').textContent = '';
    categoryModal.show();
}

function saveCategory() {
    const name = document.getElementById('categoryName').value;
    if (!name.trim()) {
        document.getElementById('category-error').textContent = 'El nombre es obligatorio.';
        return;
    }
    const url = `<?php echo BASE_URL; ?>stock/add${currentCategoryType}`;
    const formData = new FormData();
    formData.append('name', name);

    fetch(url, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const selectIdMap = { Supplier: 'supplier_select' };
            const selectElement = document.getElementById(selectIdMap[currentCategoryType]);
            const newOption = new Option(result.data.name, result.data.id, true, true);
            selectElement.add(newOption);
            categoryModal.hide();
            showToast(`Se agregó "${result.data.name}" correctamente.`, 'success');
        } else {
            document.getElementById('category-error').textContent = result.message;
        }
    })
    .catch(error => {
        document.getElementById('category-error').textContent = 'Ocurrió un error de red.';
    });
}
</script>
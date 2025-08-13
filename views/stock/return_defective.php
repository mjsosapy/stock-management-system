<?php $title = 'Devolución a Proveedor'; include 'views/templates/header.php'; ?>

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
  .search-result-item { padding: 10px; cursor: pointer; }
  .search-result-item:hover { background-color: #f0f0f0; }
</style>

<h2 class="mb-4">↪️ Devolución a Proveedor por Fallas</h2>
<p class="text-muted">Utiliza este formulario para registrar la devolución de artículos defectuosos al proveedor. El stock se descontará del inventario.</p>
<form id="return-form" method="POST" action="<?php echo BASE_URL; ?>stock/doReturnDefective" class="card p-4 shadow-sm">
  
  <div class="mb-3 position-relative">
    <label for="product-search" class="form-label">Buscar Artículo a Devolver</label>
    <input type="text" id="product-search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off" required>
    <input type="hidden" name="stock_id" id="stock_id_input" required>
    <div id="search-results"></div>
  </div>

  <div class="mb-3">
    <label class="form-label">Cantidad a Devolver</label>
    <input type="number" name="quantity_removed" class="form-control" required min="1">
  </div>
  <div class="mb-3">
    <label class="form-label">Motivo de la Devolución (Ej: "Cartucho no reconocido", "Derrame de tinta")</label>
    <input type="text" name="reason" class="form-control" required maxlength="255">
  </div>
  <button type="submit" class="btn btn-danger">Confirmar Devolución</button>
</form>

<?php include 'views/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('product-search');
    const resultsContainer = document.getElementById('search-results');
    const stockIdInput = document.getElementById('stock_id_input');
    const form = document.getElementById('return-form');

    searchInput.addEventListener('keyup', function() {
        const term = searchInput.value;
        resultsContainer.innerHTML = '';
        stockIdInput.value = '';

        if (term.length < 1) { return; }

        fetch(`<?php echo BASE_URL; ?>stock/search?term=${term}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(item => {
                        if (item.calculated_quantity > 0) {
                            const itemDiv = document.createElement('div');
                            let displayName = `${item.brand} ${item.model_name}`;
                            if(item.color) { displayName += ` - ${item.color}`; }
                            displayName += ` (Disponibles: ${item.calculated_quantity})`;
                            
                            itemDiv.textContent = displayName;
                            itemDiv.className = 'search-result-item';
                            itemDiv.setAttribute('data-id', item.id);
                            itemDiv.setAttribute('data-name', `${item.brand} ${item.model_name}${item.color ? ' - ' + item.color : ''}`);
                            resultsContainer.appendChild(itemDiv);
                        }
                    });
                } else {
                    resultsContainer.innerHTML = '<div class="p-2 text-muted">No se encontraron resultados con stock.</div>';
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
</script>
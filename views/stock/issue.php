<?php 
$title = 'Registrar Salida'; 
include 'views/templates/header.php'; 

// Recuperar y limpiar el estado del formulario de la sesión
$form_state = $_SESSION['issue_form_state'] ?? null;
if ($form_state) {
    unset($_SESSION['issue_form_state']);
}
?>

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
  }
  .search-result-item:hover {
    background-color: #f0f0f0;
  }
</style>

<h2 class="mb-4">📤 Registrar Salida de Artículo</h2>
<form id="issue-form" method="POST" action="<?php echo BASE_URL; ?>stock/issue" class="card p-4 shadow-sm">
  
  <div class="mb-3 position-relative">
    <label for="product-search" class="form-label">Buscar Artículo</label>
    <input type="text" id="product-search" class="form-control" name="item_name_search" placeholder="Escribe al menos 1 caracter..." autocomplete="off" value="<?php echo htmlspecialchars($form_state['data']['item_name_search'] ?? ''); ?>" required>
    <input type="hidden" name="item_id" id="item_id_input" value="<?php echo htmlspecialchars($form_state['data']['item_id'] ?? ''); ?>" required>
    <div id="search-results"></div>
  </div>

  <div class="mb-3">
      <label class="form-label">Cantidad a Egresar</label>
      <input type="number" name="quantity" class="form-control <?php echo isset($form_state['error']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($form_state['data']['quantity'] ?? ''); ?>" required min="1">
      <?php if (isset($form_state['error'])): ?>
          <div class="invalid-feedback">
              <?php echo $form_state['error']; ?>
          </div>
      <?php endif; ?>
  </div>
  
  <div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Entregado a (Nombre y Apellido)</label>
        <div class="input-group">
            <select name="recipient_id" id="recipient_select" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php $data['recipients']->data_seek(0); while($recipient = $data['recipients']->fetch_assoc()): ?>
                    <option value="<?php echo $recipient['id']; ?>" <?php if (($form_state['data']['recipient_id'] ?? '') == $recipient['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($recipient['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Recipient')">+</button>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Departamento</label>
        <div class="input-group">
            <select name="department_id" id="department_select" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php $data['departments']->data_seek(0); while($department = $data['departments']->fetch_assoc()): ?>
                    <option value="<?php echo $department['id']; ?>" <?php if (($form_state['data']['department_id'] ?? '') == $department['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($department['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Department')">+</button>
        </div>
    </div>
  </div>

  <button type="submit" class="btn btn-warning">Registrar Salida</button>
  <a href="<?php echo BASE_URL; ?>stock/dashboard" class="btn btn-secondary mt-2">Cancelar</a>
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
    const itemIdInput = document.getElementById('item_id_input');
    const form = document.getElementById('issue-form');

    searchInput.addEventListener('keyup', function() {
        const term = searchInput.value;
        resultsContainer.innerHTML = '';
        itemIdInput.value = '';

        if (term.length < 1) { return; }

        fetch(`<?php echo BASE_URL; ?>stock/search?term=${term}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(item => {
                        if (item.calculated_quantity > 0) {
                            const itemDiv = document.createElement('div');
                            let displayName = `${item.brand} ${item.model_name}`;
                            if (item.color) { displayName += ` - ${item.color}`; }
                            displayName += ` (Disponibles: ${item.calculated_quantity})`;
                            
                            itemDiv.textContent = displayName;
                            itemDiv.className = 'search-result-item';
                            itemDiv.setAttribute('data-id', item.id);
                            
                            let dataName = `${item.brand} ${item.model_name}`;
                            if (item.color) { dataName += ` - ${item.color}`; }
                            itemDiv.setAttribute('data-name', dataName);
                            
                            resultsContainer.appendChild(itemDiv);
                        }
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
            itemIdInput.value = selectedId;
            resultsContainer.innerHTML = '';
        }
    });

    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target)) {
            resultsContainer.innerHTML = '';
        }
    });

    form.addEventListener('submit', function(event) {
        if (!itemIdInput.value) {
            alert('Por favor, busca y selecciona un artículo de la lista.');
            event.preventDefault();
        }
    });
});

function openCategoryModal(type) {
    currentCategoryType = type;
    const titles = { Recipient: 'Destinatario', Department: 'Departamento' };
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
            const selectIdMap = { Recipient: 'recipient_select', Department: 'department_select' };
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
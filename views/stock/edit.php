<?php $title = 'Editar Producto'; include 'views/templates/header.php'; ?>
<h2 class="mb-4">✏️ Editar Detalles del Producto</h2>
<p class="text-muted">Nota: La cantidad de stock no se edita aquí. Utilice las opciones "Reponer Stock" o "Registrar Falla" para ajustar las cantidades.</p>
<form method="POST" action="<?php echo BASE_URL; ?>stock/edit/<?php echo $data['item']['id']; ?>" class="card p-4 shadow-sm">
  <input type="hidden" name="id" value="<?php echo $data['item']['id']; ?>">
  
  <div class="row align-items-end">
      <div class="col-md-6 mb-3">
        <label class="form-label">Tipo</label>
        <select name="type" class="form-select" required>
            <option value="Tóner" <?php if($data['item']['type'] == 'Tóner') echo 'selected'; ?>>Tóner</option>
            <option value="Tinta" <?php if($data['item']['type'] == 'Tinta') echo 'selected'; ?>>Tinta</option>
            <option value="Cinta Matricial" <?php if($data['item']['type'] == 'Cinta Matricial') echo 'selected'; ?>>Cinta Matricial</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
          <label class="form-label">Costo Unitario</label>
          <input type="number" name="cost" class="form-control" value="<?php echo htmlspecialchars($data['item']['cost']); ?>" min="0" step="0.01" required>
      </div>
      </div>
  
  <h5 class="mt-3">Detalles del Consumible</h5>
  <div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Marca</label>
        <div class="input-group">
            <select name="brand_id" id="brand_select" class="form-select" required>
                <?php $data['brands']->data_seek(0); while($brand = $data['brands']->fetch_assoc()): ?>
                    <option value="<?php echo $brand['id']; ?>" <?php if($data['item']['brand_id'] == $brand['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Brand')">+</button>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Modelo</label>
        <div class="input-group">
            <select name="consumable_model_id" id="consumable_model_select" class="form-select" required>
                <?php $data['consumable_models']->data_seek(0); while($consumable_model = $data['consumable_models']->fetch_assoc()): ?>
                    <option value="<?php echo $consumable_model['id']; ?>" <?php if($data['item']['consumable_model_id'] == $consumable_model['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($consumable_model['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('ConsumableModel')">+</button>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Proveedor</label>
        <div class="input-group">
            <select name="supplier_id" id="supplier_select" class="form-select">
                <option value="">-- Opcional --</option>
                <?php $data['suppliers']->data_seek(0); while($supplier = $data['suppliers']->fetch_assoc()): ?>
                    <option value="<?php echo $supplier['id']; ?>" <?php if($data['item']['supplier_id'] == $supplier['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($supplier['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Supplier')">+</button>
        </div>
    </div>
  </div>
  
  <h5 class="mt-3">Compatibilidad y Departamentos</h5>
  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">Marca Impresora</label>
      <div class="input-group">
          <select name="printer_brand_id" id="printer_brand_select" class="form-select">
              <option value="">-- Selecciona --</option>
              <?php $data['printer_brands']->data_seek(0); while($p_brand = $data['printer_brands']->fetch_assoc()): ?>
                  <option value="<?php echo $p_brand['id']; ?>" <?php if($data['item']['printer_brand_id'] == $p_brand['id']) echo 'selected'; ?>>
                      <?php echo htmlspecialchars($p_brand['name']); ?>
                  </option>
              <?php endwhile; ?>
          </select>
          <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('PrinterBrand')">+</button>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Modelo Impresora</label>
      <div class="input-group">
          <select name="printer_model_id" id="printer_model_select" class="form-select">
              <option value="">-- Primero selecciona una marca --</option>
              <?php 
                foreach($data['printer_models_for_brand'] as $p_model) {
                    $selected = ($data['item']['printer_model_id'] == $p_model['id']) ? 'selected' : '';
                    echo "<option value=\"{$p_model['id']}\" {$selected}>" . htmlspecialchars($p_model['name']) . "</option>";
                }
              ?>
          </select>
          <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('PrinterModel')" id="addPrinterModelBtn">+</button>
      </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Departamento(s) de uso</label>
        <div class="input-group">
            <select name="department_ids[]" id="department_select" class="form-select" multiple>
                <?php $data['departments']->data_seek(0); while($department = $data['departments']->fetch_assoc()): ?>
                    <option value="<?php echo $department['id']; ?>" <?php if(in_array($department['id'], $data['item_departments'])) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($department['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Department')">+</button>
        </div>
    </div>
  </div>
  
  <div class="mt-3">
      <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
      <a href="<?php echo BASE_URL; ?>stock/report" class="btn btn-secondary">Cancelar</a>
  </div>
</form>

<div class="modal fade" id="categoryModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="categoryModalLabel"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="categoryForm"><div class="mb-3"><label for="categoryName" class="form-label">Nombre</label><input type="text" class="form-control" id="categoryName" required></div><div id="category-error" class="text-danger"></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="button" class="btn btn-primary" onclick="saveCategory()">Guardar</button></div></div></div></div>

<?php include 'views/templates/footer.php'; ?>

<script>
let currentCategoryType = '';
let categoryModal;

document.addEventListener('DOMContentLoaded', function() {
    categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    
    const printerBrandSelect = document.getElementById('printer_brand_select');
    const printerModelSelect = document.getElementById('printer_model_select');
    const addPrinterModelBtn = document.getElementById('addPrinterModelBtn');

    if (printerBrandSelect.value) {
        printerModelSelect.disabled = false;
        addPrinterModelBtn.disabled = false;
    } else {
        printerModelSelect.disabled = true;
        addPrinterModelBtn.disabled = true;
    }

    printerBrandSelect.addEventListener('change', function() {
        const brandId = this.value;
        printerModelSelect.innerHTML = '<option value="">Cargando...</option>';
        if (brandId) {
            printerModelSelect.disabled = false;
            addPrinterModelBtn.disabled = false;
            fetch(`<?php echo BASE_URL; ?>stock/getPrinterModelsByBrand/${brandId}`)
                .then(response => response.json())
                .then(models => {
                    printerModelSelect.innerHTML = '<option value="">-- Selecciona un modelo --</option>';
                    models.forEach(model => printerModelSelect.add(new Option(model.name, model.id)));
                });
        } else {
            printerModelSelect.innerHTML = '<option value="">-- Primero selecciona una marca --</option>';
            printerModelSelect.disabled = true;
            addPrinterModelBtn.disabled = true;
        }
    });
});

function openCategoryModal(type) {
    currentCategoryType = type;
    const titles = { Brand: 'Marca de Consumible', ConsumableModel: 'Modelo de Consumible', Supplier: 'Proveedor', PrinterBrand: 'Marca de Impresora', PrinterModel: 'Modelo de Impresora', Department: 'Departamento' };
    document.getElementById('categoryModalLabel').textContent = `Agregar Nueva ${titles[type]}`;
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
    if (currentCategoryType === 'PrinterModel') {
        const brandId = document.getElementById('printer_brand_select').value;
        if (!brandId) {
            document.getElementById('category-error').textContent = 'Debes seleccionar una marca de impresora primero.';
            return;
        }
        formData.append('brand_id', brandId);
    }
    fetch(url, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const selectIdMap = { Brand: 'brand_select', ConsumableModel: 'consumable_model_select', Supplier: 'supplier_select', PrinterBrand: 'printer_brand_select', PrinterModel: 'printer_model_select', Department: 'department_select' };
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
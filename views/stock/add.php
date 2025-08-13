<?php $title = 'Agregar Producto'; include 'views/templates/header.php'; ?>
<h2 class="mb-4">➕ Agregar Nuevo Producto al Inventario</h2>
<form method="POST" action="<?php echo BASE_URL; ?>stock/add" class="card p-4 shadow-sm">
  
  <h5 class="mt-3">Detalles del Consumible</h5>
  <div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Marca</label>
        <div class="input-group">
            <select name="brand_id" id="brand_select" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php while($brand = $data['brands']->fetch_assoc()): ?>
                    <option value="<?php echo $brand['id']; ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Brand')">+</button>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Modelo</label>
        <div class="input-group">
            <select name="consumable_model_id" id="consumable_model_select" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php while($consumable_model = $data['consumable_models']->fetch_assoc()): ?>
                    <option value="<?php echo $consumable_model['id']; ?>"><?php echo htmlspecialchars($consumable_model['name']); ?></option>
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
                <?php while($supplier = $data['suppliers']->fetch_assoc()): ?>
                    <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Supplier')">+</button>
        </div>
    </div>
  </div>

  <div class="row align-items-end">
    <div class="col-md-6 mb-3">
      <label class="form-label">Tipo de Producto</label>
      <select name="type" id="productType" class="form-select" required>
        <option value="" disabled selected>-- Selecciona --</option>
        <option value="Tinta">Tinta</option>
        <option value="Tóner">Tóner</option>
        <option value="Cinta Matricial">Cinta Matricial</option>
      </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Costo Unitario</label>
        <input type="number" name="cost" class="form-control" value="0.00" min="0" step="0.01" required>
    </div>
    </div>
  
  <div id="quantity-section" class="mt-3" style="display: none;">
    <h5 class="mb-3">Stock Inicial</h5>
    
    <div id="ink-options" style="display: none;">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Cantidad Negro</label>
          <input type="number" name="quantity[Negro]" class="form-control" value="0" min="0">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Cantidad Color</label>
          <input type="number" name="quantity[Color]" class="form-control" value="0" min="0">
        </div>
      </div>
    </div>

    <div id="toner-options" style="display: none;">
      <div class="mb-3">
        <label class="form-label">Tipo de Tóner</label>
        <div>
          <input type="radio" name="toner_type" value="mono" id="toner_mono" class="form-check-input">
          <label for="toner_mono" class="form-check-label me-3">Negro</label>
          <input type="radio" name="toner_type" value="color" id="toner_color" class="form-check-input">
          <label for="toner_color" class="form-check-label">Color (CMYK)</label>
        </div>
      </div>
      
      <div id="toner-mono-quantity" style="display: none;">
        <div class="col-md-6 mb-3">
          <label class="form-label">Cantidad</label>
          <input type="number" name="quantity[Negro]" class="form-control" value="0" min="0">
        </div>
      </div>
      
      <div id="toner-color-quantities" style="display: none;" class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Cyan</label>
          <input type="number" name="quantity[Cyan]" class="form-control" value="0" min="0">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Magenta</label>
          <input type="number" name="quantity[Magenta]" class="form-control" value="0" min="0">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Yellow</label>
          <input type="number" name="quantity[Yellow]" class="form-control" value="0" min="0">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Negro</label>
          <input type="number" name="quantity[Negro-Toner]" class="form-control" value="0" min="0">
        </div>
      </div>
    </div>

    <div id="ribbon-options" style="display: none;">
        <div class="col-md-6 mb-3">
          <label class="form-label">Cantidad</label>
          <input type="number" name="quantity[Negro]" class="form-control" value="0" min="0">
        </div>
    </div>

  </div>

  <h5 class="mt-3">Compatibilidad y Departamentos</h5>
  <div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Marca Impresora (Opcional)</label>
        <div class="input-group">
          <select name="printer_brand_id" id="printer_brand_select" class="form-select">
            <option value="">-- Selecciona --</option>
            <?php $data['printer_brands']->data_seek(0); while($p_brand = $data['printer_brands']->fetch_assoc()): ?>
              <option value="<?php echo $p_brand['id']; ?>"><?php echo htmlspecialchars($p_brand['name']); ?></option>
            <?php endwhile; ?>
          </select>
          <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('PrinterBrand')">+</button>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Modelo Impresora (Opcional)</label>
        <div class="input-group">
          <select name="printer_model_id" id="printer_model_select" class="form-select" disabled>
            <option value="">-- Primero selecciona una marca --</option>
          </select>
          <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('PrinterModel')" id="addPrinterModelBtn" disabled>+</button>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Departamento(s) de uso</label>
        <div class="input-group">
            <select name="department_ids[]" id="department_select" class="form-select" multiple>
                <?php while($department = $data['departments']->fetch_assoc()): ?>
                    <option value="<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-outline-success" type="button" onclick="openCategoryModal('Department')">+</button>
        </div>
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-success">Agregar Producto(s)</button>
    <a href="<?php echo BASE_URL; ?>stock/report" class="btn btn-secondary">Cancelar</a>
  </div>
</form>

<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="categoryModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="categoryForm">
          <div class="mb-3">
            <label for="categoryName" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="categoryName" required>
          </div>
          <div id="category-error" class="text-danger"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="saveCategory()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<?php include 'views/templates/footer.php'; ?>

<script>
let currentCategoryType = '';
let categoryModal;

document.addEventListener('DOMContentLoaded', function() {
    categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    
    const productType = document.getElementById('productType');
    const quantitySection = document.getElementById('quantity-section');
    const inkOptions = document.getElementById('ink-options');
    const tonerOptions = document.getElementById('toner-options');
    const tonerMonoRadio = document.getElementById('toner_mono');
    const tonerColorRadio = document.getElementById('toner_color');
    const tonerMonoQty = document.getElementById('toner-mono-quantity');
    const tonerColorQty = document.getElementById('toner-color-quantities');
    const ribbonOptions = document.getElementById('ribbon-options');

    // Manejar cambio de tipo de producto
    productType.addEventListener('change', function() {
        quantitySection.style.display = 'block';
        inkOptions.style.display = 'none';
        tonerOptions.style.display = 'none';
        tonerMonoQty.style.display = 'none';
        tonerColorQty.style.display = 'none';
        ribbonOptions.style.display = 'none';
        
        tonerMonoRadio.checked = false;
        tonerColorRadio.checked = false;

        if (this.value === 'Tinta') {
            inkOptions.style.display = 'block';
        } else if (this.value === 'Tóner') {
            tonerOptions.style.display = 'block';
        } else if (this.value === 'Cinta Matricial') {
            ribbonOptions.style.display = 'block';
        }
    });

    // Manejar cambio en tipo de tóner
    tonerMonoRadio.addEventListener('change', function() {
        if (this.checked) {
            tonerMonoQty.style.display = 'block';
            tonerColorQty.style.display = 'none';
        }
    });

    tonerColorRadio.addEventListener('change', function() {
        if (this.checked) {
            tonerMonoQty.style.display = 'none';
            tonerColorQty.style.display = 'block';
        }
    });

    const printerBrandSelect = document.getElementById('printer_brand_select');
    const printerModelSelect = document.getElementById('printer_model_select');
    const addPrinterModelBtn = document.getElementById('addPrinterModelBtn');

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
                    models.forEach(model => {
                        printerModelSelect.add(new Option(model.name, model.id));
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    printerModelSelect.innerHTML = '<option value="">Error al cargar modelos</option>';
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
    const titles = {
        'Brand': 'Marca de Consumible',
        'ConsumableModel': 'Modelo de Consumible',
        'Supplier': 'Proveedor',
        'PrinterBrand': 'Marca de Impresora',
        'PrinterModel': 'Modelo de Impresora',
        'Department': 'Departamento'
    };
    
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
                const selectIdMap = {
                    'Brand': 'brand_select',
                    'ConsumableModel': 'consumable_model_select',
                    'Supplier': 'supplier_select',
                    'PrinterBrand': 'printer_brand_select',
                    'PrinterModel': 'printer_model_select',
                    'Department': 'department_select'
                };
                
                const selectElement = document.getElementById(selectIdMap[currentCategoryType]);
                selectElement.add(new Option(result.data.name, result.data.id, true, true));
                categoryModal.hide();
                
                if (typeof showToast === 'function') {
                    showToast(`Se agregó "${result.data.name}" correctamente.`, 'success');
                }
            } else {
                document.getElementById('category-error').textContent = result.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('category-error').textContent = 'Ocurrió un error de red.';
        });
}
</script>
<?php include 'views/templates/header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">📋 Lista de Productos y Precios</h1>
        <div class="btn-group">
            <a href="<?php echo BASE_URL; ?>cost-analysis/index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>cost-analysis/export-products-pdf" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>

    <!-- Resumen rápido -->
    <div class="row mb-4 g-2">
        <div class="col-xl col-lg-4 col-md-6 col-sm-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body py-2 d-flex flex-column justify-content-center">
                    <div class="text-center">
                        <h6 class="card-title mb-1">Tipos de Productos</h6>
                        <h4 class="mb-1"><?php echo count($data['all_products']); ?></h4>
                        <small class="opacity-75">SKUs únicos</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl col-lg-4 col-md-6 col-sm-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body py-2 d-flex flex-column justify-content-center">
                    <div class="text-center">
                        <h6 class="card-title mb-1">Total de Unidades</h6>
                        <h4 class="mb-1"><?php echo number_format($data['total_units'], 0, ',', '.'); ?></h4>
                        <small class="opacity-75">Cantidad total</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl col-lg-4 col-md-6 col-sm-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body py-2 d-flex flex-column justify-content-center">
                    <div class="text-center">
                        <h6 class="card-title mb-1">Valor Total</h6>
                        <h5 class="mb-1">Gs. <?php 
                            $total_value = 0;
                            foreach($data['all_products'] as $product) {
                                $total_value += $product['total_value'];
                            }
                            echo number_format($total_value, 0, ',', '.'); 
                        ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6 col-sm-6">
            <div class="card bg-warning text-white h-100">
                <div class="card-body py-2 d-flex flex-column justify-content-center">
                    <div class="text-center">
                        <h6 class="card-title mb-1">Stock Bajo</h6>
                        <h4 class="mb-1"><?php 
                            $low_stock = 0;
                            foreach($data['all_products'] as $product) {
                                if($product['current_stock'] <= 5) $low_stock++;
                            }
                            echo $low_stock; 
                        ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6 col-sm-6">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body py-2 d-flex flex-column justify-content-center">
                    <div class="text-center">
                        <h6 class="card-title mb-1">Costo Promedio</h6>
                        <h5 class="mb-1">Gs. <?php 
                            $total_cost = 0;
                            $count_with_cost = 0;
                            foreach($data['all_products'] as $product) {
                                if($product['cost'] > 0) {
                                    $total_cost += $product['cost'];
                                    $count_with_cost++;
                                }
                            }
                            $average_cost = $count_with_cost > 0 ? $total_cost / $count_with_cost : 0;
                            echo number_format($average_cost, 0, ',', '.'); 
                        ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Filtros Avanzados -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter"></i> Filtros Avanzados
                        <button class="btn btn-sm btn-outline-primary float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false">
                            <i class="fas fa-chevron-down"></i> Expandir/Contraer
                        </button>
                    </h5>
                </div>
                <div class="collapse" id="filtersCollapse">
                    <div class="card-body">
                        <div class="row">
                            <!-- Filtro por Marca -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Marca</label>
                                <select class="form-select" id="filterBrand">
                                    <option value="">Todas las marcas</option>
                                    <?php 
                                    $brands = array_unique(array_column($data['all_products'], 'brand_name'));
                                    sort($brands);
                                    foreach($brands as $brand): ?>
                                        <option value="<?php echo htmlspecialchars($brand); ?>"><?php echo htmlspecialchars($brand); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filtro por Tipo -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" id="filterType">
                                    <option value="">Todos los tipos</option>
                                    <?php 
                                    $types = array_unique(array_column($data['all_products'], 'type'));
                                    foreach($types as $type): ?>
                                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filtro por Color -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Color</label>
                                <select class="form-select" id="filterColor">
                                    <option value="">Todos los colores</option>
                                    <?php 
                                    $colors = array_unique(array_column($data['all_products'], 'color'));
                                    $colorOrder = ['Negro', 'Color', 'Cyan', 'Magenta', 'Yellow'];
                                    $orderedColors = array_intersect($colorOrder, $colors);
                                    $remainingColors = array_diff($colors, $colorOrder);
                                    $finalColors = array_merge($orderedColors, $remainingColors);
                                    foreach($finalColors as $color): ?>
                                        <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filtro por Proveedor -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Proveedor</label>
                                <select class="form-select" id="filterSupplier">
                                    <option value="">Todos los proveedores</option>
                                    <?php 
                                    $suppliers = array_unique(array_column($data['all_products'], 'supplier_name'));
                                    sort($suppliers);
                                    foreach($suppliers as $supplier): ?>
                                        <option value="<?php echo htmlspecialchars($supplier); ?>"><?php echo htmlspecialchars($supplier); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filtro por Rango de Stock -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nivel de Stock</label>
                                <select class="form-select" id="filterStock">
                                    <option value="">Todos los niveles</option>
                                    <option value="critical">Crítico (≤ 5)</option>
                                    <option value="low">Bajo (6-10)</option>
                                    <option value="medium">Medio (11-20)</option>
                                    <option value="high">Alto (> 20)</option>
                                </select>
                            </div>

                            <!-- Filtro por Rango de Costo -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Rango de Costo</label>
                                <select class="form-select" id="filterCost">
                                    <option value="">Todos los costos</option>
                                    <option value="0-50000">Gs. 0 - 50,000</option>
                                    <option value="50000-100000">Gs. 50,000 - 100,000</option>
                                    <option value="100000-200000">Gs. 100,000 - 200,000</option>
                                    <option value="200000+">Gs. 200,000+</option>
                                </select>
                            </div>

                            <!-- Filtro por Departamento -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Departamento</label>
                                <select class="form-select" id="filterDepartment">
                                    <option value="">Todos los departamentos</option>
                                    <?php 
                                    $allDepartments = [];
                                    foreach($data['all_products'] as $product) {
                                        if (!empty($product['departments'])) {
                                            $depts = explode(', ', $product['departments']);
                                            $allDepartments = array_merge($allDepartments, $depts);
                                        }
                                    }
                                    $uniqueDepartments = array_unique($allDepartments);
                                    sort($uniqueDepartments);
                                    foreach($uniqueDepartments as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Búsqueda por Texto -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Búsqueda General</label>
                                <input type="text" class="form-control" id="filterSearch" placeholder="Buscar en todas las columnas...">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-filter"></i> Aplicar Filtros
                                </button>
                                <button class="btn btn-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Limpiar Filtros
                                </button>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                                    <i class="fas fa-download"></i> Exportar Personalizado
                                </button>
                                <button class="btn btn-warning" onclick="shareFilteredView()">
                                    <i class="fas fa-share"></i> Compartir Vista
                                </button>
                                <span class="ms-3">
                                    <strong id="filteredCount"><?php echo count($data['all_products']); ?></strong> productos mostrados
                                </span>
                                <div class="mt-2">
                                    <small id="activeFilters" class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">📋 Lista Completa de Productos y Precios</h5>
                    <small class="text-muted">Información detallada de todos los productos en inventario</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="productsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Color</th>
                                    <th>Proveedor</th>
                                    <th>Tipo</th>
                                    <th>Cantidad Actual</th>
                                    <th>Costo Unitario</th>
                                    <th>Valor Total</th>
                                    <th>Impresora Compatible</th>
                                    <th>Departamento(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['all_products'])): ?>
                                    <?php foreach ($data['all_products'] as $product): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($product['brand_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($product['model_name']); ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?php echo $product['color'] === 'Negro' ? '#000' : ($product['color'] === 'Cyan' ? '#00FFFF' : ($product['color'] === 'Magenta' ? '#FF00FF' : ($product['color'] === 'Yellow' ? '#FFFF00' : '#6c757d'))); ?>; color: <?php echo in_array($product['color'], ['Negro']) ? 'white' : 'black'; ?>;">
                                                    <?php echo htmlspecialchars($product['color']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $product['type'] === 'Tóner' ? 'bg-primary' : 'bg-info'; ?>">
                                                    <?php echo htmlspecialchars($product['type']); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo $product['current_stock'] <= 5 ? 'bg-danger' : ($product['current_stock'] <= 10 ? 'bg-warning' : 'bg-success'); ?>">
                                                    <?php echo $product['current_stock']; ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="editable-cost" 
                                                     data-product-id="<?php echo $product['id']; ?>"
                                                     data-original-cost="<?php echo $product['cost']; ?>"
                                                     data-current-stock="<?php echo $product['current_stock']; ?>">
                                                    <span class="cost-display">
                                                        <strong>Gs. <?php echo number_format($product['cost'], 0, ',', '.'); ?></strong>
                                                        <i class="fas fa-edit ms-1 text-muted edit-icon" style="font-size: 12px; cursor: pointer;" title="Clic para editar"></i>
                                                    </span>
                                                    <input type="number" 
                                                           class="form-control form-control-sm cost-input d-none" 
                                                           value="<?php echo $product['cost']; ?>"
                                                           min="0"
                                                           step="1000"
                                                           placeholder="Ingrese el costo">
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">Gs. <?php echo number_format($product['total_value'], 0, ',', '.'); ?></strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($product['printer_brand_name'] . ' - ' . $product['printer_model_name']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($product['departments']); ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay productos disponibles</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Seleccionar Columnas de Exportación -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-cog"></i> Configurar Exportación Personalizada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-table"></i> Seleccionar Columnas a Incluir:</h6>
                        <div class="form-check-group">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="0" id="col-brand" checked>
                                <label class="form-check-label" for="col-brand">
                                    <i class="fas fa-tag"></i> Marca
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="1" id="col-model" checked>
                                <label class="form-check-label" for="col-model">
                                    <i class="fas fa-cube"></i> Modelo
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="2" id="col-color" checked>
                                <label class="form-check-label" for="col-color">
                                    <i class="fas fa-palette"></i> Color
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="3" id="col-supplier" checked>
                                <label class="form-check-label" for="col-supplier">
                                    <i class="fas fa-truck"></i> Proveedor
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="4" id="col-type" checked>
                                <label class="form-check-label" for="col-type">
                                    <i class="fas fa-layer-group"></i> Tipo
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check-group mt-4 mt-md-0">
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="5" id="col-stock" checked>
                                <label class="form-check-label" for="col-stock">
                                    <i class="fas fa-boxes"></i> Cantidad Actual
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="6" id="col-cost" checked>
                                <label class="form-check-label" for="col-cost">
                                    <i class="fas fa-dollar-sign"></i> Costo Unitario
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="7" id="col-total" checked>
                                <label class="form-check-label" for="col-total">
                                    <i class="fas fa-calculator"></i> Valor Total
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="8" id="col-printer" checked>
                                <label class="form-check-label" for="col-printer">
                                    <i class="fas fa-print"></i> Impresora Compatible
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input column-checkbox" type="checkbox" value="9" id="col-departments" checked>
                                <label class="form-check-label" for="col-departments">
                                    <i class="fas fa-building"></i> Departamento(s)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <h6><i class="fas fa-file-export"></i> Formato de Exportación:</h6>
                        <div class="btn-group w-100" role="group" aria-label="Formato de exportación">
                            <input type="radio" class="btn-check" name="exportFormat" id="formatExcel" value="excel" checked>
                            <label class="btn btn-outline-success" for="formatExcel">
                                <i class="fas fa-file-excel"></i> Excel
                            </label>
                            
                            <input type="radio" class="btn-check" name="exportFormat" id="formatPdf" value="pdf">
                            <label class="btn btn-outline-danger" for="formatPdf">
                                <i class="fas fa-file-pdf"></i> PDF
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Solo se exportarán las filas que estén visibles según los filtros aplicados.
                        <br><small>Columnas seleccionadas: <span id="selectedColumnsCount">10</span> de 10</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="selectAllColumns()">
                    <i class="fas fa-check-square"></i> Seleccionar Todo
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="deselectAllColumns()">
                    <i class="fas fa-square"></i> Deseleccionar Todo
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="exportCustomData()">
                    <i class="fas fa-download"></i> Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery y DataTables para la tabla de productos -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<style>
/* Estilos para los filtros */
.card-header h5 {
    font-weight: 600;
    color: #495057;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

/* Estilos para edición en línea de costos */
.editable-cost {
    position: relative;
}

.cost-display:hover {
    background-color: #f8f9fa;
    border-radius: 4px;
    padding: 2px 4px;
    cursor: pointer;
}

.cost-display:hover .edit-icon {
    color: #007bff !important;
}

.cost-input {
    width: 120px;
    text-align: right;
    font-weight: bold;
}

.cost-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.editing-cost {
    background-color: #fff3cd !important;
    border: 1px solid #ffeaa7;
    border-radius: 4px;
}

.cost-updated {
    background-color: #d4edda !important;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    animation: fadeToNormal 3s ease-in-out;
}

@keyframes fadeToNormal {
    0% { background-color: #d4edda; }
    100% { background-color: transparent; }
}

.cost-error {
    background-color: #f8d7da !important;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
}

.form-select, .form-control {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus, .form-control:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn-group .btn {
    margin-right: 0.5rem;
}

#filteredCount {
    color: #0d6efd;
    font-weight: 600;
}

/* Animación para el colapso de filtros */
.collapse {
    transition: height 0.35s ease;
}

.card {
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Estilos para la tabla de productos */
#productsTable {
    font-size: 0.875rem;
}

#productsTable th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    vertical-align: middle;
}

#productsTable td {
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

#productsTable tbody tr:hover {
    background-color: #f8f9fa;
}

.table-responsive {
    border-radius: 0.5rem;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Estilos para el modal de exportación */
.form-check-group .form-check {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.15s ease-in-out;
}

.form-check-group .form-check:hover {
    background-color: #f8f9fa;
}

.form-check-group .form-check-label {
    font-weight: 500;
    cursor: pointer;
    margin-left: 0.5rem;
}

.form-check-group .form-check-label i {
    width: 20px;
    text-align: center;
    margin-right: 0.5rem;
    color: #6c757d;
}

.form-check-input:checked + .form-check-label i {
    color: #0d6efd;
}

#selectedColumnsCount {
    font-weight: 600;
    color: #0d6efd;
}

.btn-check:checked + .btn {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

/* Estilos para DataTables */
.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin-top: 1rem;
}

/* Tarjetas de resumen */
.card {
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.15s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>

<script>
let dataTable;
let originalData = [];

// Función de diagnóstico para debugging
window.debugDataTable = function() {
    console.log('=== DIAGNÓSTICO DE DATATABLE ===');
    console.log('1. Variable dataTable:', !!dataTable);
    console.log('2. Tipo de dataTable:', typeof dataTable);
    console.log('3. jQuery disponible:', typeof jQuery !== 'undefined');
    console.log('4. $ disponible:', typeof $ !== 'undefined');
    
    if (typeof $ !== 'undefined') {
        console.log('5. DataTables disponible:', typeof $.fn.DataTable !== 'undefined');
        console.log('6. Tabla existe:', $('#productsTable').length > 0);
        console.log('7. isDataTable:', $.fn.DataTable.isDataTable('#productsTable'));
        console.log('8. Filas en tabla:', $('#productsTable tbody tr').length);
    } else {
        console.log('5-8. jQuery no disponible, no se pueden verificar DataTables');
    }
    
    if (dataTable) {
        try {
            console.log('9. Métodos disponibles:', Object.getOwnPropertyNames(dataTable).filter(prop => typeof dataTable[prop] === 'function').slice(0, 10));
            console.log('10. Función search disponible:', typeof dataTable.search === 'function');
            console.log('11. Función draw disponible:', typeof dataTable.draw === 'function');
        } catch (e) {
            console.error('Error accediendo a métodos:', e);
        }
    }
    
    return {
        dataTable: !!dataTable,
        jqueryAvailable: typeof jQuery !== 'undefined',
        dollarAvailable: typeof $ !== 'undefined',
        isInitialized: typeof $ !== 'undefined' && $.fn.DataTable && $.fn.DataTable.isDataTable('#productsTable'),
        canSearch: dataTable && typeof dataTable.search === 'function'
    };
};

// Función para probar filtros desde la consola
window.testFilters = function() {
    console.log('=== PROBANDO FILTROS ===');
    
    if (!dataTable) {
        console.error('DataTable no disponible');
        return;
    }
    
    // Obtener datos únicos para cada columna
    const data = dataTable.rows().data().toArray();
    console.log('Total de filas:', data.length);
    
    // Extraer valores únicos
    const brands = [...new Set(data.map(row => row[0]))].filter(v => v);
    const colors = [...new Set(data.map(row => row[2]))].filter(v => v);
    const suppliers = [...new Set(data.map(row => row[3]))].filter(v => v);
    const types = [...new Set(data.map(row => row[4]))].filter(v => v);
    
    console.log('Marcas disponibles:', brands);
    console.log('Colores disponibles:', colors);
    console.log('Proveedores disponibles:', suppliers);
    console.log('Tipos disponibles:', types);
    
    // Probar filtro simple
    console.log('Probando filtro de marca con primera marca disponible...');
    if (brands.length > 0) {
        dataTable.column(0).search(brands[0]).draw();
        const info = dataTable.page.info();
        console.log('Resultados después del filtro:', info.recordsDisplay);
        
        // Limpiar filtro
        dataTable.column(0).search('').draw();
        console.log('Filtro limpiado');
    }
};

// Esperar a que todo esté cargado
$(document).ready(function() {
    console.log('=== INICIALIZACIÓN DE DATATABLE ===');
    console.log('Documento listo, inicializando DataTable...');
    console.log('Timestamp:', new Date().toISOString());
    
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('✗ jQuery no está cargado');
        return;
    }
    console.log('✓ jQuery está disponible:', $.fn.jquery);
    
    // Verificar que DataTables esté disponible
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('✗ DataTables no está cargado');
        return;
    }
    console.log('✓ DataTables está disponible:', $.fn.DataTable.version);
    
    // Inicializar DataTables para la tabla de productos
    const tableElement = document.getElementById('productsTable');
    if (tableElement) {
        console.log('✓ Tabla encontrada, contenido:', {
            rows: $('#productsTable tbody tr').length,
            columns: $('#productsTable thead th').length,
            hasData: $('#productsTable tbody tr').length > 0
        });
        
        try {
            dataTable = $('#productsTable').DataTable({
                responsive: true,
                language: {
                    "decimal": "",
                    "emptyTable": "No hay productos disponibles",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ productos",
                    "infoEmpty": "Mostrando 0 a 0 de 0 productos",
                    "infoFiltered": "(filtrado de _MAX_ productos totales)",
                    "infoPostFix": "",
                    "thousands": ".",
                    "lengthMenu": "Mostrar _MENU_ productos",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron productos",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                pageLength: 25,
                order: [[ 0, 'asc' ]], // Ordenar por marca por defecto
                columnDefs: [
                    { targets: [5, 6, 7], className: 'text-end' }, // Alinear números a la derecha
                    { targets: [4, 5], searchable: false } // No buscar en badges
                ],
                initComplete: function() {
                    console.log('✓ DataTable inicializado correctamente');
                    console.log('DataTable instance:', this.api());
                    console.log('DataTable methods available:', Object.keys(this.api()));
                    
                    // Verificar funcionalidades básicas
                    try {
                        const rowCount = this.api().rows().count();
                        console.log('✓ Número de filas:', rowCount);
                        
                        // Probar búsqueda
                        this.api().search('').draw();
                        console.log('✓ Función de búsqueda funcional');
                        
                    } catch (testError) {
                        console.error('Error probando funcionalidades:', testError);
                    }
                    
                    // Guardar datos originales para filtros
                    originalData = this.api().rows().data().toArray();
                    console.log('✓ Datos originales guardados:', originalData.length, 'filas');
                    
                    // Cargar filtros desde URL si existen
                    loadFiltersFromURL();
                }
            });
            
            console.log('✓ DataTable creado exitosamente');
            console.log('Variable dataTable asignada:', !!dataTable);
            console.log('DataTable es función:', typeof dataTable);
            
            // Verificación adicional después de crear DataTable
            setTimeout(function() {
                console.log('=== VERIFICACIÓN POST-INICIALIZACIÓN ===');
                console.log('DataTable variable:', !!dataTable);
                console.log('DataTable isDataTable:', $.fn.DataTable.isDataTable('#productsTable'));
                console.log('DataTable API disponible:', dataTable && typeof dataTable.search === 'function');
                
                if (dataTable && typeof dataTable.search === 'function') {
                    console.log('✓ DataTable completamente funcional');
                    updateFilteredCount(); // Actualizar contador inicial
                } else {
                    console.error('✗ DataTable no está completamente funcional');
                }
            }, 500);
            
            // Inicializar estado de filtros
            updateActiveFilters();
            
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
        }
        
    } else {
        console.error('Tabla no encontrada');
    }
    
    // Event listeners para filtros en tiempo real
    // Esperar un poco para que se inicialice DataTable
    setTimeout(function() {
        console.log('Configurando event listeners...');
        
        // Filtros en tiempo real para algunos campos
        if (document.getElementById('filterSearch')) {
            document.getElementById('filterSearch').addEventListener('input', function() {
                setTimeout(applyFilters, 300); // Debounce de 300ms
            });
        }
        
        // Filtros inmediatos para selects
        ['filterBrand', 'filterType', 'filterColor', 'filterSupplier', 'filterStock', 'filterCost', 'filterDepartment'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', applyFilters);
            }
        });
        
        console.log('Event listeners configurados');
        
        // Configurar edición en línea de costos
        setupInlineEditing();
    }, 1000); // Aumenté el tiempo de espera
});

// Función para configurar edición en línea de costos
function setupInlineEditing() {
    console.log('Configurando edición en línea de costos...');
    
    // Event delegation para manejar clics en elementos dinámicos
    $(document).on('click', '.cost-display', function() {
        const container = $(this).closest('.editable-cost');
        startCostEdit(container);
    });
    
    // Manejar teclas en inputs de costo
    $(document).on('keydown', '.cost-input', function(e) {
        if (e.key === 'Enter') {
            saveCostEdit($(this).closest('.editable-cost'));
        } else if (e.key === 'Escape') {
            cancelCostEdit($(this).closest('.editable-cost'));
        }
    });
    
    // Manejar pérdida de foco
    $(document).on('blur', '.cost-input', function() {
        const container = $(this).closest('.editable-cost');
        setTimeout(() => {
            if (container.hasClass('editing-cost')) {
                saveCostEdit(container);
            }
        }, 150);
    });
    
    console.log('Edición en línea configurada');
}

// Función para iniciar edición de costo
function startCostEdit(container) {
    if (container.hasClass('editing-cost')) return;
    
    const display = container.find('.cost-display');
    const input = container.find('.cost-input');
    
    // Cambiar a modo edición
    container.addClass('editing-cost');
    display.addClass('d-none');
    input.removeClass('d-none').focus().select();
}

// Función para guardar edición de costo
function saveCostEdit(container) {
    const input = container.find('.cost-input');
    const newCost = parseInt(input.val()) || 0;
    const productId = container.data('product-id');
    const originalCost = container.data('original-cost');
    const currentStock = container.data('current-stock');
    
    if (newCost < 0) {
        alert('El costo no puede ser negativo');
        cancelCostEdit(container);
        return;
    }
    
    if (newCost === originalCost) {
        cancelCostEdit(container);
        return;
    }
    
    // Mostrar indicador de carga
    container.removeClass('editing-cost').addClass('cost-updating');
    
    // Actualizar en servidor
    updateCostInDatabase(productId, newCost, container, currentStock);
}

// Función para cancelar edición de costo
function cancelCostEdit(container) {
    const display = container.find('.cost-display');
    const input = container.find('.cost-input');
    const originalCost = container.data('original-cost');
    
    // Restaurar valor original
    input.val(originalCost);
    
    // Volver a modo display
    container.removeClass('editing-cost cost-updating cost-error');
    input.addClass('d-none');
    display.removeClass('d-none');
}

// Función para actualizar costo en base de datos
function updateCostInDatabase(productId, newCost, container, currentStock) {
    console.log('Actualizando costo en BD:', { productId, newCost });
    
    fetch('<?php echo BASE_URL; ?>cost-analysis/update-cost', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId,
            new_cost: newCost
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Actualización exitosa
            updateCostDisplay(container, newCost, currentStock);
            container.removeClass('cost-updating').addClass('cost-updated');
            
            // Recalcular totales si es necesario
            if (typeof updateFilteredCount === 'function') {
                updateFilteredCount();
            }
            
            console.log('Costo actualizado exitosamente');
        } else {
            throw new Error(data.message || 'Error al actualizar el costo');
        }
    })
    .catch(error => {
        console.error('Error al actualizar costo:', error);
        container.removeClass('cost-updating').addClass('cost-error');
        alert('Error al actualizar el costo: ' + error.message);
        cancelCostEdit(container);
    });
}

// Función para actualizar la visualización del costo
function updateCostDisplay(container, newCost, currentStock) {
    const display = container.find('.cost-display strong');
    const input = container.find('.cost-input');
    
    // Actualizar display
    display.text('Gs. ' + newCost.toLocaleString('es-PY'));
    
    // Actualizar valor total en la fila
    const row = container.closest('tr');
    const totalCell = row.find('td:nth-child(8) strong');
    const newTotal = newCost * currentStock;
    totalCell.text('Gs. ' + newTotal.toLocaleString('es-PY'));
    
    // Actualizar datos
    container.data('original-cost', newCost);
    
    // Volver a modo display
    input.addClass('d-none');
    container.find('.cost-display').removeClass('d-none');
}

// Función para aplicar filtros
function applyFilters() {
    console.log('=== APLICANDO FILTROS ===');
    
    // Ejecutar diagnóstico completo
    const diagnostic = window.debugDataTable();
    console.log('Diagnóstico:', diagnostic);
    
    if (!diagnostic.canSearch) {
        console.error('DataTable no está listo para búsquedas');
        alert('Error: La tabla no está lista. Diagnóstico disponible en consola (F12). Recarga la página.');
        return;
    }
    
    console.log('✓ DataTable listo, procediendo con filtros...');
    
    // Obtener valores de filtros
    const brand = document.getElementById('filterBrand').value;
    const type = document.getElementById('filterType').value;
    const color = document.getElementById('filterColor').value;
    const supplier = document.getElementById('filterSupplier').value;
    const department = document.getElementById('filterDepartment').value;
    const search = document.getElementById('filterSearch').value;
    const stockFilter = document.getElementById('filterStock').value;
    const costFilter = document.getElementById('filterCost').value;
    
    console.log('Valores de filtros:', { brand, type, color, supplier, department, search, stockFilter, costFilter });
    
    // Limpiar filtros previos
    dataTable.columns().search('').search('');
    
    // Aplicar búsqueda general primero si existe
    if (search) {
        console.log('Aplicando búsqueda general:', search);
        dataTable.search(search);
    } else {
        dataTable.search('');
    }
    
    // Aplicar filtros por columna específica
    if (brand) {
        console.log('Aplicando filtro de marca:', brand);
        dataTable.column(0).search(brand, false, false);
    }
    
    if (color) {
        console.log('Aplicando filtro de color:', color);
        dataTable.column(2).search(color, false, false);
    }
    
    if (supplier) {
        console.log('Aplicando filtro de proveedor:', supplier);
        dataTable.column(3).search(supplier, false, false);
    }
    
    if (type) {
        console.log('Aplicando filtro de tipo:', type);
        dataTable.column(4).search(type, false, false);
    }
    
    if (department) {
        console.log('Aplicando filtro de departamento:', department);
        dataTable.column(9).search(department, false, false);
    }
    
    // Realizar el dibujo de la tabla
    console.log('Ejecutando draw...');
    dataTable.draw();
    
    // Aplicar filtros adicionales después del draw
    setTimeout(function() {
        console.log('Aplicando filtros adicionales...');
        applyStockAndCostFilters();
        updateFilteredCount();
        updateActiveFilters();
        
        // Debug: mostrar información de filas después del filtrado
        const info = dataTable.page.info();
        console.log('Información de paginación:', info);
        console.log('Filas mostradas:', info.recordsDisplay, 'de', info.recordsTotal);
        
    }, 200);
    
    console.log('Filtros aplicados');
}

// Función para aplicar filtros de stock y costo
function applyStockAndCostFilters() {
    const stockFilter = document.getElementById('filterStock').value;
    const costFilter = document.getElementById('filterCost').value;
    
    if (!stockFilter && !costFilter) return;
    
    console.log('Aplicando filtros de stock y costo:', { stock: stockFilter, cost: costFilter });
    
    $('#productsTable tbody tr').each(function() {
        const $row = $(this);
        let hide = false;
        
        // Filtro por stock
        if (stockFilter) {
            const stockText = $row.find('td:eq(5)').text().trim();
            const stockValue = parseInt(stockText.replace(/[^\d]/g, '')) || 0;
            
            switch(stockFilter) {
                case 'critical': if (stockValue > 5) hide = true; break;
                case 'low': if (stockValue < 6 || stockValue > 10) hide = true; break;
                case 'medium': if (stockValue < 11 || stockValue > 20) hide = true; break;
                case 'high': if (stockValue <= 20) hide = true; break;
            }
        }
        
        // Filtro por costo
        if (costFilter && !hide) {
            const costText = $row.find('td:eq(6)').text().trim();
            const costValue = parseInt(costText.replace(/[^\d]/g, '')) || 0;
            
            switch(costFilter) {
                case '0-50000': if (costValue > 50000) hide = true; break;
                case '50000-100000': if (costValue < 50000 || costValue > 100000) hide = true; break;
                case '100000-200000': if (costValue < 100000 || costValue > 200000) hide = true; break;
                case '200000+': if (costValue < 200000) hide = true; break;
            }
        }
        
        if (hide) {
            $row.hide();
        } else {
            $row.show();
        }
    });
}

// Función para limpiar filtros
function clearFilters() {
    console.log('=== LIMPIANDO FILTROS ===');
    
    // Ejecutar diagnóstico completo
    const diagnostic = window.debugDataTable();
    console.log('Diagnóstico:', diagnostic);
    
    if (!diagnostic.canSearch) {
        console.error('DataTable no está listo para limpiar filtros');
        alert('Error: La tabla no está lista. Diagnóstico disponible en consola (F12). Recarga la página.');
        return;
    }
    
    console.log('✓ DataTable listo, procediendo con limpieza...');
    
    // Limpiar formulario
    document.getElementById('filterBrand').value = '';
    document.getElementById('filterType').value = '';
    document.getElementById('filterColor').value = '';
    document.getElementById('filterSupplier').value = '';
    document.getElementById('filterStock').value = '';
    document.getElementById('filterCost').value = '';
    document.getElementById('filterDepartment').value = '';
    document.getElementById('filterSearch').value = '';
    
    // Limpiar todos los filtros de DataTables
    dataTable
        .columns().search('')
        .search('')
        .draw();
    
    // Mostrar todas las filas
    $('#productsTable tbody tr').show();
    
    // Actualizar contador
    setTimeout(function() {
        updateFilteredCount();
        updateActiveFilters();
    }, 100);
    
    console.log('Filtros limpiados');
}

// Función para actualizar contador de productos filtrados
function updateFilteredCount() {
    if (!dataTable) return;
    
    try {
        // Obtener información de DataTables
        const info = dataTable.page.info();
        const visibleRows = info.recordsDisplay;
        
        console.log('Información de conteo:', {
            total: info.recordsTotal,
            filtered: info.recordsDisplay,
            start: info.start,
            end: info.end
        });
        
        if (document.getElementById('filteredCount')) {
            document.getElementById('filteredCount').textContent = visibleRows;
        }
        
        console.log('Contador actualizado a:', visibleRows, 'productos');
        
        // Si no hay resultados, mostrar mensaje adicional
        if (visibleRows === 0) {
            console.log('⚠️ No se encontraron productos con los filtros aplicados');
        }
        
    } catch (error) {
        console.error('Error al actualizar contador:', error);
        
        // Fallback: contar filas visibles manualmente
        const visibleRows = $('#productsTable tbody tr:visible').length;
        if (document.getElementById('filteredCount')) {
            document.getElementById('filteredCount').textContent = visibleRows;
        }
        console.log('Contador actualizado (fallback):', visibleRows);
    }
}

// Función para mostrar filtros activos
function updateActiveFilters() {
    const filters = {
        brand: document.getElementById('filterBrand').value,
        type: document.getElementById('filterType').value,
        color: document.getElementById('filterColor').value,
        supplier: document.getElementById('filterSupplier').value,
        stock: document.getElementById('filterStock').value,
        cost: document.getElementById('filterCost').value,
        department: document.getElementById('filterDepartment').value,
        search: document.getElementById('filterSearch').value
    };
    
    const activeFilters = [];
    
    if (filters.brand) activeFilters.push(`Marca: ${filters.brand}`);
    if (filters.type) activeFilters.push(`Tipo: ${filters.type}`);
    if (filters.color) activeFilters.push(`Color: ${filters.color}`);
    if (filters.supplier) activeFilters.push(`Proveedor: ${filters.supplier}`);
    if (filters.stock) {
        const stockLabels = {
            'critical': 'Crítico (≤ 5)',
            'low': 'Bajo (6-10)',
            'medium': 'Medio (11-20)',
            'high': 'Alto (> 20)'
        };
        activeFilters.push(`Stock: ${stockLabels[filters.stock]}`);
    }
    if (filters.cost) {
        const costLabels = {
            '0-50000': 'Gs. 0 - 50,000',
            '50000-100000': 'Gs. 50,000 - 100,000',
            '100000-200000': 'Gs. 100,000 - 200,000',
            '200000+': 'Gs. 200,000+'
        };
        activeFilters.push(`Costo: ${costLabels[filters.cost]}`);
    }
    if (filters.department) activeFilters.push(`Departamento: ${filters.department}`);
    if (filters.search) activeFilters.push(`Búsqueda: "${filters.search}"`);
    
    const activeFiltersElement = document.getElementById('activeFilters');
    if (activeFilters.length > 0) {
        activeFiltersElement.innerHTML = `<strong>Filtros activos:</strong> ${activeFilters.join(' | ')}`;
        activeFiltersElement.className = 'text-info';
    } else {
        activeFiltersElement.innerHTML = 'No hay filtros activos';
        activeFiltersElement.className = 'text-muted';
    }
}

// Función para compartir vista filtrada
function shareFilteredView() {
    const filters = {
        brand: document.getElementById('filterBrand').value,
        type: document.getElementById('filterType').value,
        color: document.getElementById('filterColor').value,
        supplier: document.getElementById('filterSupplier').value,
        stock: document.getElementById('filterStock').value,
        cost: document.getElementById('filterCost').value,
        department: document.getElementById('filterDepartment').value,
        search: document.getElementById('filterSearch').value
    };
    
    // Crear URL con parámetros
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            params.append(key, filters[key]);
        }
    });
    
    const shareUrl = window.location.origin + window.location.pathname + '?' + params.toString();
    
    // Copiar al portapapeles
    navigator.clipboard.writeText(shareUrl).then(function() {
        // Mostrar mensaje de éxito
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        button.classList.remove('btn-warning');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-warning');
        }, 2000);
    }).catch(function() {
        // Fallback para navegadores que no soportan clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = shareUrl;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        alert('URL copiada al portapapeles: ' + shareUrl);
    });
}

// Cargar filtros desde URL al inicializar
function loadFiltersFromURL() {
    const params = new URLSearchParams(window.location.search);
    
    if (params.get('brand')) document.getElementById('filterBrand').value = params.get('brand');
    if (params.get('type')) document.getElementById('filterType').value = params.get('type');
    if (params.get('color')) document.getElementById('filterColor').value = params.get('color');
    if (params.get('supplier')) document.getElementById('filterSupplier').value = params.get('supplier');
    if (params.get('stock')) document.getElementById('filterStock').value = params.get('stock');
    if (params.get('cost')) document.getElementById('filterCost').value = params.get('cost');
    if (params.get('department')) document.getElementById('filterDepartment').value = params.get('department');
    if (params.get('search')) document.getElementById('filterSearch').value = params.get('search');
    
    // Aplicar filtros si hay parámetros
    if (params.toString()) {
        setTimeout(function() {
            applyFilters();
            // Expandir panel de filtros si hay filtros activos
            document.getElementById('filtersCollapse').classList.add('show');
        }, 1000);
    }
}

// ==================== FUNCIONES PARA EXPORTACIÓN PERSONALIZADA ====================

// Función para actualizar contador de columnas seleccionadas
function updateColumnCount() {
    const checkboxes = document.querySelectorAll('.column-checkbox');
    const checked = document.querySelectorAll('.column-checkbox:checked');
    document.getElementById('selectedColumnsCount').textContent = checked.length;
}

// Función para seleccionar todas las columnas
function selectAllColumns() {
    const checkboxes = document.querySelectorAll('.column-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateColumnCount();
}

// Función para deseleccionar todas las columnas
function deselectAllColumns() {
    const checkboxes = document.querySelectorAll('.column-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateColumnCount();
}

// Función principal para exportación personalizada
function exportCustomData() {
    console.log('Iniciando exportación personalizada...');
    
    if (!dataTable) {
        alert('La tabla no está lista aún, por favor intente de nuevo');
        return;
    }
    
    // Obtener columnas seleccionadas
    const selectedColumns = [];
    const columnNames = [];
    const checkboxes = document.querySelectorAll('.column-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Debe seleccionar al menos una columna para exportar');
        return;
    }
    
    checkboxes.forEach(checkbox => {
        selectedColumns.push(parseInt(checkbox.value));
        columnNames.push(checkbox.nextElementSibling.textContent.trim());
    });
    
    // Obtener formato seleccionado
    const format = document.querySelector('input[name="exportFormat"]:checked').value;
    
    try {
        const visibleData = [];
        
        // Obtener solo las filas visibles después de filtrar
        dataTable.rows({ filter: 'applied' }).every(function() {
            const rowNode = this.node();
            const cells = rowNode.querySelectorAll('td');
            
            if (cells.length >= 9) {
                const fullRowData = [
                    cleanText(cells[0].textContent), // Marca
                    cleanText(cells[1].textContent), // Modelo
                    cleanText(cells[2].textContent), // Color
                    cleanText(cells[3].textContent), // Proveedor
                    cleanText(cells[4].textContent), // Tipo
                    cleanText(cells[5].textContent), // Cantidad
                    cleanText(cells[6].textContent), // Costo
                    cleanText(cells[7].textContent), // Valor Total
                    cleanText(cells[8].textContent), // Impresora
                    cleanText(cells[9] ? cells[9].textContent : '') // Departamentos
                ];
                
                // Filtrar solo las columnas seleccionadas
                const filteredRowData = selectedColumns.map(index => fullRowData[index]);
                visibleData.push(filteredRowData);
            }
        });
        
        if (visibleData.length === 0) {
            alert('No hay datos para exportar con los filtros actuales');
            return;
        }
        
        // Exportar según el formato seleccionado
        if (format === 'excel') {
            exportToCustomExcel(visibleData, columnNames, selectedColumns);
        } else if (format === 'pdf') {
            exportToCustomPdf(visibleData, columnNames, selectedColumns);
        }
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
        modal.hide();
        
        console.log(`Exportación ${format} completada con ${visibleData.length} filas y ${selectedColumns.length} columnas`);
        
    } catch (error) {
        console.error('Error en exportación personalizada:', error);
        alert('Error al exportar los datos: ' + error.message);
    }
}

// Función para exportar a Excel personalizado
function exportToCustomExcel(data, columnNames, selectedColumns) {
    console.log('Generando Excel personalizado...');
    
    const date = new Date().toLocaleDateString('es-PY');
    const time = new Date().toLocaleTimeString('es-PY');
    
    let htmlContent = `
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
            .header { background-color: #2c3e50; color: white; font-weight: bold; text-align: center; padding: 12px; }
            .subheader { background-color: #34495e; color: white; font-weight: bold; text-align: center; padding: 8px; font-size: 14px; }
            .data-header { background-color: #3498db; color: white; font-weight: bold; text-align: center; padding: 10px; }
            .text-cell { padding: 8px; border: 1px solid #bdc3c7; text-align: left; }
            .number-cell { padding: 8px; border: 1px solid #bdc3c7; text-align: right; }
            .center-cell { padding: 8px; border: 1px solid #bdc3c7; text-align: center; }
            .footer { background-color: #ecf0f1; font-weight: bold; text-align: center; padding: 8px; font-size: 12px; }
            tr:nth-child(even) { background-color: #f8f9fa; }
        </style>
    </head>
    <body>
        <table>
            <tr><td colspan="${columnNames.length}" class="header">REPORTE PERSONALIZADO DE PRODUCTOS</td></tr>
            <tr><td colspan="${columnNames.length}" class="subheader">Sistema de Gestión de Stock - Exportado el ${date} a las ${time}</td></tr>
            <tr><td colspan="${columnNames.length}"></td></tr>
            <tr>`;
    
    // Agregar headers de columnas seleccionadas
    columnNames.forEach(name => {
        htmlContent += `<td class="data-header">${name}</td>`;
    });
    htmlContent += '</tr>';
    
    // Agregar datos
    data.forEach(row => {
        htmlContent += '<tr>';
        row.forEach((cell, index) => {
            const columnIndex = selectedColumns[index];
            // Determinar el tipo de celda basado en la columna
            if (columnIndex === 5) { // Cantidad
                const quantity = cleanNumber(cell);
                htmlContent += `<td class="number-cell">${quantity}</td>`;
            } else if (columnIndex === 6) { // Costo
                const cost = cleanCurrency(cell);
                htmlContent += `<td class="number-cell">Gs. ${cost.toLocaleString('es-PY')}</td>`;
            } else if (columnIndex === 7) { // Valor Total
                const value = cleanCurrency(cell);
                htmlContent += `<td class="number-cell">Gs. ${value.toLocaleString('es-PY')}</td>`;
            } else {
                htmlContent += `<td class="text-cell">${escapeHtml(cell)}</td>`;
            }
        });
        htmlContent += '</tr>';
    });
    
    // Pie del reporte
    htmlContent += `
            <tr><td colspan="${columnNames.length}"></td></tr>
            <tr><td colspan="${columnNames.length}" class="footer">REPORTE PERSONALIZADO - SISTEMA DE GESTIÓN DE STOCK</td></tr>
            <tr>
                <td colspan="${Math.ceil(columnNames.length/2)}" class="footer">Total de productos: ${data.length}</td>
                <td colspan="${Math.floor(columnNames.length/2)}" class="footer">Columnas incluidas: ${columnNames.length}</td>
            </tr>
        </table>
    </body>
    </html>`;
    
    const content = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(htmlContent);
    downloadCustomFile(content, 'excel', columnNames.length, data.length);
}

// Función para exportar a PDF personalizado  
function exportToCustomPdf(data, columnNames, selectedColumns) {
    console.log('Generando PDF personalizado...');
    
    // Crear formulario para enviar datos al servidor
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo BASE_URL; ?>cost-analysis/export-custom-pdf';
    form.style.display = 'none';
    
    // Agregar datos
    const dataInput = document.createElement('input');
    dataInput.name = 'data';
    dataInput.value = JSON.stringify(data);
    form.appendChild(dataInput);
    
    // Agregar nombres de columnas
    const columnsInput = document.createElement('input');
    columnsInput.name = 'columns';
    columnsInput.value = JSON.stringify(columnNames);
    form.appendChild(columnsInput);
    
    // Agregar índices de columnas
    const indexesInput = document.createElement('input');
    indexesInput.name = 'column_indexes';
    indexesInput.value = JSON.stringify(selectedColumns);
    form.appendChild(indexesInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Función para descargar archivos personalizados
function downloadCustomFile(content, format, columnCount, rowCount) {
    const link = document.createElement('a');
    const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
    const filename = `Productos_Personalizado_${columnCount}col_${rowCount}filas_${timestamp}.${format === 'excel' ? 'xls' : 'pdf'}`;
    
    link.setAttribute('href', content);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Mostrar confirmación
    setTimeout(() => {
        alert(`Archivo ${format.toUpperCase()} exportado exitosamente:\n${filename}\n\nColumnas incluidas: ${columnCount}\nFilas exportadas: ${rowCount}`);
    }, 100);
}

// Event listeners para el modal
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar contador cuando cambian los checkboxes
    document.querySelectorAll('.column-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateColumnCount);
    });
    
    // Actualizar contador inicial
    updateColumnCount();
});

// Función para exportar datos filtrados
function exportFilteredData() {
    console.log('Exportando datos filtrados...');
    
    if (!dataTable) {
        alert('La tabla no está lista aún, por favor intente de nuevo');
        return;
    }
    
    try {
        const visibleData = [];
        
        // Obtener solo las filas visibles después de filtrar
        dataTable.rows({ filter: 'applied' }).every(function() {
            const rowNode = this.node();
            const cells = rowNode.querySelectorAll('td');
            
            if (cells.length >= 9) { // Asegurar que tenga todas las columnas
                const rowData = [
                    cleanText(cells[0].textContent), // Marca
                    cleanText(cells[1].textContent), // Modelo
                    cleanText(cells[2].textContent), // Color
                    cleanText(cells[3].textContent), // Proveedor
                    cleanText(cells[4].textContent), // Tipo
                    cleanText(cells[5].textContent), // Cantidad
                    cleanText(cells[6].textContent), // Costo Unitario
                    cleanText(cells[7].textContent), // Valor Total
                    cleanText(cells[8].textContent), // Impresora Compatible
                    cleanText(cells[9] ? cells[9].textContent : '') // Departamentos
                ];
                visibleData.push(rowData);
            }
        });
        
        if (visibleData.length === 0) {
            alert('No hay datos filtrados para exportar');
            return;
        }
        
        // Obtener estadísticas actuales
        const stats = getFilteredStats();
        
        // Crear contenido Excel con formato mejorado
        const excelContent = generateExcelContent(visibleData, stats);
        
        // Descargar archivo
        downloadExcelFile(excelContent);
        
        console.log('Exportación completada:', visibleData.length, 'productos');
        
    } catch (error) {
        console.error('Error al exportar:', error);
        alert('Error al generar el archivo de exportación');
    }
}

// Función auxiliar para limpiar texto
function cleanText(text) {
    if (!text) return '';
    return text.replace(/\s+/g, ' ').trim();
}

// Función para obtener estadísticas de datos filtrados
function getFilteredStats() {
    const info = dataTable.page.info();
    
    // Calcular estadísticas de filas visibles
    let totalValue = 0;
    let lowStockCount = 0;
    let totalCost = 0;
    let costCount = 0;
    
    dataTable.rows({ filter: 'applied' }).every(function() {
        const rowNode = this.node();
        const cells = rowNode.querySelectorAll('td');
        
        if (cells.length >= 8) {
            // Extraer valores numéricos
            const quantity = parseInt(cleanText(cells[5].textContent).replace(/\D/g, '')) || 0;
            const cost = parseInt(cleanText(cells[6].textContent).replace(/[^\d]/g, '')) || 0;
            const value = parseInt(cleanText(cells[7].textContent).replace(/[^\d]/g, '')) || 0;
            
            totalValue += value;
            if (quantity <= 5) lowStockCount++;
            if (cost > 0) {
                totalCost += cost;
                costCount++;
            }
        }
    });
    
    return {
        totalProducts: info.recordsDisplay,
        totalValue: totalValue,
        lowStockCount: lowStockCount,
        averageCost: costCount > 0 ? Math.round(totalCost / costCount) : 0
    };
}

// Función para generar contenido Excel con formato
function generateExcelContent(data, stats) {
    const date = new Date().toLocaleDateString('es-PY');
    const time = new Date().toLocaleTimeString('es-PY');
    
    // Crear contenido HTML para Excel (mejor formato)
    let htmlContent = `
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            .header { font-weight: bold; font-size: 14px; text-align: center; background-color: #4472C4; color: white; }
            .stats-header { font-weight: bold; background-color: #D9E2F3; text-align: center; }
            .stats-value { text-align: center; }
            .table-header { font-weight: bold; background-color: #4472C4; color: white; text-align: center; border: 1px solid black; }
            .data-cell { border: 1px solid #ccc; text-align: center; }
            .text-cell { border: 1px solid #ccc; text-align: left; }
            .number-cell { border: 1px solid #ccc; text-align: right; }
            .footer { font-style: italic; background-color: #F2F2F2; text-align: center; }
        </style>
    </head>
    <body>
        <table border="1" cellpadding="3" cellspacing="0">
            <!-- Encabezado Principal -->
            <tr>
                <td colspan="10" class="header">LISTA DE PRODUCTOS Y PRECIOS - DATOS FILTRADOS</td>
            </tr>
            <tr>
                <td colspan="10" class="header">Generado el: ${date} a las ${time}</td>
            </tr>
            <tr><td colspan="10"></td></tr>
            
            <!-- Estadísticas Resumidas -->
            <tr>
                <td colspan="10" class="stats-header">RESUMEN DE DATOS FILTRADOS</td>
            </tr>
            <tr>
                <td class="stats-header">Productos Mostrados</td>
                <td class="stats-value">${stats.totalProducts}</td>
                <td class="stats-header">Valor Total</td>
                <td class="stats-value">Gs. ${stats.totalValue.toLocaleString('es-PY')}</td>
                <td class="stats-header">Stock Bajo</td>
                <td class="stats-value">${stats.lowStockCount}</td>
                <td class="stats-header">Costo Promedio</td>
                <td class="stats-value">Gs. ${stats.averageCost.toLocaleString('es-PY')}</td>
                <td colspan="2"></td>
            </tr>
            <tr><td colspan="10"></td></tr>
            
            <!-- Encabezados de la Tabla -->
            <tr>
                <td class="table-header">MARCA</td>
                <td class="table-header">MODELO</td>
                <td class="table-header">COLOR</td>
                <td class="table-header">PROVEEDOR</td>
                <td class="table-header">TIPO</td>
                <td class="table-header">CANTIDAD ACTUAL</td>
                <td class="table-header">COSTO UNITARIO</td>
                <td class="table-header">VALOR TOTAL</td>
                <td class="table-header">IMPRESORA COMPATIBLE</td>
                <td class="table-header">DEPARTAMENTO(S)</td>
            </tr>`;
    
    // Agregar datos de productos
    data.forEach(row => {
        htmlContent += '<tr>';
        
        // Marca
        htmlContent += `<td class="text-cell">${escapeHtml(row[0])}</td>`;
        
        // Modelo
        htmlContent += `<td class="text-cell">${escapeHtml(row[1])}</td>`;
        
        // Color
        htmlContent += `<td class="data-cell">${escapeHtml(row[2])}</td>`;
        
        // Proveedor
        htmlContent += `<td class="text-cell">${escapeHtml(row[3])}</td>`;
        
        // Tipo
        htmlContent += `<td class="data-cell">${escapeHtml(row[4])}</td>`;
        
        // Cantidad (número)
        const quantity = cleanNumber(row[5]);
        htmlContent += `<td class="number-cell">${quantity}</td>`;
        
        // Costo Unitario (moneda)
        const cost = cleanCurrency(row[6]);
        htmlContent += `<td class="number-cell">Gs. ${cost.toLocaleString('es-PY')}</td>`;
        
        // Valor Total (moneda)
        const value = cleanCurrency(row[7]);
        htmlContent += `<td class="number-cell">Gs. ${value.toLocaleString('es-PY')}</td>`;
        
        // Impresora Compatible
        htmlContent += `<td class="text-cell">${escapeHtml(row[8])}</td>`;
        
        // Departamentos
        htmlContent += `<td class="text-cell">${escapeHtml(row[9] || '')}</td>`;
        
        htmlContent += '</tr>';
    });
    
    // Pie del reporte
    htmlContent += `
            <tr><td colspan="10"></td></tr>
            <tr>
                <td colspan="10" class="footer">SISTEMA DE GESTIÓN DE STOCK</td>
            </tr>
            <tr>
                <td colspan="5" class="footer">Total de productos en este reporte: ${data.length}</td>
                <td colspan="5" class="footer">Exportado el: ${date} ${time}</td>
            </tr>
        </table>
    </body>
    </html>`;
    
    return 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(htmlContent);
}

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// Función auxiliar para limpiar números
function cleanNumber(text) {
    const number = parseInt(String(text || '0').replace(/\D/g, '')) || 0;
    return number;
}

// Función auxiliar para limpiar valores de moneda
function cleanCurrency(text) {
    const number = parseInt(String(text || '0').replace(/[^\d]/g, '')) || 0;
    return number;
}

// Función para descargar archivo Excel
function downloadExcelFile(content) {
    const link = document.createElement('a');
    const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
    const filename = 'Productos_Filtrados_' + timestamp + '.xls';
    
    link.setAttribute('href', content);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Mostrar confirmación
    setTimeout(() => {
        alert('Archivo exportado exitosamente: ' + filename);
    }, 100);
}
</script>

<?php include 'views/templates/footer.php'; ?>

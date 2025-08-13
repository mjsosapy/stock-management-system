<?php $title = 'Dashboard Analítico'; include 'views/templates/header.php'; ?>

<!-- KPIs Cards -->
<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card kpi-card bg-primary text-white h-100 dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title">📦 Total Productos</h6>
            <h2 class="mb-0 stats-number"><?php echo number_format($data['total_products']); ?></h2>
          </div>
          <div class="align-self-center">
            <i class="fas fa-boxes fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
      <div class="card-footer bg-transparent border-0">
        <a href="<?php echo BASE_URL; ?>stock/report" class="btn btn-light btn-sm">Ver Inventario</a>
      </div>
    </div>
  </div>
  
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card kpi-card bg-warning text-white h-100 dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title">⚠️ Stock Bajo</h6>
            <h2 class="mb-0 stats-number"><?php echo $data['low_stock_count']; ?></h2>
            <small>≤ 5 unidades</small>
          </div>
          <div class="align-self-center">
            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
      <div class="card-footer bg-transparent border-0">
        <a href="<?php echo BASE_URL; ?>stock/lowStockReport" class="btn btn-light btn-sm">Ver Alertas</a>
      </div>
    </div>
  </div>
  
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card kpi-card bg-success text-white h-100 dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title">💰 Valor Total</h6>
            <h2 class="mb-0 stats-number">Gs. <?php echo number_format($data['total_stock_value'], 0, ',', '.'); ?></h2>
            <small>Inventario actual</small>
          </div>
          <div class="align-self-center">
            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
      <div class="card-footer bg-transparent border-0">
        <a href="<?php echo BASE_URL; ?>report/detailed" class="btn btn-light btn-sm">Ver Reporte</a>
      </div>
    </div>
  </div>
  
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card kpi-card bg-danger text-white h-100 dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title">❌ Sin Stock</h6>
            <h2 class="mb-0 stats-number"><?php echo $data['zero_stock_count']; ?></h2>
            <small>Productos agotados</small>
          </div>
          <div class="align-self-center">
            <i class="fas fa-times-circle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
      <div class="card-footer bg-transparent border-0">
        <a href="<?php echo BASE_URL; ?>stock/report?include_zero_stock=1" class="btn btn-light btn-sm">Ver Productos</a>
      </div>
    </div>
  </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
  <!-- Consumo por Departamento -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100 dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">📊 Consumo por Departamento (30 días)</h5>
      </div>
      <div class="card-body chart-container">
        <canvas id="departmentChart" height="300"></canvas>
      </div>
    </div>
  </div>
  
  <!-- Consumo por Tipo -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100 dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">🎯 Consumo por Tipo de Producto</h5>
      </div>
      <div class="card-body chart-container">
        <canvas id="typeChart" height="300"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <!-- Movimientos Recientes -->
  <div class="col-lg-8 mb-4">
    <div class="card h-100 dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">📈 Movimientos de Stock (14 días)</h5>
      </div>
      <div class="card-body chart-container">
        <canvas id="movementsChart" height="200"></canvas>
      </div>
    </div>
  </div>
  
  <!-- Distribución de Stock -->
  <div class="col-lg-4 mb-4">
    <div class="card h-100 dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">📊 Distribución de Stock</h5>
      </div>
      <div class="card-body chart-container">
        <canvas id="stockDistributionChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Top Productos y Alertas -->
<div class="row mb-4">
  <!-- Top Productos Más Consumidos -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100 dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">🔥 Top Productos Más Consumidos</h5>
        <small class="text-muted">Últimos 30 días</small>
      </div>
      <div class="card-body">
        <?php if (!empty($data['top_consumed_products'])): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($data['top_consumed_products'] as $index => $product): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                  <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                </div>
                <span class="badge bg-primary rounded-pill"><?php echo $product['total_consumed']; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted text-center">No hay datos de consumo en los últimos 30 días</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Alertas de Stock Bajo -->
  <div class="col-lg-6 mb-4">
    <div class="card h-100 dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">⚠️ Alertas de Stock Bajo</h5>
        <small class="text-muted">Productos con ≤ 5 unidades</small>
      </div>
      <div class="card-body">
        <?php if ($data['low_stock_items'] && $data['low_stock_items']->num_rows > 0): ?>
          <div class="list-group list-group-flush">
            <?php $count = 0; ?>
            <?php while ($item = $data['low_stock_items']->fetch_assoc()): ?>
              <?php if ($count >= 5) break; ?>
              <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                  <strong><?php echo htmlspecialchars($item['model_name']); ?></strong>
                  <br><small class="text-muted"><?php echo htmlspecialchars($item['color_display_name']); ?></small>
                </div>
                <span class="badge bg-warning text-dark"><?php echo $item['calculated_quantity']; ?></span>
              </div>
              <?php $count++; ?>
            <?php endwhile; ?>
          </div>
          <div class="text-center mt-3">
            <a href="<?php echo BASE_URL; ?>stock/lowStockReport" class="btn btn-warning btn-sm">Ver Todas las Alertas</a>
          </div>
        <?php else: ?>
          <div class="text-center text-success">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <p class="mb-0">¡Excelente! No hay productos con stock bajo</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Acciones Rápidas -->
<div class="row">
  <div class="col-12">
    <div class="card dashboard-card">
      <div class="card-header chart-card-header">
        <h5 class="mb-0">🚀 Acciones Rápidas</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-sm-6 mb-3">
            <a href="<?php echo BASE_URL; ?>stock/add" class="btn btn-success w-100">
              <i class="fas fa-plus-circle"></i> Agregar Producto
            </a>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
            <a href="<?php echo BASE_URL; ?>stock/issue" class="btn btn-warning w-100">
              <i class="fas fa-sign-out-alt"></i> Registrar Salida
            </a>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
            <a href="<?php echo BASE_URL; ?>stock/replenish" class="btn btn-info w-100">
              <i class="fas fa-plus"></i> Reponer Stock
            </a>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
            <a href="<?php echo BASE_URL; ?>history/movements" class="btn btn-secondary w-100">
              <i class="fas fa-history"></i> Ver Historial
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos desde PHP
    const departmentData = <?php echo json_encode($data['consumption_by_department']); ?>;
    const typeData = <?php echo json_encode($data['consumption_by_type']); ?>;
    const movementsData = <?php echo json_encode($data['recent_movements']); ?>;
    const stockDistributionData = <?php echo json_encode($data['stock_distribution']); ?>;

    // Configuración común
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    };

    // Gráfico de Consumo por Departamento
    if (departmentData.length > 0) {
        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels: departmentData.map(d => d.department_name || 'Sin Departamento'),
                datasets: [{
                    label: 'Cantidad Consumida',
                    data: departmentData.map(d => d.total_quantity),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ]
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Gráfico de Consumo por Tipo
    if (typeData.length > 0) {
        new Chart(document.getElementById('typeChart'), {
            type: 'doughnut',
            data: {
                labels: typeData.map(d => d.type),
                datasets: [{
                    data: typeData.map(d => d.total_quantity),
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0']
                }]
            },
            options: chartOptions
        });
    }

    // Gráfico de Movimientos Recientes
    if (movementsData.length > 0) {
        new Chart(document.getElementById('movementsChart'), {
            type: 'line',
            data: {
                labels: movementsData.map(d => {
                    const date = new Date(d.movement_date);
                    return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Salidas',
                    data: movementsData.map(d => d.total_out),
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Entradas',
                    data: movementsData.map(d => d.total_in),
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Gráfico de Distribución de Stock
    if (stockDistributionData.length > 0) {
        new Chart(document.getElementById('stockDistributionChart'), {
            type: 'pie',
            data: {
                labels: stockDistributionData.map(d => d.stock_level),
                datasets: [{
                    data: stockDistributionData.map(d => d.count),
                    backgroundColor: ['#DC3545', '#FFC107', '#28A745', '#17A2B8']
                }]
            },
            options: chartOptions
        });
    }
});
</script>

<?php include 'views/templates/footer.php'; ?>
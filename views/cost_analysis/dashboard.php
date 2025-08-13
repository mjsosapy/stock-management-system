<?php include 'views/templates/header.php'; ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">💰 <?php echo $data['title']; ?></h1>
        <div class="btn-group">
            <a href="<?php echo BASE_URL; ?>cost-analysis/products-list" class="btn btn-info">
                <i class="fas fa-list"></i> Ver Lista de Productos y Precios
            </a>
            <a href="<?php echo BASE_URL; ?>cost-analysis/detailed-report" class="btn btn-primary">
                <i class="fas fa-file-alt"></i> Reporte Detallado
            </a>
            <a href="<?php echo BASE_URL; ?>cost-analysis/export-pdf" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>

    <!-- Resumen de Costos -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-primary text-white h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">📦 Total Productos</h6>
                            <h2 class="mb-0 stats-number"><?php echo number_format($data['cost_summary']['total_products'] ?? 0); ?></h2>
                            <small><?php echo number_format($data['cost_summary']['products_with_cost'] ?? 0); ?> con costo definido</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-success text-white h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">💰 Valor Total Inventario</h6>
                            <h2 class="mb-0 stats-number">Gs. <?php echo number_format($data['cost_summary']['total_inventory_value'] ?? 0, 0, ',', '.'); ?></h2>
                            <small>Inventario valorizado</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-info text-white h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">📊 Costo Promedio</h6>
                            <h2 class="mb-0 stats-number">Gs. <?php echo number_format($data['cost_summary']['average_cost'] ?? 0, 0, ',', '.'); ?></h2>
                            <small>Por producto</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card kpi-card bg-warning text-white h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">🔝 Costo Más Alto</h6>
                            <h2 class="mb-0 stats-number">Gs. <?php echo number_format($data['cost_summary']['highest_cost'] ?? 0, 0, ',', '.'); ?></h2>
                            <small>Producto más caro</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Análisis -->
    <div class="row mb-4">
        <!-- Costos por Departamento -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">🏢 Costos por Departamento (6 meses)</h5>
                </div>
                <div class="card-body chart-container">
                    <canvas id="costByDepartmentChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Costos por Tipo -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">🎯 Distribución de Costos por Tipo</h5>
                </div>
                <div class="card-body chart-container">
                    <canvas id="costByTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tendencia Mensual y Productos Costosos -->
    <div class="row mb-4">
        <!-- Tendencia Mensual -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">📈 Tendencia de Costos Mensuales</h5>
                </div>
                <div class="card-body chart-container">
                    <canvas id="monthlyTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Productos más Costosos -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">💎 Top 10 Productos Más Costosos</h5>
                </div>
                <div class="card-body">
                    <div class="high-cost-products-list">
                        <?php if (!empty($data['high_cost_products'])): ?>
                            <?php foreach (array_slice($data['high_cost_products'], 0, 10) as $index => $product): ?>
                                <div class="cost-product-item mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                            <small class="text-muted">
                                                Stock: <?php echo $product['current_stock']; ?> | 
                                                Consumo 3m: <?php echo $product['consumed_3months']; ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-primary">Gs. <?php echo number_format($product['cost'], 0, ',', '.'); ?></strong>
                                            <br>
                                            <small class="text-muted">Valor: Gs. <?php echo number_format($product['total_value'], 0, ',', '.'); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($index < 9): ?><hr><?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay productos con costos definidos</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis por Marcas y Eficiencia -->
    <div class="row mb-4">
        <!-- Costos por Marca -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">🏷️ Valor de Inventario por Marca</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['cost_by_brand'])): ?>
                        <div class="brand-cost-list">
                            <?php foreach (array_slice($data['cost_by_brand'], 0, 8) as $brand): ?>
                                <div class="brand-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($brand['brand_name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $brand['products_count']; ?> productos</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-success">Gs. <?php echo number_format($brand['total_inventory_value'], 0, ',', '.'); ?></strong>
                                            <br>
                                            <small class="text-muted">Avg: Gs. <?php echo number_format($brand['average_cost'], 0, ',', '.'); ?></small>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?php echo min(100, ($brand['total_inventory_value'] / max($data['cost_by_brand'][0]['total_inventory_value'], 1)) * 100); ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay datos de marcas disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Eficiencia de Costos por Departamento -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-header chart-card-header">
                    <h5 class="mb-0">⚡ Eficiencia de Costos por Departamento</h5>
                    <small class="text-muted">Costo por unidad consumida (últimos 6 meses)</small>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['cost_efficiency'])): ?>
                        <div class="efficiency-list">
                            <?php foreach ($data['cost_efficiency'] as $dept): ?>
                                <div class="efficiency-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($dept['department_name']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo $dept['total_consumed']; ?> unidades consumidas | 
                                                <?php echo $dept['different_products']; ?> productos diferentes
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge <?php echo $dept['cost_per_unit_consumed'] <= 50000 ? 'bg-success' : ($dept['cost_per_unit_consumed'] <= 100000 ? 'bg-warning' : 'bg-danger'); ?>">
                                                Gs. <?php echo number_format($dept['cost_per_unit_consumed'], 0, ',', '.'); ?>/u
                                            </span>
                                            <br>
                                            <small class="text-muted">Total: Gs. <?php echo number_format($dept['total_spent'], 0, ',', '.'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay datos de eficiencia disponibles</p>
                        </div>
                    <?php endif; ?>
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
    const costByDepartmentData = <?php echo json_encode($data['cost_by_department']); ?>;
    const costByTypeData = <?php echo json_encode($data['cost_by_type']); ?>;
    const monthlyTrendData = <?php echo json_encode($data['monthly_cost_trend']); ?>;

    // Configuración común de colores
    const colors = {
        primary: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#17a2b8',
        purple: '#6f42c1',
        orange: '#fd7e14',
        pink: '#e83e8c'
    };

    // Gráfico de Costos por Departamento
    if (costByDepartmentData.length > 0) {
        const deptCtx = document.getElementById('costByDepartmentChart').getContext('2d');
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: costByDepartmentData.map(d => d.department_name),
                datasets: [{
                    label: 'Costo Total (Gs.)',
                    data: costByDepartmentData.map(d => parseFloat(d.total_cost)),
                    backgroundColor: colors.primary,
                    borderColor: colors.primary,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Gs. ' + value.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 4
                    }
                }
            }
        });
    }

    // Gráfico de Costos por Tipo
    if (costByTypeData.length > 0) {
        const typeCtx = document.getElementById('costByTypeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: costByTypeData.map(d => d.type),
                datasets: [{
                    data: costByTypeData.map(d => parseFloat(d.total_value)),
                    backgroundColor: [colors.success, colors.warning, colors.info, colors.danger, colors.purple],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Gs. ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico de Tendencia Mensual
    if (monthlyTrendData.length > 0) {
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: monthlyTrendData.map(d => {
                    const date = new Date(d.month + '-01');
                    return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'Costo Mensual (Gs.)',
                    data: monthlyTrendData.map(d => parseFloat(d.monthly_cost)),
                    borderColor: colors.success,
                    backgroundColor: colors.success + '20',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Gs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php include 'views/templates/footer.php'; ?>

<?php 
$title = 'Reporte Detallado'; 
include 'views/templates/header.php'; 
?>

<h2 class="mb-4">📊 Reporte Detallado de Inventario</h2>

<div class="card shadow-sm mb-4">
    <div class="card-header"><strong>🔍 Filtrar Reporte</strong></div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>report/detailed" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3"><label for="start_date" class="form-label">Fecha de Inicio</label><input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date'] ?? ''); ?>" required></div>
            <div class="col-md-3"><label for="end_date" class="form-label">Fecha de Fin</label><input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date'] ?? ''); ?>" required></div>
            <div class="col-md-3"><label for="department_id" class="form-label">Departamento</label><select id="department_id" name="department_id" class="form-select"><option value="">Todos</option><?php $data['departments']->data_seek(0); while($d = $data['departments']->fetch_assoc()): ?><option value="<?php echo $d['id']; ?>" <?php if (($data['department_id'] ?? '') == $d['id']) echo 'selected'; ?>><?php echo htmlspecialchars($d['name']); ?></option><?php endwhile; ?></select></div>
            <div class="col-md-3 d-flex"><button type="submit" class="btn btn-primary w-100 me-2">Ver</button><a href="#" id="generate-pdf-btn" class="btn btn-danger w-100" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a></div>
        </form>
    </div>
</div>

<?php if ($data['report_data']): ?>
    <h3 class="mt-5">Resumen General</h3><hr>
    <div class="row mb-4">
        <div class="col-md-6"><div class="card text-center text-white bg-primary h-100"><div class="card-header"><strong>Valor Total del Inventario Actual</strong></div><div class="card-body"><h3 class="card-title mb-0">$<?php echo number_format($data['report_data']['total_stock_value'], 2); ?></h3></div></div></div>
        <div class="col-md-6"><div class="card text-center text-white bg-danger h-100"><div class="card-header"><strong>Devoluciones a Proveedor en Periodo</strong></div><div class="card-body"><h3 class="card-title mb-0"><?php echo $data['report_data']['defective_returns']->num_rows; ?></h3></div></div></div>
    </div>

    <h3 class="mt-5">Consumo por Departamento</h3><hr>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha de Entrega</th>
                            <th>Departamento</th>
                            <th>Retirado por</th>
                            <th>Insumo</th>
                            <th>Cantidad</th>
                            <th>Costo Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $consumption = $data['report_data']['consumption_by_department'];
                            if ($consumption->num_rows > 0):
                                while($row = $consumption->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['movement_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['department_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['recipient_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars(($row['brand_name'] ?? '') . ' ' . ($row['model_name'] ?? '') . ' - ' . ($row['color'] ?? '')); ?></td>
                                <td class="text-center"><?php echo $row['total_quantity']; ?></td>
                                <td class="text-end">Gs. <?php echo number_format($row['total_cost'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php 
                                endwhile;
                            else: 
                        ?>
                            <tr><td colspan="6" class="text-center text-muted"><em>No hubo consumo en el periodo seleccionado.</em></td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total General:</th>
                            <th class="text-end">Gs. <?php echo number_format($data['report_data']['grand_total_consumption_cost'], 0, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>
                </div>
        </div>
    </div>

    <h3 class="mt-5">Detalle de Devoluciones a Proveedor por Falla</h3><hr>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-danger">
                        <tr><th>Fecha</th><th>Proveedor</th><th>Insumo</th><th>Cantidad</th><th>Motivo de la Falla</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                            $returns = $data['report_data']['defective_returns']; $returns->data_seek(0);
                            if ($returns->num_rows > 0): 
                                while($row = $returns->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['movement_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(($row['brand_name'] ?? '') . ' ' . ($row['model_name'] ?? '') . ' - ' . ($row['color'] ?? '')); ?></td>
                                <td class="text-center"><?php echo $row['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($row['reason'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center text-muted"><em>No se registraron devoluciones en este periodo.</em></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include 'views/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const departmentIdInput = document.getElementById('department_id');
    const pdfBtn = document.getElementById('generate-pdf-btn');

    function updatePdfLink() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const departmentId = departmentIdInput.value;
        const url = `<?php echo BASE_URL; ?>report/generatePdf?start_date=${startDate}&end_date=${endDate}&department_id=${departmentId}`;
        pdfBtn.href = url;
    }

    startDateInput.addEventListener('change', updatePdfLink);
    endDateInput.addEventListener('change', updatePdfLink);
    departmentIdInput.addEventListener('change', updatePdfLink);
    updatePdfLink();
});
</script>

<?php include 'views/templates/footer.php'; ?>

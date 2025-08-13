<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Detallado de Inventario</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 10px; }
        .header h1 { font-size: 22px; text-align: center; color: #2c3e50; }
        .sub-header { text-align: center; font-size: 12px; color: #7f8c8d; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; word-wrap: break-word; }
        thead { background-color: #34495e; color: #ffffff; }
        th { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f2f2f2; }
        .section-title { font-size: 16px; color: #3498db; border-bottom: 1px solid #3498db; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; }
        .summary-card { border: 1px solid #ccc; padding: 15px; text-align: center; width: 45%; display: inline-block; margin: 0 2%; }
        .summary-card .value { font-size: 20px; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .footer { position: fixed; bottom: 0; text-align: center; width: 100%; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header"><h1>Reporte Detallado de Inventario</h1></div>
    <div class="sub-header">
        Periodo del <?php echo htmlspecialchars(date('d/m/Y', strtotime($data['start_date'] ?? ''))); ?> al <?php echo htmlspecialchars(date('d/m/Y', strtotime($data['end_date'] ?? ''))); ?><br>
        Departamento: <?php echo htmlspecialchars($data['department_name'] ?? 'Todos'); ?>
    </div>

    <div class="section-title">Resumen General</div>
    <table><tbody><tr><td class="text-center"><strong>Valor Total del Inventario Actual</strong><br><span class="value">$<?php echo number_format($data['report_data']['total_stock_value'], 2); ?></span></td><td class="text-center"><strong>Devoluciones a Proveedor en Periodo</strong><br><span class="value"><?php echo $data['report_data']['defective_returns']->num_rows; ?></span></td></tr></tbody></table>

    <div class="section-title">Consumo por Departamento</div>
    <table>
        <thead><tr><th>Fecha Entrega</th><th>Departamento</th><th>Retirado por</th><th>Insumo</th><th>Cant.</th><th>Costo Total</th></tr></thead>
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
            <?php endwhile; else: ?>
                <tr><td colspan="6" class="text-center">No hubo consumo en el periodo seleccionado.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Total General:</th>
                <th class="text-end">Gs. <?php echo number_format($data['report_data']['grand_total_consumption_cost'], 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="section-title">Detalle de Devoluciones a Proveedor por Falla</div>
    <table>
        <thead><tr><th>Fecha</th><th>Proveedor</th><th>Insumo</th><th>Cantidad</th><th>Motivo</th></tr></thead>
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
                <tr><td colspan="5" class="text-center">No se registraron devoluciones en este periodo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">Reporte generado el <?php echo date('d/m/Y H:i:s'); ?></div>
</body>
</html>

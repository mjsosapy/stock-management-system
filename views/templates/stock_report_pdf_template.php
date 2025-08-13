<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Stock Actual</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 10px; }
        .header h1 { font-size: 20px; text-align: center; color: #2c3e50; }
        .sub-header { text-align: center; font-size: 12px; color: #7f8c8d; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; word-wrap: break-word; }
        thead { background-color: #34495e; color: #ffffff; }
        th { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; height: 30px; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Stock Actual</h1>
    </div>
    <div class="sub-header">
        Generado el: <?php echo htmlspecialchars($data['generation_date']); ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Proveedor</th>
                <th>Tipo</th>
                <th class="text-center">Cantidad</th>
                <th>Impresora Compatible</th>
                <th>Departamento(s)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($data['stock_list']) && $data['stock_list']->num_rows > 0): ?>
                <?php while ($item = $data['stock_list']->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['brand_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['model_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['color_display_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['supplier_name'] ?? 'N/A'); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['type']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['calculated_quantity']); ?></td>
                        <td><?php echo htmlspecialchars(($item['printer_brand_name'] ?? '') . ' ' . ($item['printer_model_name'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($item['departments'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No se encontraron productos con los filtros seleccionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Página 1 de 1
    </div>
</body>
</html>
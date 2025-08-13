<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 9px; }
        .header h1 { font-size: 20px; text-align: center; color: #2c3e50; }
        .sub-header { text-align: center; font-size: 11px; color: #7f8c8d; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; word-wrap: break-word; }
        thead { background-color: #34495e; color: #ffffff; }
        th { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f2f2f2; }
        .section-title { font-size: 14px; color: #3498db; border-bottom: 1px solid #3498db; padding-bottom: 4px; margin-top: 20px; margin-bottom: 12px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .fw-bold { font-weight: bold; }
        .badge { display: inline-block; padding: .25em .4em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: .25rem; color: #fff; }
        .bg-primary { background-color: #007bff; }
        .bg-info { background-color: #17a2b8; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-dark { background-color: #343a40; }
        .bg-secondary { background-color: #6c757d; }
        .text-dark { color: #212529; }
        .badge-negro { background-color: #212529 !important; color: white; }
        .footer { position: fixed; bottom: 0; text-align: center; width: 100%; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header"><h1>Historial de Movimientos de Inventario</h1></div>
    <div class="sub-header">
        <?php if (!empty($data['filters']['start_date']) && !empty($data['filters']['end_date'])): ?>
            Periodo del <?php echo htmlspecialchars(date('d/m/Y', strtotime($data['filters']['start_date']))); ?> al <?php echo htmlspecialchars(date('d/m/Y', strtotime($data['filters']['end_date']))); ?>
        <?php else: ?>
            Reporte completo
        <?php endif; ?>
    </div>

    <div class="section-title">Resumen</div>
    <p><strong>Total de Cambios en Unidades:</strong> <?php echo $data['total_change']; ?></p>

    <div class="section-title">Movimientos</div>
    <table>
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Tipo de Movimiento</th>
                <th>Cambio</th>
                <th>Detalles Principales</th>
                <th>Referencia / Motivo Adicional</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($data['movements']->num_rows > 0): ?>
                <?php while ($row = $data['movements']->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['movement_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['brand'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['model'] ?? ''); ?></td>
                        <td>
                            <?php
                            $color = htmlspecialchars($row['color'] ?? 'Sin especificar');
                            if ($color !== 'Sin especificar') {
                                $color_class = 'bg-secondary';
                                $text_class = '';

                                if ($color === 'Negro') {
                                    $color_class = 'badge-negro';
                                } elseif ($color === 'Color') {
                                    $color_class = 'bg-info';
                                    $text_class = 'text-dark';
                                }
                                ?>
                                <span class="badge <?php echo $color_class; ?> <?php echo $text_class; ?>"><?php echo $color; ?></span>
                                <?php
                            } else {
                                ?>
                                <span class="text-muted">-</span>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                                $badge_class = 'bg-secondary'; $translated_type = 'DESCONOCIDO';
                                switch ($row['movement_type']) {
                                    case 'INITIAL_STOCK': $badge_class = 'bg-primary'; $translated_type = 'STOCK INICIAL'; break;
                                    case 'REPLENISHMENT': $badge_class = 'bg-info text-dark'; $translated_type = 'REPOSICIÓN'; break;
                                    case 'SALE': $badge_class = 'bg-success'; $translated_type = 'SALIDA / ENTREGA'; break;
                                    case 'RETURN_DEFECTIVE': $badge_class = 'bg-danger'; $translated_type = 'DEVOLUCIÓN POR FALLA'; break;
                                    case 'SALE_REVERSAL': $badge_class = 'bg-dark'; $translated_type = 'REVERSIÓN DE SALIDA'; break;
                                    case 'DEACTIVATED': $badge_class = 'bg-secondary'; $translated_type = 'DADO DE BAJA'; break;
                                }
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo $translated_type; ?></span>
                        </td>
                        <td class="fw-bold <?php echo ($row['quantity_change'] > 0) ? 'text-success' : ($row['quantity_change'] < 0 ? 'text-danger' : ''); ?>">
                            <?php echo ($row['quantity_change'] > 0) ? '+' : ''; ?><?php echo $row['quantity_change']; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['reason'] ?? 'N/A'); ?>
                            <?php if ($row['is_reversed'] > 0): ?><strong class="text-danger d-block">(Revertido)</strong><?php endif; ?>
                        </td>
                        <td></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">No se encontraron movimientos con los filtros seleccionados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">Reporte generado el <?php echo date('d/m/Y H:i:s'); ?></div>
</body>
</html>
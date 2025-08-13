<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Cotización #<?php echo $data['order_id']; ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .order-info {
            margin-bottom: 25px;
        }
        .order-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #bdc3c7;
            padding: 10px;
            text-align: left;
        }
        thead {
            background-color: #34495e;
            color: #ffffff;
        }
        th {
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        td.center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            text-align: center;
            width: 100%;
            font-size: 10px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Solicitud de Cotización</h1>
        </div>
        <div class="order-info">
            <p><strong>Número de Pedido:</strong> #<?php echo htmlspecialchars($data['order_id']); ?></p>
            <p><strong>Fecha de Solicitud:</strong> <?php echo htmlspecialchars($data['order_date']); ?></p>
        </div>
        
        <p>Por favor, cotizar los siguientes insumos:</p>

        <table>
            <thead>
                <tr>
                    <th>Modelo</th>
                    <th>Color</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($data['order_details']) && $data['order_details']->num_rows > 0): ?>
                    <?php while ($item = $data['order_details']->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['model_name']); ?></td>
                            <td class="center"><?php echo htmlspecialchars($item['color_display_name']); ?></td>
                            <td class="center"><?php echo htmlspecialchars($item['type']); ?></td>
                            <td class="center"><?php echo htmlspecialchars($item['quantity_requested']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="center">No hay artículos en este pedido.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Este documento es una solicitud de cotización y no una orden de compra.
    </div>
</body>
</html>
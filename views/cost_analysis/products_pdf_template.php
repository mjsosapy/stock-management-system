<?php
// Incluir dompdf
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar dompdf
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Crear el HTML del PDF
$html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>' . $data['title'] . '</title>
    <style>
        body {
            font-family: "Helvetica", "Arial", sans-serif;
            color: #333;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #7f8c8d;
            font-size: 12px;
            margin: 0;
        }
        .stats-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #bdc3c7;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
        }
        thead {
            background-color: #34495e;
            color: #ffffff;
        }
        th {
            text-align: center;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #bdc3c7;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>' . htmlspecialchars($data['title']) . '</h1>
            <p class="subtitle">Reporte generado el ' . $data['generated_date'] . '</p>
        </div>

        <div class="stats-section">
            <table style="width: 100%; border: none; margin: 0; padding: 0;">
                <tr>
                    <td style="width: 20%; text-align: center; border: none; padding: 10px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">' . number_format($data['stats']['total_products']) . '</div>
                        <div style="font-size: 11px; color: #7f8c8d;">Tipos de Productos</div>
                    </td>
                    <td style="width: 20%; text-align: center; border: none; padding: 10px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">' . number_format($data['stats']['total_units']) . '</div>
                        <div style="font-size: 11px; color: #7f8c8d;">Total de Unidades</div>
                    </td>
                    <td style="width: 20%; text-align: center; border: none; padding: 10px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">Gs. ' . number_format($data['stats']['total_value'], 0, ',', '.') . '</div>
                        <div style="font-size: 11px; color: #7f8c8d;">Valor Total</div>
                    </td>
                    <td style="width: 20%; text-align: center; border: none; padding: 10px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">' . number_format($data['stats']['low_stock_count']) . '</div>
                        <div style="font-size: 11px; color: #7f8c8d;">Stock Bajo</div>
                    </td>
                    <td style="width: 20%; text-align: center; border: none; padding: 10px;">
                        <div style="font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">Gs. ' . number_format($data['stats']['average_cost'], 0, ',', '.') . '</div>
                        <div style="font-size: 11px; color: #7f8c8d;">Costo Promedio</div>
                    </td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Marca</th>
                    <th style="width: 12%;">Modelo</th>
                    <th style="width: 8%;">Color</th>
                    <th style="width: 12%;">Proveedor</th>
                    <th style="width: 8%;">Tipo</th>
                    <th style="width: 8%;">Cantidad</th>
                    <th style="width: 12%;">Costo Unit.</th>
                    <th style="width: 12%;">Valor Total</th>
                    <th style="width: 16%;">Impresora</th>
                </tr>
            </thead>
            <tbody>';

foreach($data['products'] as $product) {
    // Determinar clase de stock
    $stock_class = '';
    if($product['current_stock'] <= 5) {
        $stock_class = 'badge-danger';
    } elseif($product['current_stock'] <= 10) {
        $stock_class = 'badge-warning';
    } else {
        $stock_class = 'badge-success';
    }

    // Determinar color del badge de tipo
    $type_class = $product['type'] === 'Tóner' ? 'badge-primary' : 'badge-info';

    $html .= '<tr>
        <td><strong>' . htmlspecialchars($product['brand_name']) . '</strong></td>
        <td>' . htmlspecialchars($product['model_name']) . '</td>
        <td class="text-center">' . htmlspecialchars($product['color']) . '</td>
        <td>' . htmlspecialchars($product['supplier_name']) . '</td>
        <td class="text-center"><span class="badge ' . $type_class . '">' . htmlspecialchars($product['type']) . '</span></td>
        <td class="text-center"><span class="badge ' . $stock_class . '">' . number_format($product['current_stock']) . '</span></td>
        <td class="text-right">Gs. ' . number_format($product['cost'], 0, ',', '.') . '</td>
        <td class="text-right">Gs. ' . number_format($product['total_value'], 0, ',', '.') . '</td>
        <td>' . htmlspecialchars($product['printer_brand_name'] . ' ' . $product['printer_model_name']) . '</td>
    </tr>';
}

$html .= '</tbody>
        </table>

        <div class="footer">
            <p><strong>Sistema de Gestión de Stock</strong></p>
            <p>Reporte generado automáticamente el ' . $data['generated_date'] . '</p>
            <p>Total de productos mostrados: ' . count($data['products']) . '</p>
        </div>
    </div>
</body>
</html>';

// Cargar HTML en dompdf
$dompdf->loadHtml($html);

// Configurar tamaño de página
$dompdf->setPaper('A4', 'landscape'); // Horizontal para mejor visualización de la tabla

// Renderizar PDF
$dompdf->render();

// Generar nombre del archivo
$filename = 'Lista_Productos_' . date('Y-m-d_H-i-s') . '.pdf';

// Enviar PDF al navegador
$dompdf->stream($filename, ['Attachment' => 0]); // 0 = mostrar en navegador, 1 = descargar
?>

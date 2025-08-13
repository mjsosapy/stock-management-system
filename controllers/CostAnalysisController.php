<?php
// controllers/CostAnalysisController.php

require_once 'BaseController.php';
require_once 'models/Stock.php';
require_once 'models/History.php';
require_once 'models/Department.php';
require_once 'models/Brand.php';

class CostAnalysisController extends BaseController {
    private $stockModel;
    private $historyModel;
    private $departmentModel;
    private $brandModel;

    public function __construct() {
        parent::__construct();
        $this->stockModel = new Stock($this->db);
        $this->historyModel = new History($this->db);
        $this->departmentModel = new Department($this->db);
        $this->brandModel = new Brand($this->db);
    }

    public function index() {
        $data = [
            'title' => 'Análisis de Costos',
            'cost_summary' => $this->getCostSummary(),
            'cost_by_department' => $this->getCostByDepartment(),
            'cost_by_brand' => $this->getCostByBrand(),
            'cost_by_type' => $this->getCostByType(),
            'monthly_cost_trend' => $this->getMonthlyCostTrend(),
            'high_cost_products' => $this->getHighCostProducts(),
            'cost_efficiency' => $this->getCostEfficiency()
        ];
        
        include 'views/cost_analysis/dashboard.php';
    }

    private function getCostSummary() {
        $query = "
            SELECT 
                COUNT(*) as total_products,
                SUM(cost * COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0)) as total_inventory_value,
                AVG(cost) as average_cost,
                MAX(cost) as highest_cost,
                MIN(cost) as lowest_cost,
                SUM(CASE WHEN cost > 0 THEN 1 ELSE 0 END) as products_with_cost
            FROM stock s 
            WHERE is_active = 1
        ";
        
        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc() : [];
    }

    private function getCostByDepartment() {
        $query = "
            SELECT 
                d.name as department_name,
                COUNT(DISTINCT sm.stock_id) as products_used,
                SUM(ABS(sm.quantity_change) * s.cost) as total_cost,
                AVG(s.cost) as average_product_cost,
                SUM(ABS(sm.quantity_change)) as total_quantity_consumed
            FROM stock_movements sm
            JOIN stock s ON sm.stock_id = s.id
            JOIN departments d ON sm.department_id = d.id
            WHERE sm.movement_type = 'SALE' 
            AND sm.movement_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            AND s.cost > 0
            GROUP BY d.id, d.name
            ORDER BY total_cost DESC
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getCostByBrand() {
        $query = "
            SELECT 
                b.name as brand_name,
                COUNT(s.id) as products_count,
                SUM(s.cost * COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0)) as total_inventory_value,
                AVG(s.cost) as average_cost,
                SUM(CASE WHEN COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) <= 5 THEN s.cost ELSE 0 END) as low_stock_value
            FROM stock s
            JOIN brands b ON s.brand_id = b.id
            WHERE s.is_active = 1 AND s.cost > 0
            GROUP BY b.id, b.name
            ORDER BY total_inventory_value DESC
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getCostByType() {
        $query = "
            SELECT 
                type,
                COUNT(*) as products_count,
                SUM(cost * COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0)) as total_value,
                AVG(cost) as average_cost,
                SUM(cost) as total_cost_all_products
            FROM stock s
            WHERE is_active = 1 AND cost > 0
            GROUP BY type
            ORDER BY total_value DESC
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getMonthlyCostTrend() {
        $query = "
            SELECT 
                DATE_FORMAT(sm.movement_date, '%Y-%m') as month,
                SUM(ABS(sm.quantity_change) * s.cost) as monthly_cost,
                COUNT(DISTINCT sm.stock_id) as products_used,
                AVG(s.cost) as average_cost_per_product
            FROM stock_movements sm
            JOIN stock s ON sm.stock_id = s.id
            WHERE sm.movement_type = 'SALE' 
            AND sm.movement_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            AND s.cost > 0
            GROUP BY DATE_FORMAT(sm.movement_date, '%Y-%m')
            ORDER BY month ASC
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getHighCostProducts() {
        $query = "
            SELECT 
                CONCAT(b.name, ' - ', cm.name, ' (', s.color, ')') as product_name,
                s.cost,
                COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as current_stock,
                s.cost * COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as total_value,
                COALESCE((SELECT SUM(ABS(quantity_change)) FROM stock_movements WHERE stock_id = s.id AND movement_type = 'SALE' AND movement_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)), 0) as consumed_3months
            FROM stock s
            JOIN brands b ON s.brand_id = b.id
            JOIN consumable_models cm ON s.consumable_model_id = cm.id
            WHERE s.is_active = 1 AND s.cost > 0
            ORDER BY s.cost DESC
            LIMIT 10
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getCostEfficiency() {
        $query = "
            SELECT 
                d.name as department_name,
                COUNT(DISTINCT sm.stock_id) as different_products,
                SUM(ABS(sm.quantity_change)) as total_consumed,
                SUM(ABS(sm.quantity_change) * s.cost) as total_spent,
                AVG(s.cost) as avg_cost_per_unit,
                SUM(ABS(sm.quantity_change) * s.cost) / NULLIF(SUM(ABS(sm.quantity_change)), 0) as cost_per_unit_consumed
            FROM stock_movements sm
            JOIN stock s ON sm.stock_id = s.id
            JOIN departments d ON sm.department_id = d.id
            WHERE sm.movement_type = 'SALE' 
            AND sm.movement_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            AND s.cost > 0
            GROUP BY d.id, d.name
            HAVING total_consumed > 0
            ORDER BY cost_per_unit_consumed ASC
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getAllProducts() {
        $query = "
            SELECT 
                s.id,
                b.name as brand_name,
                cm.name as model_name,
                s.color,
                sup.name as supplier_name,
                s.type,
                s.cost,
                pb.name as printer_brand_name,
                pm.name as printer_model_name,
                COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as current_stock,
                s.cost * COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as total_value,
                GROUP_CONCAT(DISTINCT d.name SEPARATOR ', ') as departments
            FROM stock s
            JOIN brands b ON s.brand_id = b.id
            JOIN consumable_models cm ON s.consumable_model_id = cm.id
            JOIN suppliers sup ON s.supplier_id = sup.id
            LEFT JOIN printer_brands pb ON s.printer_brand_id = pb.id
            LEFT JOIN printer_models pm ON s.printer_model_id = pm.id
            LEFT JOIN stock_departments sd ON s.id = sd.stock_id
            LEFT JOIN departments d ON sd.department_id = d.id
            WHERE s.is_active = 1
            GROUP BY s.id, b.name, cm.name, s.color, sup.name, s.type, s.cost, pb.name, pm.name
            ORDER BY b.name, cm.name, s.color
        ";
        
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function productsList() {
        $data = [
            'title' => 'Lista de Productos y Precios',
            'all_products' => $this->getAllProducts(),
            'total_units' => $this->getTotalUnits()
        ];
        
        include 'views/cost_analysis/products_list.php';
    }

    public function detailedReport() {
        $department_id = $_GET['department'] ?? null;
        $brand_id = $_GET['brand'] ?? null;
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        $data = [
            'title' => 'Reporte Detallado de Costos',
            'filters' => [
                'department_id' => $department_id,
                'brand_id' => $brand_id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ],
            'departments' => $this->departmentModel->getAll(),
            'brands' => $this->brandModel->getAll(),
            'detailed_costs' => $this->getDetailedCosts($department_id, $brand_id, $start_date, $end_date)
        ];

        include 'views/cost_analysis/detailed_report.php';
    }

    private function getDetailedCosts($department_id, $brand_id, $start_date, $end_date) {
        $where_conditions = ["sm.movement_type = 'SALE'", "s.cost > 0"];
        $params = [];
        $types = "";

        if ($department_id) {
            $where_conditions[] = "sm.department_id = ?";
            $params[] = $department_id;
            $types .= "i";
        }

        if ($brand_id) {
            $where_conditions[] = "s.brand_id = ?";
            $params[] = $brand_id;
            $types .= "i";
        }

        if ($start_date) {
            $where_conditions[] = "DATE(sm.movement_date) >= ?";
            $params[] = $start_date;
            $types .= "s";
        }

        if ($end_date) {
            $where_conditions[] = "DATE(sm.movement_date) <= ?";
            $params[] = $end_date;
            $types .= "s";
        }

        $query = "
            SELECT 
                sm.movement_date,
                d.name as department_name,
                r.name as recipient_name,
                CONCAT(b.name, ' - ', cm.name, ' (', s.color, ')') as product_name,
                ABS(sm.quantity_change) as quantity,
                s.cost as unit_cost,
                ABS(sm.quantity_change) * s.cost as total_cost,
                sm.reason
            FROM stock_movements sm
            JOIN stock s ON sm.stock_id = s.id
            JOIN departments d ON sm.department_id = d.id
            JOIN recipients r ON sm.recipient_id = r.id
            JOIN brands b ON s.brand_id = b.id
            JOIN consumable_models cm ON s.consumable_model_id = cm.id
            WHERE " . implode(' AND ', $where_conditions) . "
            ORDER BY sm.movement_date DESC
        ";

        $stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    private function getTotalUnits() {
        $query = "
            SELECT SUM(sm.quantity_change) as total_units
            FROM stock_movements sm
            JOIN stock s ON sm.stock_id = s.id
            WHERE s.is_active = 1
        ";
        
        $result = $this->db->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total_units'] ?? 0;
        }
        return 0;
    }

    public function exportProductsPdf() {
        // Obtener todos los productos
        $products = $this->getAllProducts();
        $total_units = $this->getTotalUnits();
        
        // Calcular estadísticas
        $total_value = 0;
        $low_stock_count = 0;
        $total_cost = 0;
        $count_with_cost = 0;
        
        foreach($products as $product) {
            $total_value += $product['total_value'];
            if($product['current_stock'] <= 5) $low_stock_count++;
            if($product['cost'] > 0) {
                $total_cost += $product['cost'];
                $count_with_cost++;
            }
        }
        
        $average_cost = $count_with_cost > 0 ? $total_cost / $count_with_cost : 0;
        
        // Preparar datos para el PDF
        $data = [
            'products' => $products,
            'stats' => [
                'total_products' => count($products),
                'total_units' => $total_units,
                'total_value' => $total_value,
                'low_stock_count' => $low_stock_count,
                'average_cost' => $average_cost
            ],
            'generated_date' => date('d/m/Y H:i:s'),
            'title' => 'Lista de Productos y Precios'
        ];
        
        // Usar la plantilla de PDF existente
        include 'views/cost_analysis/products_pdf_template.php';
    }

    public function updateCost() {
        // Verificar que sea una petición AJAX POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['product_id']) || !isset($input['new_cost'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $productId = intval($input['product_id']);
        $newCost = floatval($input['new_cost']);

        // Validaciones
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
            return;
        }

        if ($newCost < 0) {
            echo json_encode(['success' => false, 'message' => 'El costo no puede ser negativo']);
            return;
        }

        try {
            // Actualizar en la base de datos
            $query = "UPDATE stock SET cost = ? WHERE id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Error preparando la consulta: ' . $this->db->error);
            }

            $stmt->bind_param('di', $newCost, $productId);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    // Éxito
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Costo actualizado exitosamente',
                        'product_id' => $productId,
                        'new_cost' => $newCost
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado o sin cambios']);
                }
            } else {
                throw new Exception('Error ejecutando la consulta: ' . $stmt->error);
            }

            $stmt->close();

        } catch (Exception $e) {
            error_log('Error actualizando costo: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    public function exportCustomPdf() {
        try {
            // Verificar que se recibieron los datos necesarios
            if (!isset($_POST['data']) || !isset($_POST['columns']) || !isset($_POST['column_indexes'])) {
                throw new Exception('Datos insuficientes para generar el PDF');
            }

            // Decodificar los datos JSON
            $data = json_decode($_POST['data'], true);
            $columnNames = json_decode($_POST['columns'], true);
            $columnIndexes = json_decode($_POST['column_indexes'], true);

            if (!$data || !$columnNames || !$columnIndexes) {
                throw new Exception('Error al procesar los datos del reporte');
            }

            // Configurar dompdf
            require_once 'vendor/autoload.php';
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->set_option('defaultFont', 'Arial');
            $dompdf->set_option('isRemoteEnabled', true);

            // Generar el HTML del PDF
            $html = $this->generateCustomPdfHtml($data, $columnNames, $columnIndexes);
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape'); // Formato horizontal para más columnas
            $dompdf->render();

            // Generar nombre del archivo
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "Productos_Personalizado_{$timestamp}.pdf";

            // Enviar el PDF al navegador
            $dompdf->stream($filename, ["Attachment" => true]);

        } catch (Exception $e) {
            error_log('Error generando PDF personalizado: ' . $e->getMessage());
            
            // Redirigir con mensaje de error
            $_SESSION['error_message'] = 'Error al generar el PDF: ' . $e->getMessage();
            header('Location: ' . BASE_URL . 'cost-analysis/products-list');
            exit;
        }
    }

    private function generateCustomPdfHtml($data, $columnNames, $columnIndexes) {
        $fecha = date('d/m/Y');
        $hora = date('H:i:s');
        $totalProductos = count($data);
        $totalColumnas = count($columnNames);

        // Comenzar el HTML
        $html = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 20px; 
                    font-size: 10px;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 20px; 
                    border-bottom: 2px solid #2c3e50;
                    padding-bottom: 10px;
                }
                .header h1 { 
                    color: #2c3e50; 
                    margin: 0; 
                    font-size: 18px; 
                    font-weight: bold;
                }
                .header h2 { 
                    color: #34495e; 
                    margin: 5px 0; 
                    font-size: 14px; 
                    font-weight: normal;
                }
                .info-section {
                    background: #ecf0f1;
                    padding: 10px;
                    margin-bottom: 20px;
                    border-radius: 5px;
                    border: 1px solid #bdc3c7;
                }
                .info-grid {
                    display: table;
                    width: 100%;
                }
                .info-row {
                    display: table-row;
                }
                .info-cell {
                    display: table-cell;
                    padding: 5px 10px;
                    width: 25%;
                    font-weight: bold;
                    color: #2c3e50;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px;
                    font-size: 9px;
                }
                th { 
                    background-color: #3498db; 
                    color: white; 
                    font-weight: bold; 
                    padding: 8px 6px; 
                    text-align: center;
                    border: 1px solid #2980b9;
                }
                td { 
                    padding: 6px 4px; 
                    border: 1px solid #bdc3c7; 
                    text-align: left;
                }
                tr:nth-child(even) { 
                    background-color: #f8f9fa; 
                }
                .number { 
                    text-align: right; 
                    font-weight: bold;
                }
                .center { 
                    text-align: center; 
                }
                .footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 8px;
                    color: #7f8c8d;
                    border-top: 1px solid #bdc3c7;
                    padding-top: 10px;
                }
            </style>
        </head>
        <body>';

        // Header del documento
        $html .= '<div class="header">
            <h1>REPORTE PERSONALIZADO DE PRODUCTOS</h1>
            <h2>Sistema de Gestión de Stock</h2>
        </div>';

        // Información del reporte
        $html .= '<div class="info-section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">📅 Fecha de Generación: ' . $fecha . '</div>
                    <div class="info-cell">🕐 Hora: ' . $hora . '</div>
                    <div class="info-cell">📊 Total Productos: ' . $totalProductos . '</div>
                    <div class="info-cell">📋 Columnas Incluidas: ' . $totalColumnas . '</div>
                </div>
            </div>
        </div>';

        // Tabla de datos
        $html .= '<table>';
        
        // Headers
        $html .= '<thead><tr>';
        foreach ($columnNames as $columnName) {
            $html .= '<th>' . htmlspecialchars($columnName) . '</th>';
        }
        $html .= '</tr></thead>';

        // Datos
        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $index => $cell) {
                $columnIndex = $columnIndexes[$index];
                
                // Formatear según el tipo de columna
                if ($columnIndex == 5) { // Cantidad
                    $html .= '<td class="number">' . number_format((int)preg_replace('/\D/', '', $cell), 0, ',', '.') . '</td>';
                } elseif ($columnIndex == 6 || $columnIndex == 7) { // Costo y Valor Total
                    $amount = (int)preg_replace('/\D/', '', $cell);
                    $html .= '<td class="number">Gs. ' . number_format($amount, 0, ',', '.') . '</td>';
                } else {
                    $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        // Footer
        $html .= '<div class="footer">
            <p><strong>Sistema de Gestión de Stock</strong> | Reporte generado automáticamente</p>
            <p>Este reporte contiene información confidencial y está destinado únicamente para uso interno</p>
        </div>';

        $html .= '</body></html>';

        return $html;
    }
}
?>

<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once 'config/Database.php';
require_once 'models/Stock.php';
require_once 'models/Brand.php';
require_once 'models/Supplier.php';
require_once 'models/PrinterBrand.php';
require_once 'models/PrinterModel.php';
require_once 'models/ConsumableModel.php';
require_once 'models/Recipient.php';
require_once 'models/Department.php';
require_once 'BaseController.php';

class StockController extends BaseController {
    private $stockModel;
    private $brandModel;
    private $supplierModel;
    private $printerBrandModel;
    private $printerModelModel;
    private $consumableModelModel;
    private $recipientModel;
    private $departmentModel;

    public function __construct() {
        parent::__construct();
        $this->stockModel = new Stock($this->db);
        $this->brandModel = new Brand($this->db);
        $this->supplierModel = new Supplier($this->db);
        $this->printerBrandModel = new PrinterBrand($this->db);
        $this->printerModelModel = new PrinterModel($this->db);
        $this->consumableModelModel = new ConsumableModel($this->db);
        $this->recipientModel = new Recipient($this->db);
        $this->departmentModel = new Department($this->db);
    }
    
    public function generateOrderPdf($order_id) {
        $data['order_id'] = $order_id;
        $data['order_date'] = date('d/m/Y');
        $data['order_details'] = $this->stockModel->getReplenishmentOrderDetails($order_id);

        ob_start();
        require 'views/templates/pdf_template.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Solicitud_Cotizacion_Nro_' . $order_id . '.pdf', ["Attachment" => false]);
    }

    public function generateStockPdf() {
        $keyword = filter_input(INPUT_GET, 'keyword');
        $type = filter_input(INPUT_GET, 'type');
        $filters = [
            'keyword' => $keyword ? htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') : null,
            'type' => $type ? htmlspecialchars($type, ENT_QUOTES, 'UTF-8') : null,
            'supplier_id' => filter_input(INPUT_GET, 'supplier_id', FILTER_VALIDATE_INT),
            'department_id' => filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT)
        ];
        
        $data['filters'] = $filters;
        $data['generation_date'] = date('d/m/Y H:i');
        $data['stock_list'] = $this->stockModel->getAll(9999, 0, $filters);

        ob_start();
        require 'views/templates/stock_report_pdf_template.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Reporte_de_Stock_' . date('Y-m-d') . '.pdf', ["Attachment" => false]);
    }

    public function createReplenishmentOrder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['items'])) {
            $items_to_order = [];
            foreach ($_POST['items'] as $group_key => $item_data) {
                if (!empty($item_data['quantity']) && (int)$item_data['quantity'] > 0 && !empty($item_data['stock_ids'])) {
                    $stock_ids_array = explode(',', $item_data['stock_ids']);
                    $primary_stock_id = $stock_ids_array[0];
                    $items_to_order[] = [
                        'stock_id' => (int)$primary_stock_id,
                        'quantity' => (int)$item_data['quantity']
                    ];
                }
            }
    
            if (empty($items_to_order)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'No se ha especificado una cantidad para ningún artículo.'];
                header("Location: " . BASE_URL . "stock/lowStockReport");
                exit();
            }
    
            $order_id = $this->stockModel->createReplenishmentOrder();
            if ($order_id) {
                foreach ($items_to_order as $item) {
                    $this->stockModel->addReplenishmentOrderItem($order_id, $item['stock_id'], $item['quantity']);
                }
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Pedido de reposición #' . $order_id . ' creado con éxito.'];
                header("Location: " . BASE_URL . "stock/viewReplenishmentOrder/" . $order_id);
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al crear el pedido de reposición.'];
                header("Location: " . BASE_URL . "stock/lowStockReport");
            }
            exit();
        }
    }

    public function replenishmentOrders() {
        $data['orders'] = $this->stockModel->getReplenishmentOrders();
        require 'views/stock/replenishment_orders_list.php';
    }

    public function viewReplenishmentOrder($order_id) {
        $data['order_id'] = $order_id;
        $data['order_details'] = $this->stockModel->getReplenishmentOrderDetails($order_id);
        $data['order_info'] = $this->stockModel->getReplenishmentOrderById($order_id);
        require 'views/stock/view_replenishment_order.php';
    }

    public function updateReplenishmentOrder($order_id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quantities'])) {
            $all_good = true;
            foreach ($_POST['quantities'] as $item_id => $quantity) {
                $quantity = (int)$quantity;
                if ($quantity >= 0) {
                    if (!$this->stockModel->updateReplenishmentOrderItem($item_id, $quantity)) {
                        $all_good = false;
                    }
                }
            }
            if ($all_good) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Cantidades del pedido actualizadas correctamente.'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Hubo un error al actualizar una o más cantidades.'];
            }
        }
        header("Location: " . BASE_URL . "stock/viewReplenishmentOrder/" . $order_id);
        exit();
    }
    
    public function lowStockReport() {
        $filters = [
            'include_zero_stock' => isset($_GET['include_zero_stock'])
        ];
        $data['filters'] = $filters;
        $data['low_stock_items'] = $this->stockModel->getLowStockItems(5, $filters);
        require 'views/stock/low_stock_report.php';
    }

    public function report() {
        $keyword = filter_input(INPUT_GET, 'keyword');
        $type = filter_input(INPUT_GET, 'type');
        $filters = [
            'keyword' => $keyword ? htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') : null,
            'type' => $type ? htmlspecialchars($type, ENT_QUOTES, 'UTF-8') : null,
            'supplier_id' => filter_input(INPUT_GET, 'supplier_id', FILTER_VALIDATE_INT),
            'department_id' => filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT)
        ];
        
        $results_per_page = 15;
        $total_results = $this->stockModel->getTotalStockCount($filters);
        $total_pages = ceil($total_results / $results_per_page);
        
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page > $total_pages && $total_pages > 0) { $page = $total_pages; }
        if ($page < 1) { $page = 1; }
        
        $start_limit = ($page - 1) * $results_per_page;
        
        $stock = $this->stockModel->getAll($results_per_page, $start_limit, $filters);
        
        $suppliers = $this->supplierModel->getAll();
        $departments = $this->departmentModel->getAll();
        $total_item_count = $this->stockModel->getTotalItemCount();
        $filtered_item_count = $this->stockModel->getFilteredItemCount($filters);

        require 'views/stock/report.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $brand_id = filter_input(INPUT_POST, 'brand_id', FILTER_VALIDATE_INT);
            $consumable_model_id = filter_input(INPUT_POST, 'consumable_model_id', FILTER_VALIDATE_INT);
            $supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);
            $cost = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $printer_brand_id = filter_input(INPUT_POST, 'printer_brand_id', FILTER_VALIDATE_INT);
            $printer_model_id = filter_input(INPUT_POST, 'printer_model_id', FILTER_VALIDATE_INT);
            $department_ids = $_POST['department_ids'] ?? [];

            if (!$brand_id || !$consumable_model_id) {
                 $_SESSION['message'] = ['type' => 'error', 'text' => 'La marca y el modelo del consumible son obligatorios.'];
                 header("Location: " . BASE_URL . "stock/add"); exit();
            }

            $quantities = $_POST['quantity'];
            $products_added = 0;
            
            foreach ($quantities as $color => $quantity) {
                // Cambiamos la condición para que acepte 0 como una cantidad válida.
                // Solo ignoraremos los campos que estén vacíos.
                if (is_numeric($quantity) && $quantity !== '') {
                    $color_mapping = ['Negro' => 'Negro', 'Color' => 'Color', 'Negro-Toner' => 'Negro', 'Cyan' => 'Cyan', 'Magenta' => 'Magenta', 'Yellow' => 'Yellow'];
                    $final_color = isset($color_mapping[$color]) ? $color_mapping[$color] : $color;
                    
                    $this->stockModel->create(
                        $brand_id, 
                        $consumable_model_id, 
                        $supplier_id, 
                        $_POST['type'], 
                        $final_color,
                        $cost,
                        (int)$quantity, 
                        $printer_brand_id, 
                        $printer_model_id,
                        $department_ids
                    );
                    $products_added++;
                }
            }
            
            $_SESSION['message'] = $products_added > 0 ?
                ['type' => 'success', 'text' => "Se agregaron {$products_added} nuevo(s) producto(s)/color(es)."] :
                ['type' => 'error', 'text' => 'No se agregó ningún producto. Debes especificar una cantidad para al menos un artículo.'];
            header("Location: " . BASE_URL . "stock/report"); exit();
        } else {
            $data['brands'] = $this->brandModel->getAll();
            $data['suppliers'] = $this->supplierModel->getAll();
            $data['consumable_models'] = $this->consumableModelModel->getAll();
            $data['printer_brands'] = $this->printerBrandModel->getAll();
            $data['departments'] = $this->departmentModel->getAll();
            require 'views/stock/add.php';
        }
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $brand_id = filter_input(INPUT_POST, 'brand_id', FILTER_VALIDATE_INT);
            $consumable_model_id = filter_input(INPUT_POST, 'consumable_model_id', FILTER_VALIDATE_INT);
            $supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);
            $cost = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $printer_brand_id = filter_input(INPUT_POST, 'printer_brand_id', FILTER_VALIDATE_INT);
            $printer_model_id = filter_input(INPUT_POST, 'printer_model_id', FILTER_VALIDATE_INT);
            $department_ids = $_POST['department_ids'] ?? [];

            if (!$brand_id || !$consumable_model_id) {
                 $_SESSION['message'] = ['type' => 'error', 'text' => 'La marca y el modelo son obligatorios.'];
                 header("Location: " . BASE_URL . "stock/edit/" . $id);
                 exit();
            }
            $result = $this->stockModel->update(
                $id, $brand_id, $consumable_model_id, $supplier_id, $_POST['type'], $cost, $printer_brand_id, $printer_model_id, $department_ids
            );
            $_SESSION['message'] = $result ? 
                ['type' => 'success', 'text' => 'Producto actualizado con éxito.'] :
                ['type' => 'error', 'text' => 'No se pudo actualizar el producto.'];
            header("Location: " . BASE_URL . "stock/report");
            exit();
        } else {
            $data['item'] = $this->stockModel->getById($id);
            if (!$data['item']) { die("Producto no encontrado."); }

            $data['brands'] = $this->brandModel->getAll();
            $data['suppliers'] = $this->supplierModel->getAll();
            $data['consumable_models'] = $this->consumableModelModel->getAll();
            $data['printer_brands'] = $this->printerBrandModel->getAll();
            $data['departments'] = $this->departmentModel->getAll();
            $data['item_departments'] = $this->stockModel->getDepartmentsByStockId($id);

            $data['printer_models_for_brand'] = [];
            if ($data['item']['printer_brand_id']) {
                $models_result = $this->printerModelModel->getByBrandId($data['item']['printer_brand_id']);
                while($p_model = $models_result->fetch_assoc()) {
                    $data['printer_models_for_brand'][] = $p_model;
                }
            }
            
            require 'views/stock/edit.php';
        }
    }

    public function deactivate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stock_id']) && !empty(trim($_POST['reason']))) {
            $this->verifyCsrfToken();

            $stock_id = (int)$_POST['stock_id'];
            $reason = trim(htmlspecialchars($_POST['reason']));

            if ($this->stockModel->deactivate($stock_id, $reason)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto dado de baja correctamente.'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al dar de baja el producto.'];
            }
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Solicitud inválida o motivo no proporcionado.'];
        }
        header("Location: " . BASE_URL . "stock/report");
        exit();
    }

    public function issue() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
            $recipient_id = filter_input(INPUT_POST, 'recipient_id', FILTER_VALIDATE_INT);
            $department_id = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
            
            $item_name_search_raw = filter_input(INPUT_POST, 'item_name_search');
            $item_name_search = $item_name_search_raw ? htmlspecialchars($item_name_search_raw, ENT_QUOTES, 'UTF-8') : null;

            if (!$item_id || !$quantity || $quantity <= 0 || !$recipient_id || !$department_id) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Todos los campos son obligatorios y la cantidad debe ser positiva.'];
                header("Location: " . BASE_URL . "stock/issue");
                exit();
            }

            $item = $this->stockModel->getById($item_id);
            if (!$item) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'El artículo seleccionado no existe.'];
                header("Location: " . BASE_URL . "stock/issue");
                exit();
            }

            $available_stock = (int)$item['calculated_quantity'];
            if ($quantity > $available_stock) {
                $_SESSION['issue_form_state'] = [
                    'error' => "Stock insuficiente. Solo hay {$available_stock} disponibles.",
                    'data' => [
                        'item_id' => $item_id,
                        'item_name_search' => $item_name_search,
                        'quantity' => $quantity,
                        'recipient_id' => $recipient_id,
                        'department_id' => $department_id
                    ]
                ];
                header("Location: " . BASE_URL . "stock/issue");
                exit();
            }
            
            $recipient_info = $this->recipientModel->getById($recipient_id);
            $department_info = $this->departmentModel->getById($department_id);
            $recipient_name = $recipient_info ? $recipient_info['name'] : 'N/A';
            $department_name = $department_info ? $department_info['name'] : 'N/A';

            if ($this->stockModel->issue($item_id, $quantity, $recipient_id, $department_id, $recipient_name, $department_name)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Salida de stock registrada con éxito.'];
                header("Location: " . BASE_URL . "history/movements");
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Ocurrió un error al registrar la salida.'];
                header("Location: " . BASE_URL . "stock/issue");
            }
            exit();
        } else {
            $data['recipients'] = $this->recipientModel->getAll();
            $data['departments'] = $this->departmentModel->getAll();
            require 'views/stock/issue.php';
        }
    }
    
    public function replenish($replenishment_item_id = null) {
        $data['prefill'] = null;
        if ($replenishment_item_id) {
            $item_details_result = $this->stockModel->getReplenishmentOrderItemById($replenishment_item_id);
            if ($item_details_result->num_rows > 0) {
                $data['prefill'] = $item_details_result->fetch_assoc();
            }
        }
        $data['suppliers'] = $this->supplierModel->getAll();
        require 'views/stock/replenish.php';
    }

    public function doReplenish() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $replenishment_order_item_id = filter_input(INPUT_POST, 'replenishment_order_item_id', FILTER_VALIDATE_INT);
            $stock_id = filter_input(INPUT_POST, 'stock_id', FILTER_VALIDATE_INT);
            $quantity_added = filter_input(INPUT_POST, 'quantity_added', FILTER_VALIDATE_INT);
            $supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);
            $reason = trim(htmlspecialchars($_POST['reason']));

            if (!$stock_id || !$quantity_added || $quantity_added <= 0 || !$supplier_id) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Asegúrate de seleccionar un producto y rellenar todos los campos.'];
                header("Location: " . BASE_URL . "stock/replenish");
                exit();
            }
            
            $supplier_info = $this->supplierModel->getById($supplier_id);
            $detailed_reason = "Compra a proveedor: {$supplier_info['name']}. ";
            if (!empty($reason)) {
                $detailed_reason .= "Referencia: $reason";
            }

            $this->stockModel->replenish($stock_id, $quantity_added, $supplier_id, $detailed_reason, $replenishment_order_item_id);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Stock repuesto correctamente.'];
            header("Location: " . BASE_URL . "stock/report");
            exit();
        }
    }

    public function returnDefective() {
        require 'views/stock/return_defective.php';
    }
    
    public function doReturnDefective() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $stock_id = filter_input(INPUT_POST, 'stock_id', FILTER_VALIDATE_INT);
            $quantity_removed = filter_input(INPUT_POST, 'quantity_removed', FILTER_VALIDATE_INT);
            $reason = trim(htmlspecialchars($_POST['reason']));

            if (!$stock_id || !$quantity_removed || $quantity_removed <= 0 || empty($reason)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Todos los campos son obligatorios y la cantidad debe ser positiva.'];
                header("Location: " . BASE_URL . "stock/returnDefective");
                exit();
            }
            
            $item = $this->stockModel->getById($stock_id);
            if (!$item) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'El artículo seleccionado no existe.'];
                header("Location: " . BASE_URL . "stock/returnDefective");
                exit();
            }
            
            $available_stock = (int)$item['calculated_quantity'];
            if ($quantity_removed > $available_stock) {
                 $_SESSION['message'] = ['type' => 'error', 'text' => "Cantidad inválida. Quieres retirar {$quantity_removed}, pero solo hay {$available_stock} en stock."];
                header("Location: " . BASE_URL . "stock/returnDefective");
                exit();
            }

            $quantity_change = -abs($quantity_removed);
            if ($this->stockModel->addMovement($stock_id, $quantity_change, 'RETURN_DEFECTIVE', $reason)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Falla registrada y stock actualizado.'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al registrar la falla.'];
            }
            header("Location: " . BASE_URL . "stock/report");
            exit();
        }
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        // Obtener datos para el dashboard analítico
        $data = [];
        
        // KPIs básicos
        $data['total_products'] = $this->stockModel->getTotalItemCount();
        $data['low_stock_count'] = $this->getLowStockCount();
        $data['total_stock_value'] = $this->stockModel->getTotalStockValue();
        $data['zero_stock_count'] = $this->getZeroStockCount();
        
        // Datos para gráficos
        $data['consumption_by_department'] = $this->getConsumptionByDepartment();
        $data['consumption_by_type'] = $this->getConsumptionByType();
        $data['recent_movements'] = $this->getRecentMovements();
        $data['top_consumed_products'] = $this->getTopConsumedProducts();
        $data['stock_distribution'] = $this->getStockDistribution();
        
        // Datos para tabla de alertas
        $data['low_stock_items'] = $this->stockModel->getLowStockItems(5, []);
        
        require 'views/dashboard.php';
    }
    
    private function getLowStockCount() {
        $result = $this->stockModel->getLowStockItems(5, []);
        return $result->num_rows;
    }
    
    private function getZeroStockCount() {
        $query = "SELECT COUNT(*) as count FROM (
            SELECT s.id
            FROM stock s
            WHERE s.is_active = 1 
            AND COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) = 0
        ) as zero_items";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['count'];
    }
    
    private function getConsumptionByDepartment() {
        $query = "SELECT 
                    d.name as department_name,
                    COUNT(sm.id) as total_movements,
                    SUM(ABS(sm.quantity_change)) as total_quantity
                  FROM stock_movements sm
                  LEFT JOIN departments d ON sm.department_id = d.id
                  WHERE sm.movement_type = 'SALE' 
                    AND sm.movement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY d.id, d.name
                  ORDER BY total_quantity DESC
                  LIMIT 10";
        $result = $this->db->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    private function getConsumptionByType() {
        $query = "SELECT 
                    s.type,
                    SUM(ABS(sm.quantity_change)) as total_quantity
                  FROM stock_movements sm
                  JOIN stock s ON sm.stock_id = s.id
                  WHERE sm.movement_type = 'SALE' 
                    AND sm.movement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY s.type
                  ORDER BY total_quantity DESC";
        $result = $this->db->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    private function getRecentMovements() {
        $query = "SELECT 
                    DATE(sm.movement_date) as movement_date,
                    COUNT(sm.id) as total_movements,
                    SUM(CASE WHEN sm.movement_type = 'SALE' THEN ABS(sm.quantity_change) ELSE 0 END) as total_out,
                    SUM(CASE WHEN sm.movement_type = 'REPLENISHMENT' THEN sm.quantity_change ELSE 0 END) as total_in
                  FROM stock_movements sm
                  WHERE sm.movement_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
                  GROUP BY DATE(sm.movement_date)
                  ORDER BY movement_date ASC";
        $result = $this->db->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    private function getTopConsumedProducts() {
        $query = "SELECT 
                    CONCAT(b.name, ' ', cm.name, ' ', s.color) as product_name,
                    SUM(ABS(sm.quantity_change)) as total_consumed
                  FROM stock_movements sm
                  JOIN stock s ON sm.stock_id = s.id
                  LEFT JOIN brands b ON s.brand_id = b.id
                  LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
                  WHERE sm.movement_type = 'SALE' 
                    AND sm.movement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY s.id
                  ORDER BY total_consumed DESC
                  LIMIT 5";
        $result = $this->db->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    private function getStockDistribution() {
        $query = "SELECT 
                    CASE
                        WHEN calculated_quantity = 0 THEN 'Sin Stock'
                        WHEN calculated_quantity BETWEEN 1 AND 5 THEN 'Stock Bajo (1-5)'
                        WHEN calculated_quantity BETWEEN 6 AND 20 THEN 'Stock Normal (6-20)'
                        ELSE 'Stock Alto (20+)'
                    END as stock_level,
                    COUNT(*) as count
                  FROM (
                    SELECT 
                        COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as calculated_quantity
                    FROM stock s
                    WHERE s.is_active = 1
                  ) as stock_calc
                  GROUP BY stock_level
                  ORDER BY count DESC";
        $result = $this->db->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function search() {
        $results = [];
        if (isset($_GET['term']) && strlen($_GET['term']) >= 1) {
            $search_term = $_GET['term'];
            $stock_results = $this->stockModel->searchByName($search_term);
            while ($row = $stock_results->fetch_assoc()) {
                $results[] = $row;
            }
        }
        header('Content-Type: application/json');
        echo json_encode($results);
        exit();
    }
    
    private function addCategory($model, $name) {
        header('Content-Type: application/json');
        if (!empty(trim($name))) {
            $newName = trim(htmlspecialchars($name));
            $newData = $model->create($newName);
            echo json_encode($newData ? ['success' => true, 'data' => $newData] : ['success' => false, 'message' => 'El nombre ya existe o hubo un error.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'El nombre no puede estar vacío.']);
        }
        exit();
    }

    public function addBrand() {
        $this->addCategory($this->brandModel, $_POST['name']);
    }

    public function addSupplier() {
        $this->addCategory($this->supplierModel, $_POST['name']);
    }

    public function addPrinterBrand() {
        $this->addCategory($this->printerBrandModel, $_POST['name']);
    }

    public function addPrinterModel() {
        header('Content-Type: application/json');
        if (!empty(trim($_POST['name'])) && !empty($_POST['brand_id'])) {
            $name = trim(htmlspecialchars($_POST['name']));
            $brand_id = (int)$_POST['brand_id'];
            $newModel = $this->printerModelModel->create($brand_id, $name);
            echo json_encode($newModel ? ['success' => true, 'data' => $newModel] : ['success' => false, 'message' => 'El modelo ya existe para esta marca o hubo un error.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'El nombre y la marca son obligatorios.']);
        }
        exit();
    }

    public function addConsumableModel() {
        $this->addCategory($this->consumableModelModel, $_POST['name']);
    }

    public function addRecipient() {
        $this->addCategory($this->recipientModel, $_POST['name']);
    }

    public function addDepartment() {
        $this->addCategory($this->departmentModel, $_POST['name']);
    }
    
    public function getPrinterModelsByBrand($brand_id) {
        header('Content-Type: application/json');
        $models = $this->printerModelModel->getByBrandId((int)$brand_id);
        $results = [];
        while($row = $models->fetch_assoc()) { $results[] = $row; }
        echo json_encode($results);
        exit();
    }
    
    public function stockOut() {
        require_once 'models/History.php';
        $historyModel = new History($this->db);
        
        // Filtrar solo las salidas (SALE)
        $filters = [
            'movement_type' => 'SALE'
        ];
        
        // Obtener las salidas de stock
        $history = $historyModel->getAllMovements(1000, 0, $filters);
        
        require 'views/stock/stock_out.php';
    }
}
?>
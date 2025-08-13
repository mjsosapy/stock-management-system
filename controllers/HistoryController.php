<?php
require_once 'models/History.php';
require_once 'BaseController.php';

class HistoryController extends BaseController {
    private $historyModel;
    private $recipientModel;
    private $departmentModel;
    private $supplierModel;

    public function __construct() {
        parent::__construct();
        $this->historyModel = new History($this->db);
        require_once 'models/Recipient.php';
        require_once 'models/Department.php';
        require_once 'models/Supplier.php';
        $this->recipientModel = new Recipient($this->db);
        $this->departmentModel = new Department($this->db);
        $this->supplierModel = new Supplier($this->db);
    }
    
    public function movements() {
        $filters = [
            'start_date' => filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'end_date' => filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'keyword' => filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_SPECIAL_CHARS),
            'movement_type' => filter_input(INPUT_GET, 'movement_type', FILTER_SANITIZE_SPECIAL_CHARS),
            'recipient_id' => filter_input(INPUT_GET, 'recipient_id', FILTER_VALIDATE_INT),
            'department_id' => filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT),
            'supplier_id' => filter_input(INPUT_GET, 'supplier_id', FILTER_VALIDATE_INT),
        ];

        $results_per_page = 15;
        $total_results = $this->historyModel->getTotalMovementsCount($filters);
        $total_pages = ceil($total_results / $results_per_page);
        
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page > $total_pages && $total_pages > 0) { $page = $total_pages; }
        if ($page < 1) { $page = 1; }
        
        $start_limit = ($page - 1) * $results_per_page;

        $data['movements'] = $this->historyModel->getAllMovements($results_per_page, $start_limit, $filters);
        
        if ($data['movements'] === false) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error Crítico: No se pudo obtener el historial de movimientos.'];
            header("Location: " . BASE_URL . "stock/dashboard");
            exit();
        }
        
        // Data for filters and pagination
        $data['recipients'] = $this->recipientModel->getAll();
        $data['departments'] = $this->departmentModel->getAll();
        $data['suppliers'] = $this->supplierModel->getAll();
        $data['filters'] = $filters;
        $data['page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['total_change'] = $this->historyModel->getSumOfChanges($filters);

        require 'views/history/movements.php';
    }

    public function ajaxFilter() {
        $filters = [
            'start_date' => filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'end_date' => filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'keyword' => filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_SPECIAL_CHARS),
            'movement_type' => filter_input(INPUT_GET, 'movement_type', FILTER_SANITIZE_SPECIAL_CHARS),
            'recipient_id' => filter_input(INPUT_GET, 'recipient_id', FILTER_VALIDATE_INT),
            'department_id' => filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT),
            'supplier_id' => filter_input(INPUT_GET, 'supplier_id', FILTER_VALIDATE_INT),
        ];

        $results_per_page = 15;
        $total_results = $this->historyModel->getTotalMovementsCount($filters);
        $total_pages = ceil($total_results / $results_per_page);
        
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $start_limit = ($page - 1) * $results_per_page;

        $data['movements'] = $this->historyModel->getAllMovements($results_per_page, $start_limit, $filters);
        $data['page'] = $page;
        $data['total_pages'] = $total_pages;
        $data['filters'] = $filters;
        $data['total_change'] = $this->historyModel->getSumOfChanges($filters);

        ob_start();
        require 'views/history/_movements_table.php';
        $html = ob_get_clean();

        header('Content-Type: application/json');
        echo json_encode([
            'html' => $html,
            'total_change' => $data['total_change']
        ]);
    }

    public function reverse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['movement_id']) && isset($_POST['reason'])) {
            $this->verifyCsrfToken();

            $movement_id = (int)$_POST['movement_id'];
            $reason = trim(htmlspecialchars($_POST['reason']));
            
            $movement = $this->historyModel->getMovementById($movement_id);

            if ($movement) {
                if ($movement['movement_type'] == 'SALE') {
                    if ($this->historyModel->reverseSale($movement_id, $reason)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'La salida ha sido revertida con éxito.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: No se pudo revertir la salida.'];
                    }
                } elseif ($movement['movement_type'] == 'INITIAL_STOCK') {
                    if ($this->historyModel->reverseInitialStock($movement_id, $reason)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'La creación del producto ha sido revertida y el artículo fue desactivado.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: No se pudo revertir la creación del producto.'];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Este tipo de movimiento no se puede revertir.'];
                }
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Movimiento no encontrado.'];
            }
        }
        
        header("Location: " . BASE_URL . "history/movements");
        exit();
    }

    public function generatePdf() {
        $filters = [
            'start_date' => filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'end_date' => filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'keyword' => filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_SPECIAL_CHARS),
            'movement_type' => filter_input(INPUT_GET, 'movement_type', FILTER_SANITIZE_SPECIAL_CHARS),
            'recipient_id' => filter_input(INPUT_GET, 'recipient_id', FILTER_VALIDATE_INT),
            'department_id' => filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT),
            'supplier_id' => filter_input(INPUT_GET, 'supplier_id', FILTER_VALIDATE_INT),
        ];

        $data['movements'] = $this->historyModel->getAllMovements(PHP_INT_MAX, 0, $filters);
        $data['filters'] = $filters;
        $data['total_change'] = $this->historyModel->getSumOfChanges($filters);

        ob_start();
        require 'views/history/pdf_template.php';
        $html = ob_get_clean();

        require_once 'vendor/autoload.php';
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("historial_movimientos.pdf", ["Attachment" => 0]);
    }
}
?>

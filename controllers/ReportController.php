<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once 'config/Database.php';
require_once 'models/Report.php';
require_once 'models/Department.php';
require_once 'BaseController.php';

class ReportController extends BaseController {
    private $reportModel;
    private $departmentModel;

    public function __construct() {
        parent::__construct();
        $this->reportModel = new Report($this->db);
        $this->departmentModel = new Department($this->db);
    }

    public function detailed() {
        $data = [
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-t'),
            'department_id' => null,
            'report_data' => null
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['start_date'])) {
            $start_date_raw = filter_input(INPUT_GET, 'start_date');
            $end_date_raw = filter_input(INPUT_GET, 'end_date');
            
            $start_date = $start_date_raw ? htmlspecialchars($start_date_raw, ENT_QUOTES, 'UTF-8') : null;
            $end_date = $end_date_raw ? htmlspecialchars($end_date_raw, ENT_QUOTES, 'UTF-8') : null;
            $department_id = filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT);

            if ($start_date && $end_date) {
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['department_id'] = $department_id ?: null;

                $data['report_data'] = [
                    'consumption_by_department' => $this->reportModel->getConsumptionByDepartment($start_date, $end_date, $department_id),
                    'defective_returns' => $this->reportModel->getDefectiveReturns($start_date, $end_date),
                    'total_stock_value' => $this->reportModel->getTotalStockValue(),
                    'grand_total_consumption_cost' => $this->reportModel->getTotalConsumptionCost($start_date, $end_date, $department_id)
                ];
            }
        }
        
        $data['departments'] = $this->departmentModel->getAll();

        require 'views/reports/detailed_report.php';
    }

    public function generatePdf() {
        try {
            // Habilitar reporte de errores para debugging
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
            
            $start_date = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $end_date = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $department_id = filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT);

            if (!$start_date || !$end_date) {
                throw new Exception("Fechas de inicio y fin son requeridas.");
            }

            // Verificar que las fechas sean válidas
            if (!strtotime($start_date) || !strtotime($end_date)) {
                throw new Exception("Formato de fecha inválido.");
            }

            $data = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'department_name' => 'Todos',
                'report_data' => [
                    'consumption_by_department' => $this->reportModel->getConsumptionByDepartment($start_date, $end_date, $department_id),
                    'defective_returns' => $this->reportModel->getDefectiveReturns($start_date, $end_date),
                    'total_stock_value' => $this->reportModel->getTotalStockValue(),
                    'grand_total_consumption_cost' => $this->reportModel->getTotalConsumptionCost($start_date, $end_date, $department_id)
                ]
            ];

            if ($department_id) {
                $dept = $this->departmentModel->getById($department_id);
                $data['department_name'] = $dept ? $dept['name'] : 'Desconocido';
            }

            // Verificar que el template existe
            $template_path = 'views/templates/report_pdf_template.php';
            if (!file_exists($template_path)) {
                throw new Exception("Template PDF no encontrado: $template_path");
            }

            ob_start();
            require $template_path;
            $html = ob_get_clean();

            if (empty($html)) {
                throw new Exception("No se pudo generar el contenido HTML del PDF.");
            }

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Helvetica');
            $options->set('chroot', realpath('.'));
            $options->setIsPhpEnabled(true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            
            // Configurar memoria y tiempo límite
            ini_set('memory_limit', '512M');
            set_time_limit(120);
            
            $dompdf->render();
            
            // Limpiar cualquier output previo
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            $output = $dompdf->output();
            
            if (empty($output)) {
                throw new Exception("El PDF generado está vacío.");
            }
            
            // Headers para el PDF
            header('Content-Type: application/pdf');
            header('Content-Length: ' . strlen($output));
            header('Content-Disposition: inline; filename="Reporte_Detallado_' . date('Y-m-d') . '.pdf"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            
            echo $output;
            exit;
            
        } catch (Exception $e) {
            // Limpiar cualquier output
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            error_log("Error generando PDF: " . $e->getMessage());
            
            // En lugar de redireccionar, mostrar el error directamente
            header('Content-Type: text/html; charset=utf-8');
            echo "<!DOCTYPE html><html><head><title>Error PDF</title></head><body>";
            echo "<h1>Error al generar PDF</h1>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
            echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "<p><a href='" . BASE_URL . "report/detailed'>Volver al reporte</a></p>";
            echo "</body></html>";
            exit;
        }
    }
}
?>
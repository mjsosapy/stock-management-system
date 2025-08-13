<?php
class Report {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtiene el consumo de insumos y el costo total por departamento en un rango de fechas.
     */
    public function getConsumptionByDepartment($start_date, $end_date, $department_id = null) {
        $params = [$start_date, $end_date];
        $types = 'ss';

        $sql = "
            SELECT 
                sm.movement_date,
                d.name AS department_name,
                r.name AS recipient_name,
                b.name AS brand_name,
                cm.name AS model_name,
                s.color,
                s.cost,
                ABS(sm.quantity_change) AS total_quantity,
                (ABS(sm.quantity_change) * s.cost) AS total_cost
            FROM stock_movements sm
            INNER JOIN stock s ON sm.stock_id = s.id
            LEFT JOIN departments d ON sm.department_id = d.id
            LEFT JOIN recipients r ON sm.recipient_id = r.id
            LEFT JOIN brands b ON s.brand_id = b.id
            LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
            WHERE sm.movement_type = 'SALE' AND DATE(sm.movement_date) BETWEEN ? AND ?
              AND NOT EXISTS (
                  SELECT 1 
                  FROM stock_movements sm_reversal 
                  WHERE sm_reversal.reverses_movement_id = sm.id
              )
        ";

        if ($department_id) {
            $sql .= " AND sm.department_id = ?";
            $params[] = $department_id;
            $types .= 'i';
        }
        
        $sql .= "
            ORDER BY sm.movement_date DESC
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Obtiene los productos devueltos por falla en un rango de fechas.
     */
    /**
     * Calcula el costo total del consumo por departamento en un rango de fechas.
     */
    public function getTotalConsumptionCost($start_date, $end_date, $department_id = null) {
        $params = [$start_date, $end_date];
        $types = 'ss';

        $sql = "
            SELECT SUM(ABS(sm.quantity_change) * s.cost) AS grand_total_cost
            FROM stock_movements sm
            INNER JOIN stock s ON sm.stock_id = s.id
            LEFT JOIN departments d ON sm.department_id = d.id
            LEFT JOIN recipients r ON sm.recipient_id = r.id
            LEFT JOIN brands b ON s.brand_id = b.id
            LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
            WHERE sm.movement_type = 'SALE' AND DATE(sm.movement_date) BETWEEN ? AND ?
              AND NOT EXISTS (
                  SELECT 1 
                  FROM stock_movements sm_reversal 
                  WHERE sm_reversal.reverses_movement_id = sm.id
              )
        ";

        if ($department_id) {
            $sql .= " AND sm.department_id = ?";
            $params[] = $department_id;
            $types .= 'i';
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['grand_total_cost'] ?? 0;
    }

    /**
     * Obtiene los productos devueltos por falla en un rango de fechas.
     */
    public function getDefectiveReturns($start_date, $end_date) {
        $stmt = $this->conn->prepare("
            SELECT
                sm.movement_date,
                b.name AS brand_name,
                cm.name AS model_name,
                s.color,
                ABS(sm.quantity_change) AS quantity,
                sm.reason,
                sup.name as supplier_name
            FROM stock_movements sm
            JOIN stock s ON sm.stock_id = s.id
            LEFT JOIN brands b ON s.brand_id = b.id
            LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
            LEFT JOIN suppliers sup ON s.supplier_id = sup.id
            WHERE sm.movement_type = 'RETURN_DEFECTIVE'
              AND DATE(sm.movement_date) BETWEEN ? AND ?
            ORDER BY sm.movement_date DESC
        ");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Calcula el valor total del inventario actual.
     */
    public function getTotalStockValue() {
        $result = $this->conn->query("
            SELECT SUM(s.cost * COALESCE(sq.current_quantity, 0)) AS total_value
            FROM stock s
            LEFT JOIN (
                SELECT stock_id, SUM(quantity_change) as current_quantity
                FROM stock_movements
                GROUP BY stock_id
            ) AS sq ON s.id = sq.stock_id
            WHERE s.is_active = 1
        ");
        $data = $result->fetch_assoc();
        return $data['total_value'] ?? 0;
    }
}
?>
?>

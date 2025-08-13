<?php
class History {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    private function applyFilters($base_query, $filters) {
        $where_clauses = [];
        $params = [];
        $types = '';

        if (!empty($filters['start_date'])) {
            $where_clauses[] = "DATE(m.movement_date) >= ?";
            $params[] = $filters['start_date'];
            $types .= 's';
        }
        if (!empty($filters['end_date'])) {
            $where_clauses[] = "DATE(m.movement_date) <= ?";
            $params[] = $filters['end_date'];
            $types .= 's';
        }
        if (!empty($filters['keyword'])) {
            $where_clauses[] = "CONCAT(b.name, ' ', cm.name) LIKE ?";
            $params[] = '%' . $filters['keyword'] . '%';
            $types .= 's';
        }
        if (!empty($filters['movement_type'])) {
            $where_clauses[] = "m.movement_type = ?";
            $params[] = $filters['movement_type'];
            $types .= 's';
        }
        if (!empty($filters['recipient_id'])) {
            $where_clauses[] = "m.recipient_id = ?";
            $params[] = (int)$filters['recipient_id'];
            $types .= 'i';
        }
        if (!empty($filters['department_id'])) {
            $where_clauses[] = "m.department_id = ?";
            $params[] = (int)$filters['department_id'];
            $types .= 'i';
        }
        if (!empty($filters['supplier_id'])) {
            $where_clauses[] = "s.supplier_id = ?";
            $params[] = (int)$filters['supplier_id'];
            $types .= 'i';
        }

        if (count($where_clauses) > 0) {
            $base_query .= " WHERE " . implode(" AND ", $where_clauses);
        }

        return ['query' => $base_query, 'params' => $params, 'types' => $types];
    }

    public function getAllMovements($limit, $offset, $filters = []) {
        $supplier_join = !empty($filters['supplier_id']) ? "JOIN" : "LEFT JOIN";

        $base_query = "SELECT m.id, m.movement_date, b.name as brand, cm.name as model, CASE
                             WHEN LOWER(s.type) = 'tinta' AND LOWER(s.color) = 'black' THEN 'Negro'
                             WHEN LOWER(s.type) = 'tinta' AND LOWER(s.color) = 'color' THEN 'Color'
                             WHEN LOWER(s.type) = 'tóner' AND LOWER(s.color) = 'monocromatico' THEN 'Negro'
                             WHEN LOWER(s.type) = 'tóner' AND LOWER(s.color) = 'black' THEN 'Negro'
                             WHEN LOWER(s.type) = 'tóner' AND LOWER(s.color) = 'color' THEN 'Color'
                             WHEN LOWER(s.type) = 'tóner' AND LOWER(s.color) = 'cyan' THEN 'Cian'
                             WHEN LOWER(s.type) = 'tóner' AND LOWER(s.color) = 'magenta' THEN 'Magenta'
                             WHEN LOWER(s.type) = 'tóner' AND LOWER(s.color) = 'yellow' THEN 'Amarillo'
                             WHEN LOWER(s.type) = 'cinta matricial' AND LOWER(s.color) = 'black' THEN 'Negro'
                             WHEN s.color IS NULL OR s.color = '' OR s.color = '0' THEN 'Sin especificar'
                             ELSE s.color
                         END as color, m.movement_type,
                         m.quantity_change, m.reason, m.reverses_movement_id,
                         (SELECT COUNT(*) FROM stock_movements WHERE reverses_movement_id = m.id) as is_reversed,
                         r.name as recipient_name, d.name as department_name, sp.name as supplier_name
                       FROM stock_movements m
                       JOIN stock s ON m.stock_id = s.id
                       LEFT JOIN brands b ON s.brand_id = b.id
                       LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
                       LEFT JOIN recipients r ON m.recipient_id = r.id
                       LEFT JOIN departments d ON m.department_id = d.id
                       $supplier_join suppliers sp ON s.supplier_id = sp.id";
        
        $filtered_query = $this->applyFilters($base_query, $filters);
        $final_query = $filtered_query['query'] . " ORDER BY m.movement_date DESC, m.id DESC LIMIT ? OFFSET ?";
        
        $params = $filtered_query['params'];
        $types = $filtered_query['types'];
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->conn->prepare($final_query);
        if ($stmt && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function getTotalMovementsCount($filters = []) {
        $supplier_join = !empty($filters['supplier_id']) ? "JOIN" : "LEFT JOIN";
        $base_query = "SELECT COUNT(m.id) as total FROM stock_movements m JOIN stock s ON m.stock_id = s.id LEFT JOIN brands b ON s.brand_id = b.id LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id LEFT JOIN recipients r ON m.recipient_id = r.id LEFT JOIN departments d ON m.department_id = d.id $supplier_join suppliers sp ON s.supplier_id = sp.id";
        $filtered_query = $this->applyFilters($base_query, $filters);
        $stmt = $this->conn->prepare($filtered_query['query']);
        if ($stmt && !empty($filtered_query['types'])) {
            $stmt->bind_param($filtered_query['types'], ...$filtered_query['params']);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function getSumOfChanges($filters = []) {
        $supplier_join = !empty($filters['supplier_id']) ? "JOIN" : "LEFT JOIN";
        $base_query = "SELECT SUM(m.quantity_change) as total_change FROM stock_movements m JOIN stock s ON m.stock_id = s.id LEFT JOIN brands b ON s.brand_id = b.id LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id LEFT JOIN recipients r ON m.recipient_id = r.id LEFT JOIN departments d ON m.department_id = d.id $supplier_join suppliers sp ON s.supplier_id = sp.id";
        $filtered_query = $this->applyFilters($base_query, $filters);
        $stmt = $this->conn->prepare($filtered_query['query']);
        if ($stmt && !empty($filtered_query['types'])) {
            $stmt->bind_param($filtered_query['types'], ...$filtered_query['params']);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_change'] ?? 0;
    }

    public function getMovementById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM stock_movements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function reverseInitialStock($movement_id_to_reverse, $user_reason) {
        $this->conn->begin_transaction();
        try {
            $original_movement = $this->getMovementById($movement_id_to_reverse);
            if (!$original_movement || $original_movement['movement_type'] !== 'INITIAL_STOCK') {
                throw new Exception("Movimiento inicial no encontrado o no es válido.");
            }
            
            $stmt_check = $this->conn->prepare("SELECT id FROM stock_movements WHERE reverses_movement_id = ?");
            $stmt_check->bind_param("i", $movement_id_to_reverse);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) {
                throw new Exception("Este movimiento ya ha sido revertido.");
            }

            $reversal_quantity = -($original_movement['quantity_change']);
            $reversal_reason = "Anulación de creación. Motivo: " . $user_reason;
            $stmt_insert = $this->conn->prepare("INSERT INTO stock_movements (stock_id, quantity_change, movement_type, reason, reverses_movement_id) VALUES (?, ?, 'SALE_REVERSAL', ?, ?)");
            $stmt_insert->bind_param("iisi", $original_movement['stock_id'], $reversal_quantity, $reversal_reason, $movement_id_to_reverse);
            $stmt_insert->execute();
            
            $stmt_deactivate = $this->conn->prepare("UPDATE stock SET is_active = 0 WHERE id = ?");
            $stmt_deactivate->bind_param("i", $original_movement['stock_id']);
            $stmt_deactivate->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function reverseSale($movement_id_to_reverse, $user_reason) {
        $this->conn->begin_transaction();
        try {
            $original_movement = $this->getMovementById($movement_id_to_reverse);
            if (!$original_movement || $original_movement['movement_type'] !== 'SALE') { throw new Exception("Movimiento no encontrado."); }
            
            $stmt_check = $this->conn->prepare("SELECT id FROM stock_movements WHERE reverses_movement_id = ?");
            $stmt_check->bind_param("i", $movement_id_to_reverse);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) { throw new Exception("Movimiento ya revertido."); }

            $reversal_quantity = -($original_movement['quantity_change']);
            $reversal_reason = "Motivo de corrección: " . $user_reason . ". (Anula la salida registrada el " . $original_movement['movement_date'] . ")";
            $stmt_insert = $this->conn->prepare("INSERT INTO stock_movements (stock_id, quantity_change, movement_type, reason, reverses_movement_id) VALUES (?, ?, 'SALE_REVERSAL', ?, ?)");
            $stmt_insert->bind_param("iisi", $original_movement['stock_id'], $reversal_quantity, $reversal_reason, $movement_id_to_reverse);
            $stmt_insert->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) { $this->conn->rollback(); return false; }
    }
}
?>
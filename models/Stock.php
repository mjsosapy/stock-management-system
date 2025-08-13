<?php
class Stock {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    // --- MÉTODOS PARA PEDIDOS DE REPOSICIÓN (sin cambios) ---
    public function createReplenishmentOrder() {
        $this->conn->query("INSERT INTO replenishment_orders (status) VALUES ('PENDIENTE')");
        return $this->conn->insert_id;
    }

    public function addReplenishmentOrderItem($order_id, $stock_id, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO replenishment_order_items (order_id, stock_id, quantity_requested) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $order_id, $stock_id, $quantity);
        return $stmt->execute();
    }

    public function getReplenishmentOrders() {
        return $this->conn->query("SELECT * FROM replenishment_orders ORDER BY order_date DESC");
    }

    public function getReplenishmentOrderDetails($order_id) {
        $stmt = $this->conn->prepare("
            SELECT
                roi.id as replenishment_item_id,
                roi.quantity_requested,
                s.type,
                b.name as brand_name,
                cm.name as model_name,
                CASE
                    WHEN s.color = 'Negro' THEN 'Negro'
                    WHEN s.color = 'Color' THEN 'Color'
                    WHEN s.color = 'Cyan' THEN 'Cian'
                    WHEN s.color = 'Magenta' THEN 'Magenta'
                    WHEN s.color = 'Yellow' THEN 'Amarillo'
                    ELSE s.color
                END as color_display_name,
                COALESCE((
                    SELECT SUM(ABS(sm.quantity_change)) 
                    FROM stock_movements sm 
                    WHERE sm.replenishment_order_item_id = roi.id 
                    AND sm.movement_type = 'REPLENISHMENT'
                ), 0) as quantity_received
            FROM replenishment_order_items roi
            JOIN stock s ON roi.stock_id = s.id
            LEFT JOIN brands b ON s.brand_id = b.id
            LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
            WHERE roi.order_id = ?
            ORDER BY cm.name ASC
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getReplenishmentOrderById($order_id) {
        $stmt = $this->conn->prepare("SELECT * FROM replenishment_orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getReplenishmentOrderItemById($item_id) {
        $stmt = $this->conn->prepare("SELECT roi.*, b.name as brand_name, cm.name as model_name, s.color FROM replenishment_order_items roi JOIN stock s ON roi.stock_id = s.id LEFT JOIN brands b ON s.brand_id = b.id LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id WHERE roi.id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function updateReplenishmentOrderItem($item_id, $quantity) {
        $stmt = $this->conn->prepare("UPDATE replenishment_order_items SET quantity_requested = ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $item_id);
        return $stmt->execute();
    }
    
    // --- MÉTODOS PRINCIPALES ---
    
    // --- INICIO DE LA MODIFICACIÓN: create() ahora acepta $cost ---
    public function create($brand_id, $consumable_model_id, $supplier_id, $type, $color, $cost, $initial_quantity, $printer_brand_id, $printer_model_id, $department_ids) {
        $this->conn->begin_transaction();
        try {
            if (empty($color) || $color === '0') {
                $color = ($type === 'Tinta') ? 'Negro' : 'Negro';
            }
            
            $stmt = $this->conn->prepare("INSERT INTO stock (brand_id, consumable_model_id, supplier_id, type, color, cost, printer_brand_id, printer_model_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiissdii", $brand_id, $consumable_model_id, $supplier_id, $type, $color, $cost, $printer_brand_id, $printer_model_id);
            $stmt->execute();
            $new_stock_id = $this->conn->insert_id;

            $this->updateDepartments($new_stock_id, $department_ids);
            
            if ($initial_quantity > 0) {
                $this->addMovement($new_stock_id, $initial_quantity, 'INITIAL_STOCK', 'Creación de nuevo producto');
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    // --- FIN DE LA MODIFICACIÓN ---

    // --- INICIO DE LA MODIFICACIÓN: update() ahora acepta $cost ---
    public function update($id, $brand_id, $consumable_model_id, $supplier_id, $type, $cost, $printer_brand_id, $printer_model_id, $department_ids) {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE stock SET brand_id = ?, consumable_model_id = ?, supplier_id = ?, type = ?, cost = ?, printer_brand_id = ?, printer_model_id = ? WHERE id = ?");
            $stmt->bind_param("iiisdiii", $brand_id, $consumable_model_id, $supplier_id, $type, $cost, $printer_brand_id, $printer_model_id, $id);
            $stmt->execute();

            $this->updateDepartments($id, $department_ids);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    // --- FIN DE LA MODIFICACIÓN ---

    public function getLowStockItems($threshold = 5, $filters = []) {
        $query = "
            SELECT
                GROUP_CONCAT(s.id) as stock_ids,
                s.model_name,
                s.type,
                s.color_display_name,
                SUM(s.calculated_quantity) as calculated_quantity,
                GROUP_CONCAT(DISTINCT s.departments SEPARATOR ', ') as departments
            FROM (
                SELECT
                    st.id,
                    cm.name as model_name,
                    st.type,
                    COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = st.id), 0) as calculated_quantity,
                    (SELECT GROUP_CONCAT(DISTINCT d.name SEPARATOR ', ')
                     FROM stock_departments sd
                     JOIN departments d ON sd.department_id = d.id
                     WHERE sd.stock_id = st.id) as departments,
                    CASE
                        WHEN st.color = 'Negro' THEN 'Negro'
                        WHEN st.color = 'Color' THEN 'Color'
                        WHEN st.color = 'Cyan' THEN 'Cian'
                        WHEN st.color = 'Magenta' THEN 'Magenta'
                        WHEN st.color = 'Yellow' THEN 'Amarillo'
                        ELSE st.color
                    END as color_display_name
                FROM stock st
                LEFT JOIN consumable_models cm ON st.consumable_model_id = cm.id
                WHERE st.is_active = 1
            ) as s
            GROUP BY s.model_name, s.color_display_name, s.type
        ";

        $having_clauses = ["calculated_quantity <= ?"];
        $params = [$threshold];
        $types = 'i';

        if (empty($filters['include_zero_stock'])) {
            $having_clauses[] = "calculated_quantity > 0";
        }

        $query .= " HAVING " . implode(" AND ", $having_clauses);
        $query .= " ORDER BY calculated_quantity ASC, s.model_name, s.color_display_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTotalItemCount() {
        $query = "SELECT SUM(sm.quantity_change) as grand_total
                  FROM stock_movements sm
                  JOIN stock s ON sm.stock_id = s.id
                  WHERE s.is_active = 1";
        $result = $this->conn->query($query);
        $data = $result->fetch_assoc();
        return $data['grand_total'] ?? 0;
    }

    public function getTotalStockValue() {
        $query = "SELECT SUM(
                    COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) * s.cost
                  ) as total_value
                  FROM stock s
                  WHERE s.is_active = 1";
        $result = $this->conn->query($query);
        $data = $result->fetch_assoc();
        return $data['total_value'] ?? 0;
    }

    private function applyFilters($base_query, $filters) {
        $params = [];
        $types = '';
        
        $where_clauses = ["s.is_active = 1"];

        if (!empty($filters['keyword'])) {
            $where_clauses[] = "CONCAT(b.name, ' ', cm.name) LIKE ?";
            $params[] = '%' . $filters['keyword'] . '%';
            $types .= 's';
        }
        if (!empty($filters['type'])) {
            $where_clauses[] = "s.type = ?";
            $params[] = $filters['type'];
            $types .= 's';
        }
        if (!empty($filters['supplier_id'])) {
            $where_clauses[] = "s.supplier_id = ?";
            $params[] = (int)$filters['supplier_id'];
            $types .= 'i';
        }
        if (!empty($filters['department_id'])) {
            $where_clauses[] = "s.id IN (SELECT stock_id FROM stock_departments WHERE department_id = ?)";
            $params[] = (int)$filters['department_id'];
            $types .= 'i';
        }
        
        // Nuevo filtro para excluir productos con stock cero por defecto
        if (empty($filters['include_zero_stock'])) {
            $where_clauses[] = "COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) > 0";
        }

        if (count($where_clauses) > 0) {
            $base_query .= " WHERE " . implode(" AND ", $where_clauses);
        }

        return ['query' => $base_query, 'params' => $params, 'types' => $types];
    }
    
    public function getFilteredItemCount($filters = []) {
        $base_query = "SELECT SUM(cq.calculated_quantity) as filtered_total
                       FROM (
                           SELECT COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as calculated_quantity
                           FROM stock s
                           LEFT JOIN brands b ON s.brand_id = b.id
                           LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id";
        
        $filtered_query = $this->applyFilters($base_query, $filters);
        
        $final_query = $filtered_query['query'] . ") as cq";

        $stmt = $this->conn->prepare($final_query);
        if ($stmt && !empty($filtered_query['types'])) {
            $stmt->bind_param($filtered_query['types'], ...$filtered_query['params']);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['filtered_total'] ?? 0;
    }

    public function getAll($limit, $offset, $filters = []) {
        $base_query = "SELECT s.*, b.name as brand_name, cm.name as model_name, sup.name as supplier_name,
                         pb.name as printer_brand_name, pm.name as printer_model_name,
                         COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as calculated_quantity,
                         (SELECT GROUP_CONCAT(d.name SEPARATOR ', ') FROM stock_departments sd JOIN departments d ON sd.department_id = d.id WHERE sd.stock_id = s.id) as departments,
                         cm.name as display_model_name,
                         CASE
                             WHEN s.type = 'Tinta' AND s.color = 'Negro' THEN 'Negro'
                             WHEN s.type = 'Tinta' AND s.color = 'Color' THEN 'Color'
                             WHEN s.type = 'Tóner' AND s.color = 'Negro' THEN 'Negro'
                             WHEN s.type = 'Tóner' AND s.color = 'Cyan' THEN 'Cian'
                             WHEN s.type = 'Tóner' AND s.color = 'Magenta' THEN 'Magenta'
                             WHEN s.type = 'Tóner' AND s.color = 'Yellow' THEN 'Amarillo'
                             WHEN s.color IS NULL OR s.color = '' OR s.color = '0' THEN 'Sin especificar'
                             ELSE s.color
                         END as color_display_name
                  FROM stock s
                  LEFT JOIN brands b ON s.brand_id = b.id
                  LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
                  LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                  LEFT JOIN printer_brands pb ON s.printer_brand_id = pb.id
                  LEFT JOIN printer_models pm ON s.printer_model_id = pm.id";
        
        $filtered_query = $this->applyFilters($base_query, $filters);
        
        $final_query = $filtered_query['query'] . " ORDER BY cm.name ASC, b.name ASC, s.color ASC LIMIT ? OFFSET ?";
        
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
    
    public function getTotalStockCount($filters = []) {
         $base_query = "SELECT COUNT(s.id) as total
                  FROM stock s
                  LEFT JOIN brands b ON s.brand_id = b.id
                  LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id";

        $filtered_query = $this->applyFilters($base_query, $filters);

        $stmt = $this->conn->prepare($filtered_query['query']);
        if ($stmt && !empty($filtered_query['types'])) {
            $stmt->bind_param($filtered_query['types'], ...$filtered_query['params']);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchByName($term) {
        $search_term = "%" . $term . "%";
        $stmt = $this->conn->prepare("
            SELECT s.id, b.name as brand, cm.name as model_name,
                   CASE
                       WHEN s.type = 'Tinta' AND s.color = 'Negro' THEN 'Negro'
                       WHEN s.type = 'Tinta' AND s.color = 'Color' THEN 'Color'
                       WHEN s.type = 'Tóner' AND s.color = 'Negro' THEN 'Negro'
                       WHEN s.type = 'Tóner' AND s.color = 'Cyan' THEN 'Cian'
                       WHEN s.type = 'Tóner' AND s.color = 'Magenta' THEN 'Magenta'
                       WHEN s.type = 'Tóner' AND s.color = 'Yellow' THEN 'Amarillo'
                       WHEN s.color IS NULL OR s.color = '' OR s.color = '0' THEN ''
                       ELSE s.color
                   END as color,
                   COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as calculated_quantity
            FROM stock s
            LEFT JOIN brands b ON s.brand_id = b.id
            LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
            WHERE CONCAT(b.name, ' ', cm.name, IFNULL(CONCAT(' - ',
                CASE
                    WHEN s.type = 'Tinta' AND s.color = 'Negro' THEN 'Negro'
                    WHEN s.type = 'Tinta' AND s.color = 'Color' THEN 'Color'
                    WHEN s.type = 'Tóner' AND s.color = 'Negro' THEN 'Negro'
                    WHEN s.type = 'Tóner' AND s.color = 'Cyan' THEN 'Cian'
                    WHEN s.type = 'Tóner' AND s.color = 'Magenta' THEN 'Magenta'
                    WHEN s.type = 'Tóner' AND s.color = 'Yellow' THEN 'Amarillo'
                    ELSE s.color
                END), '')) LIKE ? AND s.is_active = 1
            LIMIT 10");
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT s.*, b.name as brand_name, cm.name as model_name, sup.name as supplier_name,
                   pb.name as printer_brand_name, pm.name as printer_model_name,
                   COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as calculated_quantity,
                   CASE
                       WHEN s.type = 'Tinta' AND s.color = 'Negro' THEN 'Negro'
                       WHEN s.type = 'Tinta' AND s.color = 'Color' THEN 'Color'
                       WHEN s.type = 'Tóner' AND s.color = 'Negro' THEN 'Negro'
                       WHEN s.type = 'Tóner' AND s.color = 'Cyan' THEN 'Cian'
                       WHEN s.type = 'Tóner' AND s.color = 'Magenta' THEN 'Magenta'
                       WHEN s.type = 'Tóner' AND s.color = 'Yellow' THEN 'Amarillo'
                       WHEN s.color IS NULL OR s.color = '' OR s.color = '0' THEN 'Sin especificar'
                       ELSE s.color
                   END as color_display_name
            FROM stock s
            LEFT JOIN brands b ON s.brand_id = b.id
            LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
            LEFT JOIN suppliers sup ON s.supplier_id = sup.id
            LEFT JOIN printer_brands pb ON s.printer_brand_id = pb.id
            LEFT JOIN printer_models pm ON s.printer_model_id = pm.id
            WHERE s.id = ? AND s.is_active = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getDepartmentsByStockId($stock_id) {
        $stmt = $this->conn->prepare("SELECT department_id FROM stock_departments WHERE stock_id = ?");
        $stmt->bind_param("i", $stock_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $department_ids = [];
        while ($row = $result->fetch_assoc()) {
            $department_ids[] = $row['department_id'];
        }
        return $department_ids;
    }

    private function updateDepartments($stock_id, $department_ids) {
        $stmt_delete = $this->conn->prepare("DELETE FROM stock_departments WHERE stock_id = ?");
        $stmt_delete->bind_param("i", $stock_id);
        $stmt_delete->execute();

        if (!empty($department_ids)) {
            $stmt_insert = $this->conn->prepare("INSERT INTO stock_departments (stock_id, department_id) VALUES (?, ?)");
            foreach ($department_ids as $department_id) {
                $stmt_insert->bind_param("ii", $stock_id, $department_id);
                $stmt_insert->execute();
            }
        }
    }

    public function addMovement($stock_id, $quantity_change, $movement_type, $reason = null, $replenishment_order_item_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO stock_movements (stock_id, quantity_change, movement_type, reason, replenishment_order_item_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $stock_id, $quantity_change, $movement_type, $reason, $replenishment_order_item_id);
        return $stmt->execute();
    }

    public function replenish($stock_id, $quantity_added, $supplier_id, $reason, $replenishment_order_item_id = null) {
        $this->conn->begin_transaction();
        try {
            $this->addMovement($stock_id, $quantity_added, 'REPLENISHMENT', $reason, $replenishment_order_item_id);
            $stmt = $this->conn->prepare("UPDATE stock SET supplier_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $supplier_id, $stock_id);
            $stmt->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function deactivate($id, $reason) {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE stock SET is_active = 0 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $this->addMovement($id, 0, 'DEACTIVATED', $reason);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    public function issue($itemId, $quantity, $recipient_id, $department_id, $recipient_name, $department_name) {
        $quantity_change = -abs($quantity);
        $reason = "Entregado a: $recipient_name (Dpto: $department_name)";
        
        $stmt = $this->conn->prepare("INSERT INTO stock_movements (stock_id, quantity_change, movement_type, reason, recipient_id, department_id) VALUES (?, ?, 'SALE', ?, ?, ?)");
        $stmt->bind_param("iisii", $itemId, $quantity_change, $reason, $recipient_id, $department_id);
        return $stmt->execute();
    }
}
?>
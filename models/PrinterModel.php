<?php
class PrinterModel {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function getByBrandId($brand_id) {
        $stmt = $this->conn->prepare("SELECT * FROM printer_models WHERE printer_brand_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function create($brand_id, $name) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO printer_models (printer_brand_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $brand_id, $name);
            if ($stmt->execute()) { return ['id' => $this->conn->insert_id, 'name' => $name]; }
            return false;
        } catch (Exception $e) { return false; }
    }
}
?>
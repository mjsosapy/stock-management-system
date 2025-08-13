<?php
class PrinterBrand {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function getAll() { return $this->conn->query("SELECT * FROM printer_brands ORDER BY name ASC"); }
    public function create($name) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO printer_brands (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) { return ['id' => $this->conn->insert_id, 'name' => $name]; }
            return false;
        } catch (Exception $e) { return false; }
    }
}
?>
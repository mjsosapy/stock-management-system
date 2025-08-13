<?php
class ConsumableModel {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function getAll() { return $this->conn->query("SELECT * FROM consumable_models ORDER BY name ASC"); }
    public function create($name) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO consumable_models (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) { return ['id' => $this->conn->insert_id, 'name' => $name]; }
            return false;
        } catch (Exception $e) { return false; }
    }
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM consumable_models WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
<?php
class Department {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function getAll() { return $this->conn->query("SELECT * FROM departments ORDER BY name ASC"); }
    
    // Método para obtener un departamento por su ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($name) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO departments (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) { return ['id' => $this->conn->insert_id, 'name' => $name]; }
            return false;
        } catch (Exception $e) { return false; }
    }
}
?>
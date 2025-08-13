<?php
// config/Database.php

class Database {
    private $host = "localhost";
    private $db_name = "stock_manager";
    private $username = "root";
    private $password = "";
    private static $instance = null;
    public $conn;

    private function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            $this->conn->set_charset("utf8mb4");
            
            // Configurar el modo estricto de SQL
            $this->conn->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
            
            // Configurar la zona horaria
            $this->conn->query("SET time_zone = '-03:00'");
        } catch (Exception $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        try {
            if ($this->conn === null || !$this->conn->query('SELECT 1')) {
                $this->__construct();
            }
        } catch (\Exception $e) {
            $this->__construct();
        }
        return $this->conn;
    }

    // Prevenir la clonación del objeto
    private function __clone() {}

    // Prevenir la deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
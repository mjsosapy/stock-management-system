<?php
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "DESCRIBE users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Estructura de la tabla 'users':\n";
    foreach ($columns as $column) {
        echo "{$column['Field']} - {$column['Type']}\n";
    }
} catch (PDOException $e) {
    if ($e->getCode() == "42S02") {
        echo "La tabla 'users' no existe.\n";
        
        // Crear la tabla users
        $createTable = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles(id)
        )";
        
        try {
            $conn->exec($createTable);
            echo "Tabla 'users' creada exitosamente.\n";
            
            // Crear usuario admin por defecto
            $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
            $insertAdmin = "INSERT INTO users (name, email, password, role_id) VALUES ('Administrator', 'admin@example.com', :password, 1)";
            $stmt = $conn->prepare($insertAdmin);
            $stmt->bindParam(':password', $adminPass);
            $stmt->execute();
            echo "Usuario administrador creado exitosamente.\n";
            
        } catch (PDOException $createError) {
            echo "Error al crear la tabla: " . $createError->getMessage() . "\n";
            
            if (strpos($createError->getMessage(), "roles") !== false) {
                echo "Creando tabla roles primero...\n";
                $createRoles = "CREATE TABLE roles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(50) NOT NULL UNIQUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                
                try {
                    $conn->exec($createRoles);
                    echo "Tabla 'roles' creada exitosamente.\n";
                    
                    // Insertar roles básicos
                    $insertRoles = "INSERT INTO roles (name) VALUES 
                        ('admin'),
                        ('gestor'),
                        ('operador')";
                    $conn->exec($insertRoles);
                    echo "Roles básicos creados exitosamente.\n";
                    
                    // Intentar crear la tabla users nuevamente
                    $conn->exec($createTable);
                    echo "Tabla 'users' creada exitosamente.\n";
                    
                    // Crear usuario admin
                    $stmt = $conn->prepare($insertAdmin);
                    $stmt->bindParam(':password', $adminPass);
                    $stmt->execute();
                    echo "Usuario administrador creado exitosamente.\n";
                    
                } catch (PDOException $roleError) {
                    echo "Error al crear la tabla roles: " . $roleError->getMessage() . "\n";
                }
            }
        }
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>

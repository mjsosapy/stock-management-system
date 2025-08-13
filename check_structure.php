<?php
require_once 'config/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== ESTRUCTURA DE LA TABLA STOCK ===\n";
$result = $conn->query('DESCRIBE stock');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== DATOS DE EJEMPLO ===\n";
$result = $conn->query('SELECT id, cost FROM stock LIMIT 5');
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - Cost: " . $row['cost'] . "\n";
}
?>

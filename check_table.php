<?php
require_once 'config/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== ESTRUCTURA DE stock_movements ===\n";
$result = $conn->query('DESCRIBE stock_movements');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== DATOS DE EJEMPLO ===\n";
$result = $conn->query('SELECT * FROM stock_movements LIMIT 3');
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Stock ID: " . $row['stock_id'] . ", Quantity Change: " . $row['quantity_change'] . "\n";
    }
} else {
    echo "No hay datos en stock_movements\n";
}
?>

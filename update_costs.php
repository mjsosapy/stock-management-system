<?php
require_once 'config/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "Actualizando costos de productos...\n";

// Primero veamos qué tablas tenemos
echo "\n--- TABLAS DISPONIBLES ---\n";
$tables_result = $conn->query('SHOW TABLES');
while($row = $tables_result->fetch_array()) {
    echo "• " . $row[0] . "\n";
}

// Actualizar costos aleatorios para los productos existentes
$update_query = "
    UPDATE stock 
    SET cost = CASE 
        WHEN type = 'Tóner' THEN ROUND(RAND() * 450000 + 50000, -3)
        WHEN type = 'Tinta' THEN ROUND(RAND() * 180000 + 20000, -3)
        ELSE ROUND(RAND() * 100000 + 10000, -3)
    END
    WHERE cost = 0 OR cost IS NULL
";

$result = $conn->query($update_query);

if ($result) {
    $affected = $conn->affected_rows;
    echo "✅ Se actualizaron los costos de $affected productos.\n";
} else {
    echo "❌ Error al actualizar costos: " . $conn->error . "\n";
}

// Mostrar algunos ejemplos con la estructura real
echo "\n--- EJEMPLOS DE COSTOS ACTUALIZADOS ---\n";
$sample_query = "
    SELECT 
        s.id,
        s.type,
        s.color,
        s.cost
    FROM stock s
    WHERE s.cost > 0
    ORDER BY s.cost DESC
    LIMIT 10
";

$result = $conn->query($sample_query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "• ID: " . $row['id'] . " - " . $row['type'] . " (" . $row['color'] . ") - Gs. " . number_format($row['cost'], 0, ',', '.') . "\n";
    }
}

echo "\n✅ Actualización de costos completada.\n";
?>

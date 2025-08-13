<?php
require_once 'config/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "=== ANÁLISIS DE PRODUCTOS CON STOCK 0 ===\n";

// Primero, veamos cuántos productos tienen stock 0
$check_query = "
    SELECT 
        s.id,
        CONCAT(b.name, ' - ', cm.name, ' (', s.color, ')') as product_name,
        s.type,
        COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) as current_stock
    FROM stock s
    LEFT JOIN brands b ON s.brand_id = b.id
    LEFT JOIN consumable_models cm ON s.consumable_model_id = cm.id
    WHERE s.is_active = 1
    HAVING current_stock = 0
    ORDER BY s.id
";

$result = $conn->query($check_query);
$products_to_delete = [];

if ($result && $result->num_rows > 0) {
    echo "📦 Productos con stock 0 encontrados:\n\n";
    while ($row = $result->fetch_assoc()) {
        $products_to_delete[] = $row['id'];
        echo "• ID: " . $row['id'] . " - " . $row['product_name'] . " (" . $row['type'] . ")\n";
    }
    
    echo "\n💥 TOTAL: " . count($products_to_delete) . " productos con stock 0\n";
    
    if (!empty($products_to_delete)) {
        echo "\n⚠️  INICIANDO ELIMINACIÓN...\n";
        
        // Desactivar restricciones de claves foráneas temporalmente
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        $deleted_count = 0;
        $error_count = 0;
        
        foreach ($products_to_delete as $product_id) {
            try {
                // Primero eliminar movimientos de stock relacionados
                $delete_movements = "DELETE FROM stock_movements WHERE stock_id = ?";
                $stmt = $conn->prepare($delete_movements);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                
                // Eliminar relaciones con departamentos
                $delete_dept_relations = "DELETE FROM stock_departments WHERE stock_id = ?";
                $stmt = $conn->prepare($delete_dept_relations);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                
                // Finalmente eliminar el producto
                $delete_product = "DELETE FROM stock WHERE id = ?";
                $stmt = $conn->prepare($delete_product);
                $stmt->bind_param("i", $product_id);
                
                if ($stmt->execute()) {
                    $deleted_count++;
                    echo "✅ Eliminado producto ID: $product_id\n";
                } else {
                    $error_count++;
                    echo "❌ Error eliminando producto ID: $product_id - " . $conn->error . "\n";
                }
                
            } catch (Exception $e) {
                $error_count++;
                echo "❌ Excepción eliminando producto ID: $product_id - " . $e->getMessage() . "\n";
            }
        }
        
        // Reactivar restricciones de claves foráneas
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        
        echo "\n📊 RESUMEN DE ELIMINACIÓN:\n";
        echo "✅ Productos eliminados: $deleted_count\n";
        echo "❌ Errores: $error_count\n";
        
        if ($deleted_count > 0) {
            echo "\n🎉 ¡Eliminación completada exitosamente!\n";
        }
        
    } else {
        echo "\n✅ No hay productos con stock 0 para eliminar.\n";
    }
    
} else {
    echo "✅ No se encontraron productos con stock 0.\n";
}

// Verificar el estado final
echo "\n=== VERIFICACIÓN FINAL ===\n";
$final_check = "SELECT COUNT(*) as total_products FROM stock WHERE is_active = 1";
$result = $conn->query($final_check);
if ($result) {
    $row = $result->fetch_assoc();
    echo "📦 Total de productos activos restantes: " . $row['total_products'] . "\n";
}

$zero_stock_check = "
    SELECT COUNT(*) as zero_stock_count
    FROM stock s
    WHERE s.is_active = 1
    AND COALESCE((SELECT SUM(quantity_change) FROM stock_movements WHERE stock_id = s.id), 0) = 0
";
$result = $conn->query($zero_stock_check);
if ($result) {
    $row = $result->fetch_assoc();
    echo "⚠️  Productos con stock 0 restantes: " . $row['zero_stock_count'] . "\n";
}

echo "\n✅ Proceso completado.\n";
?>

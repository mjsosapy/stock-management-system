<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar el entorno de prueba
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    echo "1. Iniciando test de PDF...\n";
    
    // Verificar que las clases existan
    if (!class_exists('Dompdf\Dompdf')) {
        throw new Exception("Dompdf no está disponible");
    }
    echo "2. Dompdf está disponible\n";
    
    // Crear un HTML simple
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Test PDF</title>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { color: #333; }
        </style>
    </head>
    <body>
        <h1>Test de PDF</h1>
        <p>Si puedes ver esto, el PDF se generó correctamente.</p>
        <p>Fecha: ' . date('Y-m-d H:i:s') . '</p>
    </body>
    </html>';
    
    echo "3. HTML creado\n";
    
    // Configurar opciones
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    
    echo "4. Opciones configuradas\n";
    
    // Crear instancia de Dompdf
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    
    echo "5. Dompdf configurado\n";
    
    // Renderizar
    $dompdf->render();
    
    echo "6. PDF renderizado exitosamente\n";
    
    // Enviar al navegador
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="test.pdf"');
    
    echo $dompdf->output();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>

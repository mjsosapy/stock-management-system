<?php
/**
 * -------------------------------------------------------------------------
 * PUNTO DE ENTRADA Y ENRUTADOR PRINCIPAL DE LA APLICACIÓN
 * -------------------------------------------------------------------------
 */

// --- PASO 1: CONFIGURACIÓN DE SEGURIDAD Y OPTIMIZACIÓN ---

// Configuración del entorno
$isProduction = false; // Cambiar a true en producción

// Configuración de errores basada en el entorno
if ($isProduction) {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

// Configuración de seguridad
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', $isProduction);
ini_set('session.cookie_samesite', 'Strict');

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
if ($isProduction) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// --- CORRECCIÓN CLAVE: DEFINIR LA RUTA RAÍZ DE LA APLICACIÓN ---
// Esto nos da una ruta absoluta y confiable para incluir archivos.
define('APP_ROOT', __DIR__);

// Definir la URL base para construir enlaces en las vistas
define('BASE_URL', '/stock-app/');

// Cargar el autoloader de Composer
require_once APP_ROOT . '/vendor/autoload.php';

// --- PASO 2: LÓGICA DE ENRUTAMIENTO ---
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'stock/dashboard';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

$controllerName = !empty($urlParts[0]) ? strtolower($urlParts[0]) : 'stock';
$actionName = !empty($urlParts[1]) ? strtolower($urlParts[1]) : 'dashboard';
$params = array_slice($urlParts, 2);

// --- PASO 3: CARGAR Y EJECUTAR EL CONTROLADOR ---

// Mapeo especial para nombres con guiones
$controllerMappings = [
    'cost-analysis' => 'CostAnalysis'
];

// Determinar el nombre de archivo del controlador
if (isset($controllerMappings[$controllerName])) {
    $controllerFileName = $controllerMappings[$controllerName] . 'Controller.php';
    $controllerClassName = $controllerMappings[$controllerName] . 'Controller';
} else {
    $controllerFileName = ucfirst($controllerName) . 'Controller.php';
    $controllerClassName = ucfirst($controllerName) . 'Controller';
}

$controllerFile = APP_ROOT . '/controllers/' . $controllerFileName;

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerClassName)) {
        $controllerInstance = new $controllerClassName();

        // Mapeo de acciones con guiones
        $actionMappings = [
            'detailed-report' => 'detailedReport',
            'export-pdf' => 'exportPDF',
            'products-list' => 'productsList',
            'export-products-pdf' => 'exportProductsPdf',
            'export-custom-pdf' => 'exportCustomPdf',
            'update-cost' => 'updateCost'
        ];
        
        $methodName = isset($actionMappings[$actionName]) ? $actionMappings[$actionName] : $actionName;

        if (method_exists($controllerInstance, $methodName)) {
            call_user_func_array([$controllerInstance, $methodName], $params);
        } else {
            die("Error 404: La acción '{$actionName}' no existe en el controlador '{$controllerClassName}'.");
        }
    } else {
        die("Error Crítico: La clase '{$controllerClassName}' no se encontró en el archivo '{$controllerFile}'.");
    }
} else {
    die("Error 404: El controlador para '{$controllerName}' no fue encontrado en '{$controllerFile}'.");
}
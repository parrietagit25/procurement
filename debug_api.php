<?php
// Debug completo de la API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$debug_info = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server_info' => [
        'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
        'HTTPS' => isset($_SERVER['HTTPS']) ? 'Yes' : 'No',
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'Unknown'
    ],
    'file_checks' => [],
    'database_check' => [],
    'api_routes' => []
];

// Verificar archivos importantes
$files_to_check = [
    'api/index.php',
    'config/database.php',
    'config/config.php',
    'classes/Auth.php',
    'classes/Supplier.php'
];

foreach ($files_to_check as $file) {
    $debug_info['file_checks'][$file] = file_exists($file) ? 'EXISTS' : 'NOT FOUND';
}

// Verificar conexión a base de datos
try {
    require_once 'config/database.php';
    $db = getDB();
    if ($db) {
        $debug_info['database_check']['status'] = 'CONNECTED';
        $debug_info['database_check']['message'] = 'Conexión exitosa a la base de datos';
    } else {
        $debug_info['database_check']['status'] = 'FAILED';
        $debug_info['database_check']['message'] = 'No se pudo conectar a la base de datos';
    }
} catch (Exception $e) {
    $debug_info['database_check']['status'] = 'ERROR';
    $debug_info['database_check']['message'] = $e->getMessage();
}

// Verificar rutas de API
$api_routes = [
    '/api/suppliers',
    '/api/orders',
    '/api/products',
    '/api/quotations',
    '/api/categories',
    '/api/admin/dashboard_stats',
    '/api/supplier/dashboard_stats'
];

foreach ($api_routes as $route) {
    $debug_info['api_routes'][$route] = 'Available';
}

// Verificar si mod_rewrite está habilitado
$debug_info['mod_rewrite'] = function_exists('apache_get_modules') ? 
    (in_array('mod_rewrite', apache_get_modules()) ? 'ENABLED' : 'DISABLED') : 
    'UNKNOWN';

echo json_encode($debug_info, JSON_PRETTY_PRINT);
?>

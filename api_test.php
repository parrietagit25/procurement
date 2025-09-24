<?php
// Test específico para la API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Simular la estructura de la API
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Eliminar la ruta base si existe
$basePath = '/procurement/api';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

echo json_encode([
    'success' => true,
    'message' => 'API Test funcionando',
    'debug_info' => [
        'original_path' => $_SERVER['REQUEST_URI'],
        'parsed_path' => $path,
        'method' => $method,
        'base_path' => $basePath,
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
        'https' => isset($_SERVER['HTTPS']) ? 'Yes' : 'No'
    ],
    'available_endpoints' => [
        '/suppliers',
        '/orders',
        '/products',
        '/quotations',
        '/categories',
        '/admin/dashboard_stats',
        '/supplier/dashboard_stats'
    ]
]);
?>

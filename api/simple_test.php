<?php
// Test simple de la API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Eliminar la ruta base si existe
$basePath = '/procurement/api';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Router simple
switch($path) {
    case '/suppliers':
        if($method === 'GET') {
            echo json_encode([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'company_name' => 'Proveedor de Prueba',
                        'contact_name' => 'Juan Pérez',
                        'email' => 'juan@proveedor.com',
                        'phone' => '555-1234',
                        'status' => 'approved',
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ]
            ]);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
        break;
        
    case '/orders':
        if($method === 'GET') {
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Endpoint no encontrado',
            'path' => $path,
            'method' => $method
        ]);
        break;
}
?>

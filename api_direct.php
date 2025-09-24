<?php
// Test directo de la API sin .htaccess
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Habilitar logging de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

try {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/classes/Auth.php';
    require_once __DIR__ . '/classes/User.php';
    require_once __DIR__ . '/classes/Supplier.php';
    require_once __DIR__ . '/classes/PurchaseOrder.php';
    require_once __DIR__ . '/classes/Product.php';
    require_once __DIR__ . '/classes/Category.php';

    $db = getDB();
    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'Error de conexión a la base de datos']);
        exit;
    }
    
    $auth = new Auth($db);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al inicializar la aplicación: ' . $e->getMessage()]);
    exit;
}

// Obtener método y ruta
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

// Debug: mostrar la ruta que se está procesando
error_log("API Direct Path: " . $path);

// Router simple de la API
try {
    switch($path) {
        case '/api_direct.php':
        case '/api_direct.php/suppliers':
            if($method === 'GET') {
                // Simular datos de proveedores
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
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Endpoint no encontrado',
                'path' => $path,
                'method' => $method
            ]);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>

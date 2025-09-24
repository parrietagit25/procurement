<?php
// Router específico para la API
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Establecer headers para la API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Debug: mostrar información de la ruta
error_log("API Router Debug - REQUEST_URI: " . $request_uri);
error_log("API Router Debug - Parsed Path: " . $path);

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

error_log("API Router Debug - After removing base: " . $path);

// Verificar que sea una ruta de API
if (strpos($path, '/api/') !== 0) {
    http_response_code(404);
    echo json_encode(['error' => 'No es una ruta de API válida', 'path' => $path]);
    exit;
}

// Obtener la ruta relativa de la API (sin /api/)
$api_path = substr($path, 5); // Remover '/api/'
error_log("API Router Debug - API Path: " . $api_path);

// Inicializar la base de datos y clases
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

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

// Debug: mostrar la ruta que se está procesando
error_log("API Router - Processing API path: " . $api_path);

// Router de la API
try {
    // Manejar rutas con parámetros
    $path_parts = explode('/', $api_path);
    $endpoint = $path_parts[0];
    $param = isset($path_parts[1]) ? $path_parts[1] : null;
    
    switch($endpoint) {
        case 'suppliers':
            if($method === 'GET') {
                if($param) {
                    // GET /api/suppliers/{id}
                    $_GET['id'] = $param;
                    include 'api/endpoints/suppliers/get.php';
                } else {
                    // GET /api/suppliers
                    include 'api/endpoints/suppliers/list.php';
                }
            } elseif($method === 'POST') {
                include 'api/endpoints/suppliers/create.php';
            } elseif($method === 'PUT') {
                // PUT /api/suppliers/{id}
                if($param) {
                    $_GET['id'] = $param;
                    include 'api/endpoints/suppliers/update.php';
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requerido para actualización']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case 'orders':
            if($method === 'GET') {
                if($param) {
                    // GET /api/orders/{id}
                    $_GET['id'] = $param;
                    include 'api/endpoints/orders/get.php';
                } else {
                    // GET /api/orders
                    include 'api/endpoints/orders/list.php';
                }
            } elseif($method === 'POST') {
                if($param === 'suppliers') {
                    // POST /api/orders/{id}/suppliers
                    $_GET['id'] = $path_parts[0]; // El ID de la orden está en la primera parte
                    include 'api/endpoints/orders/add_suppliers.php';
                } else {
                    include 'api/endpoints/orders/create.php';
                }
            } elseif($method === 'PUT') {
                // PUT /api/orders/{id}
                if($param) {
                    $_GET['id'] = $param;
                    include 'api/endpoints/orders/update.php';
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requerido para actualización']);
                }
            } elseif($method === 'DELETE') {
                // DELETE /api/orders/{id}/suppliers/{supplier_id}
                if($param === 'suppliers' && isset($path_parts[2])) {
                    $_GET['id'] = $path_parts[0];
                    $_GET['supplier_id'] = $path_parts[2];
                    include 'api/endpoints/orders/remove_supplier.php';
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Ruta de eliminación no válida']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case 'products':
            if($method === 'GET') {
                if($param) {
                    // GET /api/products/{id}
                    $_GET['id'] = $param;
                    include 'api/endpoints/products/get.php';
                } else {
                    // GET /api/products
                    include 'api/endpoints/products/list.php';
                }
            } elseif($method === 'POST') {
                include 'api/endpoints/products/create.php';
            } elseif($method === 'PUT') {
                // PUT /api/products/{id}
                if($param) {
                    $_GET['id'] = $param;
                    include 'api/endpoints/products/update.php';
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requerido para actualización']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case 'categories':
            if($method === 'GET') {
                include 'api/endpoints/categories/list.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case 'admin':
            if($param === 'dashboard_stats' && $method === 'GET') {
                include 'api/endpoints/admin/dashboard_stats.php';
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint no encontrado', 'path' => $api_path]);
            }
            break;
            
        case 'quotations':
            if($method === 'GET') {
                if($param) {
                    // GET /api/quotations/{id}
                    $_GET['id'] = $param;
                    include 'api/endpoints/quotations/get.php';
                } else {
                    // GET /api/quotations
                    include 'api/endpoints/quotations/list.php';
                }
            } elseif($method === 'POST') {
                include 'api/endpoints/quotations/create.php';
            } elseif($method === 'PUT') {
                // PUT /api/quotations/{id}
                if($param) {
                    $_GET['id'] = $param;
                    include 'api/endpoints/quotations/update.php';
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID requerido para actualización']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado', 'path' => $api_path]);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>
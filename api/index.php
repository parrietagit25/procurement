<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Supplier.php';
require_once __DIR__ . '/../classes/PurchaseOrder.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

$db = getDB();
$auth = new Auth($db);

// Obtener método y ruta
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Eliminar la ruta base si existe
$basePath = '/procurement/api';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Obtener datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

// Debug: mostrar la ruta que se está procesando
error_log("API Path: " . $path);

// Router simple de la API
try {
    switch($path) {
        case '/auth/login':
            if($method === 'POST') {
                if(!isset($input['username']) || !isset($input['password'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Username y password son requeridos']);
                    exit;
                }

                $username = $input['username'];
                $password = $input['password'];

                // Intentar autenticación como usuario interno
                $result = $auth->loginUser($username, $password);

                if(!$result['success']) {
                    // Si falla como usuario interno, intentar como proveedor
                    $result = $auth->loginSupplier($username, $password);
                }

                if($result['success']) {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(401);
                    echo json_encode($result);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case '/auth/logout':
            if($method === 'POST') {
                $result = $auth->logout();
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Usuarios
        case '/users':
            if($method === 'GET') {
                include 'endpoints/users/list.php';
            } elseif($method === 'POST') {
                include 'endpoints/users/create.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/users\/(\d+)$/', $path, $matches) ? true : false):
            $user_id = $matches[1];
            if($method === 'GET') {
                include 'endpoints/users/get.php';
            } elseif($method === 'PUT') {
                include 'endpoints/users/update.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Proveedores
        case '/suppliers':
            if($method === 'GET') {
                include 'endpoints/suppliers/list.php';
            } elseif($method === 'POST') {
                include 'endpoints/suppliers/create.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/suppliers\/(\d+)$/', $path, $matches) ? true : false):
            $supplier_id = $matches[1];
            if($method === 'GET') {
                include 'endpoints/suppliers/get.php';
            } elseif($method === 'PUT') {
                include 'endpoints/suppliers/update.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/suppliers\/(\d+)\/approve$/', $path, $matches) ? true : false):
            $supplier_id = $matches[1];
            if($method === 'POST') {
                include 'endpoints/suppliers/approve.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Órdenes de compra
        case '/orders':
            if($method === 'GET') {
                include 'endpoints/orders/list.php';
            } elseif($method === 'POST') {
                include 'endpoints/orders/create.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/orders\/(\d+)$/', $path, $matches) ? true : false):
            $order_id = $matches[1];
            if($method === 'GET') {
                include 'endpoints/orders/get.php';
            } elseif($method === 'PUT') {
                include 'endpoints/orders/update.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/orders\/(\d+)\/suppliers$/', $path, $matches) ? true : false):
            $order_id = $matches[1];
            if($method === 'POST') {
                include 'endpoints/orders/add_suppliers.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/orders\/(\d+)\/suppliers\/(\d+)$/', $path, $matches) ? true : false):
            $order_id = $matches[1];
            $supplier_id = $matches[2];
            if($method === 'DELETE') {
                $GLOBALS['order_id'] = $order_id;
                $GLOBALS['supplier_id'] = $supplier_id;
                include 'endpoints/orders/remove_supplier.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Productos
        case '/products':
            if($method === 'GET') {
                include 'endpoints/products/list.php';
            } elseif($method === 'POST') {
                include 'endpoints/products/create.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/products\/(\d+)$/', $path, $matches) ? true : false):
            $product_id = $matches[1];
            if($method === 'GET') {
                include 'endpoints/products/get.php';
            } elseif($method === 'PUT') {
                include 'endpoints/products/update.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Cotizaciones
        case '/quotations':
            if($method === 'GET') {
                include 'endpoints/quotations/list.php';
            } elseif($method === 'POST') {
                include 'endpoints/quotations/create.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        case (preg_match('/^\/quotations\/(\d+)$/', $path, $matches) ? true : false):
            $quotation_id = $matches[1];
            if($method === 'GET') {
                include 'endpoints/quotations/get.php';
            } elseif($method === 'PUT') {
                include 'endpoints/quotations/update.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Categorías
        case '/categories':
            if($method === 'GET') {
                include 'endpoints/categories/list.php';
            } elseif($method === 'POST') {
                include 'endpoints/categories/create.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Proveedor - Órdenes
        case '/supplier/orders':
            if($method === 'GET') {
                include 'endpoints/supplier/orders.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Proveedor - Cotizaciones
        case '/supplier/quotations':
            if($method === 'GET') {
                include 'endpoints/supplier/quotations.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Proveedor - Enviar Cotización
        case '/supplier/submit_quotation':
            if($method === 'POST') {
                include 'endpoints/supplier/submit_quotation.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Proveedor - Estadísticas del Dashboard
        case '/supplier/dashboard_stats':
            if($method === 'GET') {
                include 'endpoints/supplier/dashboard_stats.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Admin - Estadísticas del Dashboard
        case '/admin/dashboard_stats':
            if($method === 'GET') {
                include 'endpoints/admin/dashboard_stats.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        // Dashboard
        case '/dashboard/stats':
            if($method === 'GET') {
                include 'endpoints/dashboard/stats.php';
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado']);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}
?>
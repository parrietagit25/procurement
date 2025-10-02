<?php
// Router principal del sistema de procurement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Debug: mostrar información de la ruta
error_log("Router Debug - REQUEST_URI: " . $request_uri);
error_log("Router Debug - Parsed Path: " . $path);

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

error_log("Router Debug - After removing base: " . $path);

// Si la ruta comienza con /api/, manejar la API directamente
if (strpos($path, '/api/') === 0) {
    error_log("Router Debug - API route detected: " . $path);
    
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
    
    // Habilitar logging de errores para la API
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
    error_log("API Path: " . $path);

    // Router simple de la API
    try {
        switch($path) {
            case '/api/suppliers':
                if($method === 'GET') {
                    include 'api/endpoints/suppliers/list.php';
                } elseif($method === 'POST') {
                    include 'api/endpoints/suppliers/create.php';
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método no permitido']);
                }
                break;
                
            case '/api/orders':
                if($method === 'GET') {
                    include 'api/endpoints/orders/list.php';
                } elseif($method === 'POST') {
                    include 'api/endpoints/orders/create.php';
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método no permitido']);
                }
                break;
                
            case '/api/products':
                if($method === 'GET') {
                    include 'api/endpoints/products/list.php';
                } elseif($method === 'POST') {
                    include 'api/endpoints/products/create.php';
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método no permitido']);
                }
                break;
                
            case '/api/categories':
                if($method === 'GET') {
                    include 'api/endpoints/categories/list.php';
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método no permitido']);
                }
                break;
                
            case '/api/admin/dashboard_stats':
                if($method === 'GET') {
                    include 'api/endpoints/admin/dashboard_stats.php';
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método no permitido']);
                }
                break;
                
            default:
                // Manejar rutas dinámicas de productos
                if(preg_match('/^\/api\/products\/(\d+)$/', $path, $matches)) {
                    $product_id = $matches[1];
                    if($method === 'GET') {
                        include 'api/endpoints/products/get.php';
                    } elseif($method === 'PUT') {
                        include 'api/endpoints/products/update.php';
                    } elseif($method === 'DELETE') {
                        include 'api/endpoints/products/delete.php';
                    } else {
                        http_response_code(405);
                        echo json_encode(['error' => 'Método no permitido']);
                    }
                    break;
                }
                
                if(preg_match('/^\/api\/products\/(\d+)\/toggle-status$/', $path, $matches)) {
                    $product_id = $matches[1];
                    if($method === 'PUT') {
                        include 'api/endpoints/products/toggle_status.php';
                    } else {
                        http_response_code(405);
                        echo json_encode(['error' => 'Método no permitido']);
                    }
                    break;
                }
                
                // Si no coincide con ninguna ruta
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint no encontrado', 'path' => $path]);
                break;
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
    exit;
}

// Si es la página principal, mostrar el login
if ($path === '/' || $path === '') {
    include 'views/login.php';
    exit;
}

// Para otras rutas, intentar incluir el archivo correspondiente
$file_path = ltrim($path, '/');
if (file_exists($file_path) && is_file($file_path)) {
    include $file_path;
    exit;
}

// Si no se encuentra el archivo, mostrar error 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1>404</h1>
                <h2>Página no encontrada</h2>
                <p>La página que buscas no existe.</p>
                <p><strong>Ruta solicitada:</strong> <?php echo htmlspecialchars($path); ?></p>
                <a href="/" class="btn btn-primary">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
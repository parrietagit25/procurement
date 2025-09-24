<?php
// Router específico para la API
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Si la ruta comienza con /api/, procesar la API
if (strpos($path, '/api/') === 0) {
    // Simular la ruta de la API para el archivo api/index.php
    $_SERVER['REQUEST_URI'] = $path;
    
    // Incluir el archivo de la API
    ob_start();
    include 'api/index.php';
    $output = ob_get_clean();
    
    echo $output;
    exit;
}

// Si no es una ruta de API, devolver error
http_response_code(404);
echo json_encode(['error' => 'Endpoint no encontrado', 'path' => $path]);
?>

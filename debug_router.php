<?php
// Debug del router principal
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$debug_info = [
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'path_info' => $_SERVER['PATH_INFO'] ?? 'Unknown',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
    'query_string' => $_SERVER['QUERY_STRING'] ?? 'Unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Unknown',
    'parsed_path' => parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH),
    'base_path_check' => []
];

// Verificar la lógica del router
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

$debug_info['base_path_check']['original_path'] = $path;
$debug_info['base_path_check']['starts_with_procurement'] = strpos($path, '/procurement') === 0;

// Eliminar la ruta base si existe
$basePath = '/procurement';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

$debug_info['base_path_check']['after_removing_base'] = $path;
$debug_info['base_path_check']['starts_with_api'] = strpos($path, '/api/') === 0;

// Verificar si el archivo api/index.php existe
$debug_info['api_file_exists'] = file_exists('api/index.php');
$debug_info['api_file_readable'] = is_readable('api/index.php');

echo json_encode($debug_info, JSON_PRETTY_PRINT);
?>
